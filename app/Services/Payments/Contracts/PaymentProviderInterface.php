<?php

namespace App\Services\Payments\Contracts;

use App\Models\Payment;
use App\Services\Payments\PaymentInitiation;
use App\Services\Payments\PaymentVerificationResult;

interface PaymentProviderInterface
{
    /**
     * Provider identifier used in configuration (e.g. "sandbox", "azampay").
     */
    public function name(): string;

    /**
     * Initiate a transaction at the provider and return a reference plus any
     * checkout URL the user should be redirected to.
     */
    public function createTransaction(Payment $payment, array $payload = []): PaymentInitiation;

    /**
     * Verify the state of a payment with the provider. The returned result is
     * the only thing allowed to mark a payment as paid.
     */
    public function verify(Payment $payment, array $payload = []): PaymentVerificationResult;

    /**
     * Whether the provider can process the given payment method.
     */
    public function supportsMethod(string $method): bool;
}
