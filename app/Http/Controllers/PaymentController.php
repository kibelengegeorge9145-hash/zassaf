<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\GuestBookPurchaseMail;
use App\Models\Payment;
use App\Models\SandboxPayment;
use App\Services\Payments\Contracts\PaymentServiceInterface;
use App\Services\Payments\Exceptions\PaymentCallbackException;
use App\Services\Payments\PaymentSignatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function sandboxShow(string $providerReference)
    {
        $ledger = SandboxPayment::where('provider_reference', $providerReference)->firstOrFail();

        return view('payment.sandbox', compact('ledger'));
    }

    public function sandboxConfirm(Request $request, string $providerReference)
    {
        $ledger = SandboxPayment::where('provider_reference', $providerReference)->firstOrFail();

        $payment = Payment::where('transaction_reference', $ledger->transaction_reference)->first();

        if (! $payment) {
            abort(404, 'Unknown payment.');
        }

        $ledger->update(['status' => SandboxPayment::STATUS_PAID]);

        return redirect()->route('payment.callback', [
            'transaction_reference' => $payment->transaction_reference,
            'provider_reference' => $ledger->provider_reference,
        ]);
    }

    public function sandboxCancel(Request $request, string $providerReference)
    {
        $ledger = SandboxPayment::where('provider_reference', $providerReference)->firstOrFail();

        $payment = Payment::where('transaction_reference', $ledger->transaction_reference)->first();

        if ($payment?->isPending()) {
            $payment->update(['status' => Payment::STATUS_CANCELLED, 'failure_reason' => 'Cancelled by user.']);
        }

        $ledger->update(['status' => SandboxPayment::STATUS_FAILED]);

        if ($payment?->payment_type === Payment::TYPE_BOOK) {
            return redirect()->route('books.show', $payment->book ?? $payment)
                ->with('error', __('books.messages.payment_failed'));
        }

        return redirect()->route('member.payments.index')
            ->with('error', __('membership.messages.payment_failed'));
    }

    public function callback(Request $request)
    {
        $service = app(PaymentServiceInterface::class);

        try {
            $payment = $service->handleProviderCallback($request->query());
        } catch (PaymentCallbackException $e) {
            Log::warning('Payment callback rejected: '.$e->getMessage());

            return view('payment.result', ['success' => false, 'message' => $e->getMessage()]);
        }

        if ($payment->isPaid()) {
            if ($payment->payment_type === Payment::TYPE_BOOK) {
                if ($payment->isGuestBookPayment()) {
                    $this->sendGuestBookConfirmation($payment);

                    return redirect()->route('guest.payment.success', $payment)
                        ->with('success', __('books.messages.purchase_success'));
                }

                return redirect()->route('member.library')
                    ->with('success', __('books.messages.purchase_success'));
            }

            return redirect()->route('member.payments.show', $payment)
                ->with('success', __('membership.messages.payment_success'));
        }

        if ($payment->payment_type === Payment::TYPE_BOOK) {
            if ($payment->isGuestBookPayment()) {
                return redirect()->route('books.show', $payment->book ?? $payment)
                    ->with('error', __('books.messages.payment_failed'));
            }

            return redirect()->route('books.show', $payment->book ?? $payment)
                ->with('error', __('books.messages.payment_failed'));
        }

        return redirect()->route('member.payments.show', $payment)
            ->with('error', __('membership.messages.payment_failed'));
    }

    public function guestSuccess(Request $request, Payment $payment)
    {
        abort_unless($payment->isGuestBookPayment(), 404);

        if (! $payment->isPaid()) {
            return view('payment.result', [
                'success' => false,
                'message' => __('books.messages.payment_failed'),
            ]);
        }

        $token = session()->get("guest_download_token_{$payment->id}")
            ?? Cache::get("guest_download_token_{$payment->id}");

        $emailConfigured = $this->mailConfigured();

        return view('payment.guest-success', compact('payment', 'token', 'emailConfigured'));
    }

    protected function sendGuestBookConfirmation(Payment $payment): void
    {
        if (! $this->mailConfigured()) {
            return;
        }

        $token = session()->get("guest_download_token_{$payment->id}")
            ?? Cache::get("guest_download_token_{$payment->id}");

        if (! $token) {
            return;
        }

        try {
            Mail::to($payment->customer_email)->send(
                new GuestBookPurchaseMail($payment, route('guest.download', $token))
            );
        } catch (\Throwable $e) {
            Log::warning('Guest book confirmation email could not be sent: '.$e->getMessage());
        }
    }

    protected function mailConfigured(): bool
    {
        return ! in_array(config('mail.default'), ['log', 'array'], true);
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->json()->all();

        $signature = $request->header('X-Payment-Signature');

        if (! app(PaymentSignatureService::class)->verify($payload, $signature)) {
            Log::warning('Payment webhook rejected: invalid signature.');

            return response()->json(['status' => 'rejected'], 403);
        }

        try {
            $payment = app(PaymentServiceInterface::class)->handleProviderCallback($payload);
        } catch (PaymentCallbackException $e) {
            Log::warning('Payment webhook rejected: '.$e->getMessage());

            return response()->json(['status' => 'rejected', 'error' => $e->getMessage()], 404);
        }

        return response()->json([
            'status' => 'ok',
            'payment' => $payment->transaction_reference,
            'payment_status' => $payment->status,
        ]);
    }
}
