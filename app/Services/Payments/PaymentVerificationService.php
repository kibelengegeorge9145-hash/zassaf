<?php

namespace App\Services\Payments;

use App\Models\Member;
use App\Models\Payment;
use App\Services\MembershipService;
use App\Services\Payments\Contracts\PaymentProviderInterface;
use App\Services\Payments\Contracts\PaymentServiceInterface;

/**
 * Verifies a payment with the provider and settles the outcome. Settlement is
 * idempotent: a payment that is already PAID is never processed twice and can
 * never create duplicate memberships or transactions.
 */
class PaymentVerificationService implements PaymentServiceInterface
{
    public function __construct(
        protected PaymentManager $manager,
        protected MembershipService $membershipService,
    ) {
    }

    public function initiate(Payment $payment, string $method): PaymentInitiation
    {
        return $this->provider()->createTransaction($payment, [
            'amount' => $payment->amount,
            'method' => $payment->payment_method,
            'callback_url' => url(config('payments.callback_url')),
        ]);
    }

    public function handleProviderCallback(array $payload): Payment
    {
        $reference = $payload['transaction_reference']
            ?? $payload['provider_reference']
            ?? null;

        if (! $reference) {
            throw new PaymentCallbackException('Missing payment reference in notification.');
        }

        $payment = Payment::where('transaction_reference', $reference)
            ->orWhere('provider_reference', $reference)
            ->first();

        if (! $payment) {
            throw new PaymentCallbackException('Unknown payment reference ['.$reference.'].');
        }

        return $this->settle($payment, $payload);
    }

    public function settle(Payment $payment, array $payload = []): Payment
    {
        // Idempotency guard — do not settle an already settled payment.
        if ($payment->isPaid() || $payment->status === Payment::STATUS_REFUNDED) {
            return $payment;
        }

        if ($payment->status !== Payment::STATUS_PENDING) {
            $payment->update(['status' => Payment::STATUS_PROCESSING]);
        }

        $result = $this->provider()->verify($payment, $payload);

        if ($result->paid) {
            $payment->update([
                'status' => Payment::STATUS_PAID,
                'provider_reference' => $result->providerReference ?? $payment->provider_reference,
                'paid_at' => now(),
                'failure_reason' => null,
            ]);

            if ($payment->payment_type === Payment::TYPE_BOOK) {
                $this->settleBookPurchase($payment);
            } else {
                $this->settleMembership($payment);
            }

            $this->notifiable($payment)?->notify(new \App\Notifications\PaymentSucceeded($payment->fresh()));
        } else {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'failure_reason' => $result->failureReason,
            ]);

            $this->notifiable($payment)?->notify(new \App\Notifications\PaymentFailed($payment->fresh()));
        }

        return $payment->fresh();
    }

    protected function settleMembership(Payment $payment): void
    {
        $member = $payment->member;

        if ($payment->payment_type === Payment::TYPE_REGISTRATION) {
            $this->membershipService->activateForRegistration($member, $payment);
        } else {
            $this->membershipService->extendForMonthly($member, $payment);
        }
    }

    protected function settleBookPurchase(Payment $payment): void
    {
        $book = $payment->book;

        if (! $book) {
            return;
        }

        $service = app(\App\Services\BookPurchaseService::class);

        if ($payment->user_id) {
            if ($payment->user) {
                $service->purchase($payment->user, $book, $payment);
            }

            return;
        }

        if ($payment->customer_email) {
            $service->purchaseForGuest($payment, $book);
        }
    }

    protected function notifiable(Payment $payment)
    {
        return $payment->user ?? $payment->member?->user;
    }

    protected function provider(): PaymentProviderInterface
    {
        return $this->manager->driver();
    }
}
