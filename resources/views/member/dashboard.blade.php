<x-layouts.member :title="__('membership.dashboard.heading')">
    <div class="admin-section">
        <h2>{{ __('membership.dashboard.heading') }}</h2>
        <p class="admin-sub">{{ __('membership.dashboard.sub') }}</p>
    </div>

    @if ($member)
        <div class="stat-grid">
            <div class="stat-card">
                <span class="stat-icon"><x-icon name="shield" /></span>
                <strong class="{{ $member->isActive() ? '' : 'text-muted' }}">{{ $member->status_label }}</strong>
                <span>{{ __('membership.dashboard.status') }}</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon"><x-icon name="user" /></span>
                <strong>{{ $member->membership_number ?? __('membership.dashboard.no_expiry') }}</strong>
                <span>{{ __('membership.dashboard.number') }}</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon"><x-icon name="calendar" /></span>
                <strong>{{ $member->joined_at?->format('d M Y') ?? __('membership.dashboard.no_expiry') }}</strong>
                <span>{{ __('membership.dashboard.joined') }}</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon"><x-icon name="clock" /></span>
                <strong>{{ $member->expires_at?->format('d M Y') ?? __('membership.dashboard.no_expiry') }}</strong>
                <span>{{ __('membership.dashboard.expires') }}</span>
            </div>
        </div>

        @if ($member->isPending())
            <div class="admin-panel">
                <p class="empty-note">{{ __('membership.payments.pending_note') }}</p>
                <a href="{{ route('member.payments.create', ['type' => 'registration']) }}" class="btn btn-gold">
                    {{ __('membership.payments.new_registration_payment') }}
                </a>
            </div>
        @elseif ($member->isActive())
            <div class="admin-panel">
                <p>{{ __('membership.dashboard.current_period_covered', ['date' => $member->expires_at->format('d M Y')]) }}</p>
                @if ($canPayMonthly)
                    <a href="{{ route('member.payments.create', ['type' => 'monthly']) }}" class="btn btn-gold">
                        {{ __('membership.dashboard.pay_monthly') }}
                    </a>
                @endif
            </div>
        @elseif ($member->canRenew())
            <div class="admin-panel">
                <p>{{ __('membership.dashboard.overdue') }}</p>
                <a href="{{ route('member.payments.create', ['type' => 'monthly']) }}" class="btn btn-gold">
                    {{ __('membership.dashboard.pay_monthly') }}
                </a>
            </div>
        @else
            <div class="admin-panel">
                <p class="empty-note">{{ __('membership.messages.status_suspended') }}</p>
            </div>
        @endif

        <div class="admin-columns">
            <div class="admin-panel">
                <div class="admin-panel-head">
                    <h3>{{ __('membership.dashboard.recent_payments') }}</h3>
                    <a href="{{ route('member.payments.index') }}" class="link-arrow">{{ __('membership.dashboard.view_all') }}</a>
                </div>

                @forelse ($recentPayments as $payment)
                    <a href="{{ route('member.payments.show', $payment) }}" class="admin-list-row">
                        <div>
                            <strong>{{ $payment->type_label }}</strong>
                            <small>{{ $payment->transaction_reference }} · {{ $payment->created_at->format('d M Y') }}</small>
                        </div>
                        <span class="chip">{{ $payment->formatted_amount }}</span>
                        <span class="chip {{ $payment->isPaid() ? 'chip-gold' : '' }}">{{ $payment->status_label }}</span>
                    </a>
                @empty
                    <p class="empty-note">{{ __('membership.dashboard.no_payments') }}</p>
                @endforelse
            </div>

            <div class="admin-panel">
                <div class="admin-panel-head">
                    <h3>{{ __('books.library.heading') }}</h3>
                    <a href="{{ route('member.library') }}" class="link-arrow">{{ __('books.details') }}</a>
                </div>
                @if ($libraryBooks->isNotEmpty())
                    @foreach ($libraryBooks->take(3) as $book)
                        <a href="{{ route('books.show', $book) }}" class="admin-list-row">
                            <div>
                                <strong>{{ $book->title }}</strong>
                                <small>{{ $book->author }}</small>
                            </div>
                            <span class="chip chip-gold">{{ __('books.you_own') }}</span>
                            <x-icon name="arrow-right" />
                        </a>
                    @endforeach
                @else
                    <p class="empty-note">{{ __('books.library.empty') }}</p>
                    <a href="{{ route('books') }}" class="btn btn-gold btn-sm">{{ __('books.library.browse') }}</a>
                @endif
            </div>

            <div class="admin-panel">
                <div class="admin-panel-head">
                    <h3>{{ __('membership.dashboard.quick_actions') }}</h3>
                </div>
                <a href="{{ route('member.membership') }}" class="admin-list-row">
                    <div>
                        <strong>{{ __('membership.dashboard.membership_details') }}</strong>
                    </div>
                    <x-icon name="arrow-right" />
                </a>
                <a href="{{ route('member.profile') }}" class="admin-list-row">
                    <div>
                        <strong>{{ __('membership.dashboard.manage_profile') }}</strong>
                    </div>
                    <x-icon name="arrow-right" />
                </a>
            </div>
        </div>
    @else
        <div class="admin-panel">
            <p class="empty-note">{{ __('membership.unavailable') }}</p>
        </div>
    @endif
</x-layouts.member>
