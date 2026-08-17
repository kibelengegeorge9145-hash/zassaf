<x-layouts.admin :title="__('admin.administrators.heading')">
    <div class="admin-section admin-toolbar">
        <div>
            <h2>{{ __('admin.administrators.heading') }}</h2>
            <p class="admin-sub">{{ __('admin.administrators.index_sub') }}</p>
        </div>
        <a href="{{ route('admin.administrators.create') }}" class="btn btn-gold">
            <x-icon name="plus" /> {{ __('admin.administrators.add_admin') }}
        </a>
    </div>

    <div class="admin-panel admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('admin.administrators.name') }}</th>
                    <th>{{ __('admin.administrators.email') }}</th>
                    <th>{{ __('admin.administrators.role') }}</th>
                    <th>{{ __('admin.administrators.status') }}</th>
                    <th>{{ __('admin.administrators.last_login') }}</th>
                    <th>{{ __('admin.administrators.created') }}</th>
                    <th class="admin-table-actions">{{ __('admin.administrators.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($administrators as $admin)
                    <tr>
                        <td>
                            <div class="admin-cell-user">
                                <x-avatar :user="$admin" class="avatar-sm" />
                                <div>
                                    <strong>{{ $admin->name }}</strong>
                                    @if ($admin->is(auth()->user()))
                                        <span class="chip chip-gold">{{ __('admin.administrators.you') }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $admin->email }}</td>
                        <td>
                            <span class="chip {{ $admin->isSuperAdmin() ? 'chip-gold' : '' }}">{{ $admin->role_label }}</span>
                        </td>
                        <td>
                            <span class="chip {{ $admin->is_active ? 'chip-gold' : '' }}">{{ $admin->status_label }}</span>
                        </td>
                        <td>{{ $admin->last_login_at?->format('j M Y, H:i') ?? '—' }}</td>
                        <td>{{ $admin->created_at?->format('j M Y') ?? '—' }}</td>
                        <td class="admin-table-actions">
                            <div class="admin-row-actions">
                                <a href="{{ route('admin.administrators.show', $admin) }}" class="btn btn-ghost btn-sm">
                                    <x-icon name="eye" /> {{ __('admin.administrators.view') }}
                                </a>
                                <a href="{{ route('admin.administrators.edit', $admin) }}" class="btn btn-ghost btn-sm">
                                    <x-icon name="pencil" /> {{ __('admin.actions.edit') }}
                                </a>
                                @if (! $admin->is(auth()->user()))
                                    <form method="POST" action="{{ route('admin.administrators.status', $admin) }}"
                                          onsubmit="return confirm('{{ $admin->is_active ? __('admin.administrators.deactivate_confirm') : __('admin.administrators.activate_confirm') }}')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-ghost btn-sm">
                                            {{ $admin->is_active ? __('admin.administrators.deactivate') : __('admin.administrators.activate') }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.administrators.destroy', $admin) }}"
                                          onsubmit="return confirm('{{ __('admin.administrators.delete_confirm') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><x-icon name="trash" /> {{ __('admin.actions.delete') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-note">{{ __('admin.administrators.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
