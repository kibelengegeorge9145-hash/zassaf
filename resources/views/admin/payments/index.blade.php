<x-layouts.admin :title="__('admin.payments.heading')">
    <div class="admin-section">
        <h2>{{ __('admin.payments.heading') }}</h2>
        <p class="admin-sub">{{ __('admin.payments.index_sub') }}</p>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-icon"><x-icon name="receipt" /></span>
            <strong>{{ $totals['total'] }}</strong>
            <span>{{ __('admin.payments.total') }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-icon"><x-icon name="check" /></span>
            <strong>{{ $totals['paid'] }}</strong>
            <span>{{ __('membership.statuses.payment_paid') }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-icon"><x-icon name="clock" /></span>
            <strong>{{ $totals['pending'] }}</strong>
            <span>{{ __('membership.statuses.payment_pending') }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-icon"><x-icon name="close" /></span>
            <strong>{{ $totals['failed'] }}</strong>
            <span>{{ __('membership.statuses.payment_failed') }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-icon"><x-icon name="sparkles" /></span>
            <strong>{{ number_format($totals['revenue'], 0) }}</strong>
            <span>{{ __('admin.payments.revenue') }}</span>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.payments.index') }}" class="admin-filters">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.payments.search') }}">
        <select name="type" onchange="this.form.submit()">
            <option value="">{{ __('admin.payments.all_types') }}</option>
            <option value="registration" @selected(request('type') === 'registration')>{{ __('admin.payments.type_registration') }}</option>
            <option value="monthly" @selected(request('type') === 'monthly')>{{ __('admin.payments.type_monthly') }}</option>
            <option value="book" @selected(request('type') === 'book')>{{ __('admin.payments.type_book') }}</option>
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="">{{ __('admin.payments.all_statuses') }}</option>
            @foreach (\App\Models\Payment::STATUSES as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('membership.statuses.payment_'.$status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-gold btn-sm">{{ __('admin.payments.search') }}</button>
        @if (request()->filled('search') || request()->filled('type') || request()->filled('status'))
            <a href="{{ route('admin.payments.index') }}" class="btn btn-ghost btn-sm">{{ __('admin.actions.cancel') }}</a>
        @endif
    </form>

    <div class="form-card admin-form">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('admin.payments.transaction') }}</th>
                        <th>{{ __('admin.payments.member') }}</th>
                        <th>{{ __('admin.payments.type') }}</th>
                        <th>{{ __('admin.payments.method') }}</th>
                        <th>{{ __('admin.payments.amount') }}</th>
                        <th>{{ __('admin.payments.status') }}</th>
                        <th>{{ __('admin.payments.created_at') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment->transaction_reference }}</td>
                            <td>{{ $payment->user?->name ?? $payment->member?->user?->name ?? '—' }}</td>
                            <td>{{ $payment->type_label }}</td>
                            <td>{{ $payment->method_label }}</td>
                            <td>{{ $payment->formatted_amount }}</td>
                            <td><span class="chip {{ $payment->isPaid() ? 'chip-gold' : '' }}">{{ $payment->status_label }}</span></td>
                            <td>{{ $payment->created_at->format('d M Y') }}</td>
                            <td><a href="{{ route('admin.payments.show', $payment) }}" class="link-arrow">{{ __('admin.members.view') }}</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-cell">{{ __('admin.payments.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $payments->links() }}
        </div>
    </div>
</x-layouts.admin>
