<x-layouts.admin :title="__('admin.convos.heading')">
    <div class="admin-section admin-toolbar">
        <div>
            <h2>{{ __('admin.convos.heading') }}</h2>
            <p class="admin-sub">{{ __('admin.convos.index_sub') }}</p>
        </div>
        <a href="{{ route('admin.convos.create') }}" class="btn btn-gold">
            <x-icon name="plus" /> {{ __('admin.convos.create') }}
        </a>
    </div>

    <div class="admin-panel">
        @forelse ($convos as $convo)
            <div class="admin-row">
                <div class="admin-row-main">
                    <span class="admin-row-icon"><x-icon name="mic" /></span>
                    <div>
                        <strong>{{ $convo->title_en }}</strong>
                        <small>
                            {{ $convo->event_date?->format('d M Y') ?? '—' }}
                            @if ($convo->speaker_en) · {{ $convo->speaker_en }} @endif
                        </small>
                    </div>
                </div>
                <span class="chip {{ $convo->is_published ? 'chip-gold' : '' }}">
                    {{ $convo->is_published ? __('admin.published_yes') : __('admin.published_no') }}
                </span>
                <div class="admin-row-actions">
                    <form method="POST" action="{{ route('admin.convos.toggle', $convo) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-ghost btn-sm">
                            {{ $convo->is_published ? __('admin.actions.unpublish') : __('admin.actions.publish') }}
                        </button>
                    </form>
                    <a href="{{ route('admin.convos.edit', $convo) }}" class="btn btn-ghost btn-sm">
                        <x-icon name="pencil" /> {{ __('admin.actions.edit') }}
                    </a>
                    <form method="POST" action="{{ route('admin.convos.destroy', $convo) }}"
                          onsubmit="return confirm('{{ __('admin.delete_confirm') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><x-icon name="trash" /> {{ __('admin.actions.delete') }}</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="empty-note">{{ __('admin.convos.empty') }}</p>
        @endforelse
    </div>
</x-layouts.admin>
