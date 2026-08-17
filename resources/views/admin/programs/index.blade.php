<x-layouts.admin :title="__('admin.programs.heading')">
    <div class="admin-section admin-toolbar">
        <div>
            <h2>{{ __('admin.programs.heading') }}</h2>
            <p class="admin-sub">{{ __('admin.programs.index_sub') }}</p>
        </div>
        <a href="{{ route('admin.programs.create') }}" class="btn btn-gold">
            <x-icon name="plus" /> {{ __('admin.programs.create') }}
        </a>
    </div>

    <div class="admin-panel">
        @forelse ($programs as $program)
            <div class="admin-row">
                <div class="admin-row-main">
                    <span class="admin-row-icon"><x-icon :name="$program->icon" /></span>
                    <div>
                        <strong>{{ $program->title_en }}</strong>
                        <small>{{ \Illuminate\Support\Str::limit($program->description_en, 80) }}</small>
                    </div>
                </div>
                <span class="chip {{ $program->is_published ? 'chip-gold' : '' }}">
                    {{ $program->is_published ? __('admin.published_yes') : __('admin.published_no') }}
                </span>
                <div class="admin-row-actions">
                    <form method="POST" action="{{ route('admin.programs.toggle', $program) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-ghost btn-sm">
                            {{ $program->is_published ? __('admin.actions.unpublish') : __('admin.actions.publish') }}
                        </button>
                    </form>
                    <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-ghost btn-sm">
                        <x-icon name="pencil" /> {{ __('admin.actions.edit') }}
                    </a>
                    <form method="POST" action="{{ route('admin.programs.destroy', $program) }}"
                          onsubmit="return confirm('{{ __('admin.delete_confirm') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><x-icon name="trash" /> {{ __('admin.actions.delete') }}</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="empty-note">{{ __('admin.programs.empty') }}</p>
        @endforelse
    </div>
</x-layouts.admin>
