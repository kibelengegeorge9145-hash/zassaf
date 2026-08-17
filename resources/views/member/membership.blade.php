<x-layouts.member :title="__('membership.membership.heading')">
    <div class="admin-section">
        <h2>{{ __('membership.membership.heading') }}</h2>
        <p class="admin-sub">{{ __('membership.membership.sub') }}</p>
    </div>

    @if ($member)
        <div class="admin-columns">
            <div class="admin-panel">
                <div class="admin-panel-head">
                    <h3>{{ __('membership.dashboard.status') }}</h3>
                </div>
                <ul class="detail-list">
                    <li>
                        <span>{{ __('membership.membership.status') }}</span>
                        <strong>{{ $member->status_label }}</strong>
                    </li>
                    <li>
                        <span>{{ __('membership.membership.number') }}</span>
                        <strong>{{ $member->membership_number ?? '—' }}</strong>
                    </li>
                    <li>
                        <span>{{ __('membership.membership.joined') }}</span>
                        <strong>{{ $member->joined_at?->format('d M Y') ?? '—' }}</strong>
                    </li>
                    <li>
                        <span>{{ __('membership.membership.expires') }}</span>
                        <strong>{{ $member->expires_at?->format('d M Y') ?? '—' }}</strong>
                    </li>
                </ul>
            </div>

            <div class="admin-panel">
                <div class="admin-panel-head">
                    <h3>{{ __('membership.plans.note') }}</h3>
                </div>
                <ul class="detail-list">
                    <li>
                        <span>{{ __('membership.membership.registration_fee') }}</span>
                        <strong>{{ \App\Support\MembershipConfig::formattedRegistrationFee() }}</strong>
                    </li>
                    <li>
                        <span>{{ __('membership.membership.monthly_fee') }}</span>
                        <strong>{{ \App\Support\MembershipConfig::formattedMonthlyFee() }}</strong>
                    </li>
                </ul>

                <div class="admin-panel-foot">
                    @if ($canPayMonthly)
                        <a href="{{ route('member.payments.create', ['type' => 'monthly']) }}" class="btn btn-gold">
                            {{ __('membership.membership.renew') }}
                        </a>
                    @elseif ($member->isActive())
                        <p class="empty-note">{{ __('membership.membership.paid_until', ['date' => $member->expires_at->format('d M Y')]) }}</p>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="admin-panel">
            <p class="empty-note">{{ __('membership.unavailable') }}</p>
        </div>
    @endif
</x-layouts.member>
