<x-layouts.public :title="$success ? __('membership.messages.payment_success') : __('membership.messages.payment_failed')">
    <x-page-header
        :kicker="__('membership.payments.heading')"
        :title="$success ? __('membership.messages.payment_success') : __('membership.messages.payment_failed')"
        :sub="$message"
    />

    <section class="section section-light">
        <div class="container">
            <div class="payment-gateway-card">
                <div class="form-actions">
                    <a href="{{ route('member.dashboard') }}" class="btn btn-gold">{{ __('membership.nav.dashboard') }}</a>
                    <a href="{{ route('home') }}" class="btn btn-ghost">{{ __('membership.login.back') }}</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
