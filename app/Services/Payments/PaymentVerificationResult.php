<?php

namespace App\Services\Payments;

readonly class PaymentVerificationResult
{
    public function __construct(
        public bool $paid,
        public ?string $providerReference = null,
        public ?string $failureReason = null,
        public array $meta = [],
    ) {
    }
}
