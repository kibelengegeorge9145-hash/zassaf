<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Log;

/**
 * Verifies provider notification signatures using the shared secret.
 */
class PaymentSignatureService
{
    public function sign(array $payload): string
    {
        $secret = (string) config('payments.secret');

        return hash_hmac('sha256', json_encode($payload), $secret);
    }

    public function verify(array $payload, ?string $signature): bool
    {
        if (blank($signature) || blank(config('payments.secret'))) {
            Log::warning('Payment notification received without a valid signature.');

            return false;
        }

        $expected = $this->sign($payload);

        return hash_equals($expected, $signature);
    }
}
