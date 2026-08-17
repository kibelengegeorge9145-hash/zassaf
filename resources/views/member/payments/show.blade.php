<x-layouts.member :title="__('membership.payments.detail_heading')">
    <div class="admin-section">
        <h2>{{ __('membership.payments.detail_heading') }}</h2>
        <p class="admin-sub">{{ $payment->transaction_reference }}</p>
    </div>

    <div class="admin-columns">
        <div class="admin-panel">
            <ul class="detail-list">
                <li>
                    <span>{{ __('membership.payments.reference') }}</span>
                    <strong>{{ $payment->transaction_reference }}</strong>
                </li>
                @if ($payment->provider_reference)
                    <li>
                        <span>{{ __('membership.payments.provider_reference') }}</span>
                        <strong>{{ $payment->provider_reference }}</strong>
                    </li>
                @endif
                <li>
                    <span>{{ __('membership.payments.type') }}</span>
                    <strong>{{ $payment->type_label }}</strong>
                </li>
                <li>
                    <span>{{ __('membership.payments.method') }}</span>
                    <strong>{{ $payment->method_label }}</strong>
                </li>
                <li>
                    <span>{{ __('membership.payments.amount') }}</span>
                    <strong>{{ $payment->formatted_amount }}</strong>
                </li>
                <li>
                    <span>{{ __('membership.payments.status') }}</span>
                    <strong><span class="chip {{ $payment->isPaid() ? 'chip-gold' : '' }}">{{ $payment->status_label }}</span></strong>
                </li>
                <li>
                    <span>{{ __('membership.payments.created_at') }}</span>
                    <strong>{{ $payment->created_at->format('d M Y H:i') }}</strong>
                </li>
                @if ($payment->paid_at)
                    <li>
                        <span>{{ __('membership.payments.paid_at') }}</span>
                        <strong>{{ $payment->paid_at->format('d M Y H:i') }}</strong>
                    </li>
                @endif
                @if ($payment->failure_reason)
                    <li>
                        <span>{{ __('admin.details') }}</span>
                        <strong>{{ $payment->failure_reason }}</strong>
                    </li>
                @endif
            </ul>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <h3>{{ __('membership.payments.choose_method') }}</h3>
            </div>
            @if ($payment->isPending() && $payment->payment_type === 'registration')
                <a href="{{ route('member.payments.create', ['type' => 'registration']) }}" class="btn btn-gold">
                    {{ __('membership.payments.new_registration_payment') }}
                </a>
            @else
                <a href="{{ route('member.payments.create', ['type' => 'monthly']) }}" class="btn btn-gold">
                    {{ __('membership.payments.pay_monthly') }}
                </a>
            @endif
            <a href="{{ route('member.payments.index') }}" class="btn btn-ghost">{{ __('admin.actions.back') }}</a>
        </div>
    </div>
</x-layouts.member>
