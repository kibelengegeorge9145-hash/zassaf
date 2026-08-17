<x-layouts.admin :title="__('admin.members.heading')">
    <div class="admin-section">
        <h2>{{ __('admin.members.heading') }}</h2>
        <p class="admin-sub">{{ __('admin.members.index_sub') }}</p>
    </div>

    <form method="GET" action="{{ route('admin.members.index') }}" class="admin-filters">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.members.search') }}">
        <select name="status" onchange="this.form.submit()">
            <option value="">{{ __('admin.members.all_statuses') }}</option>
            @foreach (\App\Models\Member::STATUSES as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('membership.statuses.member_'.$status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-gold btn-sm">{{ __('admin.members.search') }}</button>
        @if (request()->filled('search') || request()->filled('status'))
            <a href="{{ route('admin.members.index') }}" class="btn btn-ghost btn-sm">{{ __('admin.actions.cancel') }}</a>
        @endif
    </form>

    <div class="form-card admin-form">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('admin.members.number') }}</th>
                        <th>{{ __('admin.members.name') }}</th>
                        <th>{{ __('admin.members.email') }}</th>
                        <th>{{ __('admin.members.status') }}</th>
                        <th>{{ __('admin.members.joined') }}</th>
                        <th>{{ __('admin.members.expires') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        <tr>
                            <td>{{ $member->membership_number ?? '—' }}</td>
                            <td>{{ $member->user?->name ?? '—' }}</td>
                            <td>{{ $member->user?->email ?? '—' }}</td>
                            <td><span class="chip {{ $member->isActive() ? 'chip-gold' : '' }}">{{ $member->status_label }}</span></td>
                            <td>{{ $member->joined_at?->format('d M Y') ?? '—' }}</td>
                            <td>{{ $member->expires_at?->format('d M Y') ?? '—' }}</td>
                            <td><a href="{{ route('admin.members.show', $member) }}" class="link-arrow">{{ __('admin.members.view') }}</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-cell">{{ __('admin.members.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $members->links() }}
        </div>
    </div>
</x-layouts.admin>
