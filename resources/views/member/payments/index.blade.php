<x-layouts.member :title="__('membership.payments.heading')">
    <div class="admin-section">
        <h2>{{ __('membership.payments.heading') }}</h2>
        <p class="admin-sub">{{ __('membership.payments.sub') }}</p>
    </div>

    <form method="GET" action="{{ route('member.payments.index') }}" class="admin-filters">
        <select name="type" onchange="this.form.submit()">
            <option value="">{{ __('membership.payments.filter_type') }}</option>
            <option value="registration" @selected(request('type') === 'registration')>{{ __('membership.statuses.type_registration') }}</option>
            <option value="monthly" @selected(request('type') === 'monthly')>{{ __('membership.statuses.type_monthly') }}</option>
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="">{{ __('membership.payments.filter_status') }}</option>
            @foreach (\App\Models\Payment::STATUSES as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('membership.statuses.payment_'.$status) }}</option>
            @endforeach
        </select>
        @if (request()->filled('type') || request()->filled('status'))
            <a href="{{ route('member.payments.index') }}" class="btn btn-ghost btn-sm">{{ __('site.cta.cancel') }}</a>
        @endif
    </form>

    <div class="form-card admin-form">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('membership.payments.transaction') }}</th>
                        <th>{{ __('membership.payments.date') }}</th>
                        <th>{{ __('membership.payments.type') }}</th>
                        <th>{{ __('membership.payments.method') }}</th>
                        <th>{{ __('membership.payments.amount') }}</th>
                        <th>{{ __('membership.payments.status') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment->transaction_reference }}</td>
                            <td>{{ $payment->created_at->format('d M Y') }}</td>
                            <td>{{ $payment->type_label }}</td>
                            <td>{{ $payment->method_label }}</td>
                            <td>{{ $payment->formatted_amount }}</td>
                            <td><span class="chip {{ $payment->isPaid() ? 'chip-gold' : '' }}">{{ $payment->status_label }}</span></td>
                            <td><a href="{{ route('member.payments.show', $payment) }}" class="link-arrow">{{ __('membership.payments.view') }}</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-cell">{{ __('membership.payments.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $payments->links() }}
        </div>
    </div>
</x-layouts.member>
