<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payments\Exceptions\PaymentException;
use App\Services\Payments\PaymentTransactionService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $member = $request->user()->member;

        $query = $member->payments()->latest();

        if ($request->filled('type')) {
            $query->where('payment_type', $request->query('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $payments = $query->paginate(20)->withQueryString();

        return view('member.payments.index', compact('payments'));
    }

    public function show(Request $request, Payment $payment)
    {
        abort_unless($payment->member_id === $request->user()->member?->id, 403);

        return view('member.payments.show', compact('payment'));
    }

    public function create(Request $request)
    {
        $member = $request->user()->member;

        return view('member.payments.create', compact('member'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string'],
            'payment_method' => ['required', 'string'],
        ]);

        $member = $request->user()->member;

        $method = $validated['payment_method'];

        try {
            if ($validated['type'] === Payment::TYPE_REGISTRATION) {
                $payment = app(PaymentTransactionService::class)->createRegistrationPayment($member, $method);
            } elseif ($validated['type'] === Payment::TYPE_MONTHLY) {
                $payment = app(PaymentTransactionService::class)->createMonthlyPayment($member, $method);
            } else {
                throw new PaymentException('Invalid payment type.');
            }
        } catch (PaymentException $e) {
            return back()->with('error', $e->getMessage());
        }

        $initiation = app(\App\Services\Payments\Contracts\PaymentServiceInterface::class)->initiate($payment, $method);

        return redirect()->away($initiation->checkoutUrl);
    }
}
