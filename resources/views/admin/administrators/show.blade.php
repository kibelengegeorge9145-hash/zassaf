<x-layouts.admin :title="__('admin.administrators.view', ['name' => $user->name])">
    <div class="admin-section admin-toolbar">
        <div>
            <h2>{{ $user->name }}</h2>
            <p class="admin-sub">{{ __('admin.administrators.show_sub') }}</p>
        </div>
        <div class="admin-row-actions">
            <a href="{{ route('admin.administrators.index') }}" class="btn btn-ghost">{{ __('admin.actions.back') }}</a>
            <a href="{{ route('admin.administrators.edit', $user) }}" class="btn btn-gold">
                <x-icon name="pencil" /> {{ __('admin.actions.edit') }}
            </a>
        </div>
    </div>

    <div class="admin-columns">
        <div class="admin-panel">
            <h3>{{ __('admin.profile.account_heading') }}</h3>
            <div class="admin-cell-user admin-cell-user-lg">
                <x-avatar :user="$user" class="avatar-lg" />
                <div>
                    <strong>{{ $user->name }}</strong>
                    <small>{{ $user->email }}</small>
                </div>
            </div>
            <dl class="detail-list">
                <div>
                    <dt>{{ __('admin.profile.username') }}</dt>
                    <dd>{{ $user->username }}</dd>
                </div>
                <div>
                    <dt>{{ __('admin.profile.phone') }}</dt>
                    <dd>{{ $user->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt>{{ __('admin.profile.role') }}</dt>
                    <dd>
                        <span class="chip {{ $user->isSuperAdmin() ? 'chip-gold' : '' }}">{{ $user->role_label }}</span>
                    </dd>
                </div>
                <div>
                    <dt>{{ __('admin.profile.status') }}</dt>
                    <dd>
                        <span class="chip {{ $user->is_active ? 'chip-gold' : '' }}">{{ $user->status_label }}</span>
                    </dd>
                </div>
                <div>
                    <dt>{{ __('admin.profile.date_joined') }}</dt>
                    <dd>{{ $user->created_at?->format('j F Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt>{{ __('admin.profile.last_login') }}</dt>
                    <dd>{{ $user->last_login_at?->format('j F Y, H:i') ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="admin-panel">
            <h3>{{ __('admin.administrators.actions') }}</h3>
            <div class="admin-row-actions admin-row-actions-stack">
                @if (! $user->is(auth()->user()))
                    <form method="POST" action="{{ route('admin.administrators.status', $user) }}"
                          onsubmit="return confirm('{{ $user->is_active ? __('admin.administrators.deactivate_confirm') : __('admin.administrators.activate_confirm') }}')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn {{ $user->is_active ? 'btn-ghost' : 'btn-gold' }}">
                            {{ $user->is_active ? __('admin.administrators.deactivate') : __('admin.administrators.activate') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.administrators.destroy', $user) }}"
                          onsubmit="return confirm('{{ __('admin.administrators.delete_confirm') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><x-icon name="trash" /> {{ __('admin.actions.delete') }}</button>
                    </form>
                @else
                    <p class="field-hint">{{ __('admin.administrators.cannot_edit_self') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
