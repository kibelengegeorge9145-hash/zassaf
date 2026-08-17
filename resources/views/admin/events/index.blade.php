<x-layouts.admin :title="__('admin.events.heading')">
    <div class="admin-section admin-toolbar">
        <div>
            <h2>{{ __('admin.events.heading') }}</h2>
            <p class="admin-sub">{{ __('admin.events.index_sub') }}</p>
        </div>
        <a href="{{ route('admin.events.create') }}" class="btn btn-gold">
            <x-icon name="plus" /> {{ __('admin.events.create') }}
        </a>
    </div>

    <div class="admin-panel">
        @forelse ($events as $event)
            <div class="admin-row">
                <div class="admin-row-main">
                    <span class="admin-row-icon"><x-icon name="calendar" /></span>
                    <div>
                        <strong>{{ $event->title_en }}</strong>
                        <small>{{ $event->event_date->format('d M Y') }} @if ($event->location_en) · {{ $event->location_en }} @endif</small>
                    </div>
                </div>
                <span class="chip {{ $event->is_published ? 'chip-gold' : '' }}">
                    {{ $event->is_published ? __('admin.published_yes') : __('admin.published_no') }}
                </span>
                <div class="admin-row-actions">
                    <form method="POST" action="{{ route('admin.events.toggle', $event) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-ghost btn-sm">
                            {{ $event->is_published ? __('admin.actions.unpublish') : __('admin.actions.publish') }}
                        </button>
                    </form>
                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-ghost btn-sm">
                        <x-icon name="pencil" /> {{ __('admin.actions.edit') }}
                    </a>
                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                          onsubmit="return confirm('{{ __('admin.delete_confirm') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><x-icon name="trash" /> {{ __('admin.actions.delete') }}</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="empty-note">{{ __('admin.events.empty') }}</p>
        @endforelse
    </div>
</x-layouts.admin>
