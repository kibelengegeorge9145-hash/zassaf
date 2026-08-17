<?php

namespace App\Services\Payments;

readonly class PaymentInitiation
{
    public function __construct(
        public string $providerReference,
        public ?string $checkoutUrl = null,
        public array $meta = [],
    ) {
    }
}
