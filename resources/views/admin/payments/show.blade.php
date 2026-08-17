<x-layouts.admin :title="__('admin.payments.heading')">
    <div class="admin-section">
        <h2>{{ $payment->transaction_reference }}</h2>
        <p class="admin-sub">{{ __('admin.payments.show_sub') }}</p>
    </div>

    <div class="admin-columns">
        <div class="admin-panel">
            <ul class="detail-list">
                <li>
                    <span>{{ __('admin.payments.transaction') }}</span>
                    <strong>{{ $payment->transaction_reference }}</strong>
                </li>
                <li>
                    <span>{{ __('admin.payments.provider_reference') }}</span>
                    <strong>{{ $payment->provider_reference ?? '—' }}</strong>
                </li>
                <li>
                    <span>{{ __('admin.payments.member') }}</span>
                    <strong>{{ $payment->user?->name ?? $payment->member?->user?->name ?? '—' }}</strong>
                </li>
                @if ($payment->payment_type === \App\Models\Payment::TYPE_BOOK && $payment->book)
                    <li>
                        <span>{{ __('admin.books.heading') }}</span>
                        <strong>{{ $payment->book->title_en }}</strong>
                    </li>
                @endif
                <li>
                    <span>{{ __('admin.payments.type') }}</span>
                    <strong>{{ $payment->type_label }}</strong>
                </li>
                <li>
                    <span>{{ __('admin.payments.method') }}</span>
                    <strong>{{ $payment->method_label }}</strong>
                </li>
                <li>
                    <span>{{ __('admin.payments.amount') }}</span>
                    <strong>{{ $payment->formatted_amount }}</strong>
                </li>
                <li>
                    <span>{{ __('admin.payments.status') }}</span>
                    <strong><span class="chip {{ $payment->isPaid() ? 'chip-gold' : '' }}">{{ $payment->status_label }}</span></strong>
                </li>
                <li>
                    <span>{{ __('admin.payments.created_at') }}</span>
                    <strong>{{ $payment->created_at->format('d M Y H:i') }}</strong>
                </li>
                @if ($payment->paid_at)
                    <li>
                        <span>{{ __('admin.payments.paid_at') }}</span>
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
            @if ($payment->member)
                <a href="{{ route('admin.members.show', $payment->member) }}" class="btn btn-gold">{{ __('admin.payments.view_member') }}</a>
            @endif
            <a href="{{ route('admin.payments.index') }}" class="btn btn-ghost">{{ __('admin.actions.back') }}</a>
        </div>
    </div>
</x-layouts.admin>
