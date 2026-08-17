<x-layouts.admin :title="__('admin.registrations.heading')">
    <div class="admin-section">
        <h2>{{ __('admin.registrations.heading') }}</h2>
        <p class="admin-sub">{{ __('admin.registrations.index_sub') }}</p>
    </div>

    <form method="GET" action="{{ route('admin.registrations.index') }}" class="filter-bar">
        <select name="type" onchange="this.form.submit()">
            <option value="">{{ __('admin.registrations.type') }} — {{ __('admin.all') }}</option>
            @foreach (\App\Models\Registration::TYPES as $type)
                <option value="{{ $type }}" @selected(request('type') === $type)>{{ __('admin.type_' . $type) }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="">{{ __('admin.registrations.status') }} — {{ __('admin.all') }}</option>
            @foreach (\App\Models\Registration::STATUSES as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ __('admin.status_' . $status) }}</option>
            @endforeach
        </select>
    </form>

    <div class="admin-panel">
        @forelse ($registrations as $registration)
            <div class="admin-row">
                <div class="admin-row-main">
                    <div>
                        <strong>{{ $registration->full_name }}</strong>
                        <small>{{ $registration->email }} @if ($registration->phone) · {{ $registration->phone }} @endif</small>
                        @if ($registration->reference)
                            <small class="admin-row-sub">@if ($registration->type !== 'membership') {{ $registration->reference }} · @endif {{ $registration->created_at->diffForHumans() }}</small>
                        @else
                            <small class="admin-row-sub">{{ $registration->created_at->diffForHumans() }}</small>
                        @endif
                    </div>
                </div>
                <span class="chip">{{ $registration->type_label }}</span>
                <span class="chip {{ $registration->status === 'new' ? 'chip-gold' : '' }}">{{ $registration->status_label }}</span>
                <div class="admin-row-actions">
                    <a href="{{ route('admin.registrations.show', $registration) }}" class="btn btn-ghost btn-sm">{{ __('admin.actions.edit') }}</a>
                    <form method="POST" action="{{ route('admin.registrations.destroy', $registration) }}"
                          onsubmit="return confirm('{{ __('admin.registrations.delete_confirm') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><x-icon name="trash" /> {{ __('admin.actions.delete') }}</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="empty-note">{{ __('admin.registrations.empty') }}</p>
        @endforelse
    </div>
</x-layouts.admin>
