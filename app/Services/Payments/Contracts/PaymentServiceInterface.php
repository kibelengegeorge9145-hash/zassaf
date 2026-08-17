<?php

namespace App\Services\Payments\Contracts;

use App\Models\Payment;
use App\Services\Payments\PaymentInitiation;

interface PaymentServiceInterface
{
    /**
     * Initiate a payment at the active provider.
     */
    public function initiate(Payment $payment, string $method): PaymentInitiation;

    /**
     * Handle a provider notification (callback or webhook). Idempotent — a
     * payment can only ever be settled once.
     */
    public function handleProviderCallback(array $payload): Payment;
}
