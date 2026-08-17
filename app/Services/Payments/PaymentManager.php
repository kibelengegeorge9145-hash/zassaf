<?php

namespace App\Services\Payments;

use App\Services\Payments\Contracts\PaymentProviderInterface;
use Illuminate\Support\Manager;

/**
 * Resolves the active payment provider from configuration. Providers are
 * registered here and selected via config("payments.provider").
 */
class PaymentManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return config('payments.provider', 'sandbox');
    }

    protected function createSandboxDriver(): PaymentProviderInterface
    {
        return new Providers\SandboxPaymentProvider();
    }

    /**
     * Entry point for connecting a real Tanzanian gateway later:
     *
     *     protected function createAzampayDriver(): PaymentProviderInterface
     *     {
     *         return new Providers\AzampayProvider(
     *             config('payments.api_key'),
     *             config('payments.secret'),
     *         );
     *     }
     */
    public function supportsMethod(string $method): bool
    {
        return $this->driver()->supportsMethod($method);
    }
}
