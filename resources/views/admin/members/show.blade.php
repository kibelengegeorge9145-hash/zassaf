<x-layouts.admin :title="__('admin.members.heading')">
    <div class="admin-section">
        <h2>{{ $member->user?->name ?? __('admin.members.heading') }}</h2>
        <p class="admin-sub">{{ __('admin.members.show_sub') }}</p>
    </div>

    <div class="admin-columns">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <h3>{{ __('admin.members.number') }}</h3>
                <span class="chip {{ $member->isActive() ? 'chip-gold' : '' }}">{{ $member->status_label }}</span>
            </div>
            <ul class="detail-list">
                <li>
                    <span>{{ __('admin.members.number') }}</span>
                    <strong>{{ $member->membership_number ?? '—' }}</strong>
                </li>
                <li>
                    <span>{{ __('admin.members.name') }}</span>
                    <strong>{{ $member->user?->name }}</strong>
                </li>
                <li>
                    <span>{{ __('admin.members.email') }}</span>
                    <strong>{{ $member->user?->email }}</strong>
                </li>
                <li>
                    <span>{{ __('admin.members.phone') }}</span>
                    <strong>{{ $member->user?->phone ?? '—' }}</strong>
                </li>
                <li>
                    <span>{{ __('admin.members.joined') }}</span>
                    <strong>{{ $member->joined_at?->format('d M Y') ?? '—' }}</strong>
                </li>
                <li>
                    <span>{{ __('admin.members.expires') }}</span>
                    <strong>{{ $member->expires_at?->format('d M Y') ?? '—' }}</strong>
                </li>
            </ul>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <h3>{{ __('admin.members.actions_heading') }}</h3>
            </div>

            @if ($member->status !== \App\Models\Member::STATUS_SUSPENDED)
                <form method="POST" action="{{ route('admin.members.status', $member) }}" class="admin-panel-form" onsubmit="return confirm('{{ __('admin.members.suspend_confirm') }}')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="suspended">
                    <button type="submit" class="btn btn-ghost">{{ __('admin.members.suspend') }}</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.members.status', $member) }}" class="admin-panel-form">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="btn btn-gold">{{ __('admin.members.activate') }}</button>
                </form>
            @endif

            @if ($member->status !== \App\Models\Member::STATUS_CANCELLED)
                <form method="POST" action="{{ route('admin.members.status', $member) }}" class="admin-panel-form" onsubmit="return confirm('{{ __('admin.members.cancel_confirm') }}')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn btn-ghost">{{ __('admin.members.cancel') }}</button>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.members.expired', $member) }}" class="admin-panel-form" onsubmit="return confirm('{{ __('admin.members.expired_confirm') }}')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-ghost">{{ __('admin.members.mark_expired') }}</button>
            </form>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-head">
            <h3>{{ __('admin.members.record_payment') }}</h3>
        </div>
        <form method="POST" action="{{ route('admin.members.payments.store', $member) }}" class="form-card admin-form">
            @csrf
            <div class="form-grid">
                <div class="form-field">
                    <label for="payment_type">{{ __('admin.members.payment_type') }}</label>
                    <select id="payment_type" name="payment_type">
                        <option value="registration">{{ __('admin.members.type_registration') }}</option>
                        <option value="monthly">{{ __('admin.members.type_monthly') }}</option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="payment_method">{{ __('admin.members.payment_method') }}</label>
                    <select id="payment_method" name="payment_method">
                        @foreach (\App\Models\Payment::METHODS as $method => $label)
                            <option value="{{ $method }}">{{ __('membership.payments.'.$method) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label for="amount">{{ __('admin.members.amount') }}</label>
                    <input type="number" id="amount" name="amount" min="0" step="0.01" required value="{{ old('amount', \App\Support\MembershipConfig::monthlyFee()) }}">
                    @error('amount') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-field form-field-full">
                    <label for="note">{{ __('admin.members.note') }}</label>
                    <input type="text" id="note" name="note" maxlength="1000" value="{{ old('note') }}" placeholder="{{ __('admin.members.note_hint') }}">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-gold">{{ __('admin.members.record') }}</button>
            </div>
        </form>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-head">
            <h3>{{ __('admin.nav.payments') }}</h3>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('admin.payments.transaction') }}</th>
                        <th>{{ __('admin.payments.type') }}</th>
                        <th>{{ __('admin.payments.amount') }}</th>
                        <th>{{ __('admin.payments.method') }}</th>
                        <th>{{ __('admin.payments.status') }}</th>
                        <th>{{ __('admin.payments.created_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($member->payments as $payment)
                        <tr>
                            <td>{{ $payment->transaction_reference }}</td>
                            <td>{{ $payment->type_label }}</td>
                            <td>{{ $payment->formatted_amount }}</td>
                            <td>{{ $payment->method_label }}</td>
                            <td><span class="chip {{ $payment->isPaid() ? 'chip-gold' : '' }}">{{ $payment->status_label }}</span></td>
                            <td>{{ $payment->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">{{ __('admin.payments.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
