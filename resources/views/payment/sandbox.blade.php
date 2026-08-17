@php
    $payment = \App\Models\Payment::where('transaction_reference', $ledger->transaction_reference)->first();
    $methodLabel = $payment?->method_label ?? ucfirst((string) $ledger->payment_method);
@endphp
<x-layouts.public :title="__('membership.sandbox.heading')">
    <x-page-header
        :kicker="__('membership.sandbox.heading')"
        :title="__('membership.sandbox.heading')"
        :sub="__('membership.sandbox.sub')"
    />

    <section class="section section-light">
        <div class="container">
            <div class="payment-gateway-card">
                <div class="payment-gateway-brand">
                    <span class="brand-mark" aria-hidden="true">Z</span>
                    <span>Zassaf Elite · Secure Payment</span>
                </div>

                <ul class="detail-list">
                    <li>
                        <span>{{ __('membership.sandbox.amount') }}</span>
                        <strong>{{ number_format((float) $ledger->amount, 0) }} {{ \App\Support\MembershipConfig::currency() }}</strong>
                    </li>
                    <li>
                        <span>{{ __('membership.sandbox.method') }}</span>
                        <strong>{{ $methodLabel }}</strong>
                    </li>
                    <li>
                        <span>{{ __('membership.sandbox.reference') }}</span>
                        <strong>{{ $ledger->provider_reference }}</strong>
                    </li>
                </ul>

                <p class="empty-note">{{ __('membership.sandbox.simulation_note') }}</p>

                <div class="form-actions">
                    <form method="POST" action="{{ route('payment.sandbox.confirm', $ledger->provider_reference) }}">
                        @csrf
                        <button type="submit" class="btn btn-gold">{{ __('membership.sandbox.confirm') }}</button>
                    </form>
                    <form method="POST" action="{{ route('payment.sandbox.cancel', $ledger->provider_reference) }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost">{{ __('membership.sandbox.cancel') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
