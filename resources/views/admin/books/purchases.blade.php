<x-layouts.admin :title="__('admin.books.purchases.heading')">
    <div class="admin-section">
        <h2>{{ __('admin.books.purchases.heading') }}</h2>
        <p class="admin-sub">{{ __('admin.books.purchases.sub') }}</p>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <span class="stat-icon"><x-icon name="book-open" /></span>
            <strong>{{ $totals['books'] }}</strong>
            <span>{{ __('admin.books.purchases.total_sales') }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-icon"><x-icon name="receipt" /></span>
            <strong>{{ number_format($totals['revenue'], 0) }}</strong>
            <span>{{ __('admin.books.purchases.revenue') }}</span>
        </div>
    </div>

    <div class="form-card admin-form">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('admin.books.purchases.customer') }}</th>
                        <th>{{ __('admin.books.purchases.book') }}</th>
                        <th>{{ __('admin.books.purchases.payment') }}</th>
                        <th>{{ __('admin.books.purchases.amount') }}</th>
                        <th>{{ __('admin.books.purchases.date') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td>
                                @if ($purchase->user)
                                    {{ $purchase->user->name }}<br><small>{{ $purchase->user->email }}</small>
                                @else
                                    {{ $purchase->customer_name ?? __('admin.books.purchases.guest') }}<br>
                                    <small>{{ $purchase->customer_email }}@if ($purchase->customer_phone) · {{ $purchase->customer_phone }}@endif</small>
                                @endif
                            </td>
                            <td>{{ $purchase->book?->title_en ?? '—' }}</td>
                            <td>{{ $purchase->payment?->transaction_reference ?? '—' }}<br><small>{{ $purchase->payment?->status_label ?? '—' }}</small></td>
                            <td>{{ $purchase->payment?->formatted_amount ?? '—' }}</td>
                            <td>{{ $purchase->purchased_at?->format('d M Y H:i') ?? '—' }}</td>
                            <td>
                                @if ($purchase->payment)
                                    <a href="{{ route('admin.payments.show', $purchase->payment) }}" class="link-arrow">{{ __('admin.members.view') }}</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">{{ __('admin.books.purchases.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $purchases->links() }}
        </div>
    </div>
</x-layouts.admin>
