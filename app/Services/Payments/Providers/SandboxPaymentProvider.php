<?php

namespace App\Services\Payments\Providers;

use App\Models\Payment;
use App\Models\SandboxPayment;
use App\Services\Payments\Contracts\PaymentProviderInterface;
use App\Services\Payments\PaymentInitiation;
use App\Services\Payments\PaymentVerificationResult;
use Illuminate\Support\Str;

/**
 * Development / sandbox payment provider.
 *
 * This is a mock provider that mirrors a real gateway's ledger: it stores a
 * transaction record, exposes a hosted confirmation page, and reports the
 * payment state back to the verification service. No payment is ever marked
 * paid without going through provider verification. It is only active when
 * config("payments.sandbox.enabled") is true and must never be used in
 * production.
 */
class SandboxPaymentProvider implements PaymentProviderInterface
{
    public function name(): string
    {
        return 'sandbox';
    }

    public function createTransaction(Payment $payment, array $payload = []): PaymentInitiation
    {
        $reference = 'SB-'.strtoupper(Str::random(18));

        SandboxPayment::create([
            'provider_reference' => $reference,
            'transaction_reference' => $payment->transaction_reference,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'status' => SandboxPayment::STATUS_PENDING,
        ]);

        return new PaymentInitiation(
            providerReference: $reference,
            checkoutUrl: route('payment.sandbox.show', $reference),
        );
    }

    public function verify(Payment $payment, array $payload = []): PaymentVerificationResult
    {
        $reference = $payload['provider_reference'] ?? $payment->provider_reference;

        $ledger = SandboxPayment::where('provider_reference', $reference)->first();

        if (! $ledger) {
            return new PaymentVerificationResult(
                paid: false,
                providerReference: $reference,
                failureReason: 'Sandbox transaction not found.',
            );
        }

        return new PaymentVerificationResult(
            paid: $ledger->status === SandboxPayment::STATUS_PAID,
            providerReference: $ledger->provider_reference,
            failureReason: $ledger->status === SandboxPayment::STATUS_FAILED
                ? 'Sandbox payment declined by the simulated gateway.'
                : null,
        );
    }

    public function supportsMethod(string $method): bool
    {
        return in_array($method, array_keys(Payment::METHODS), true);
    }
}
