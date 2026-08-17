<x-layouts.admin :title="__('admin.audit.heading')">
    <div class="admin-section">
        <h2>{{ __('admin.audit.heading') }}</h2>
        <p class="admin-sub">{{ __('admin.audit.index_sub') }}</p>
    </div>

    <div class="admin-panel admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('admin.audit.actor') }}</th>
                    <th>{{ __('admin.audit.action') }}</th>
                    <th>{{ __('admin.audit.description') }}</th>
                    <th>{{ __('admin.audit.ip') }}</th>
                    <th>{{ __('admin.audit.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->actor?->name ?? '—' }}</td>
                        <td><span class="chip">{{ __('admin.audit.actions.' . $log->action) }}</span></td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->ip_address ?? '—' }}</td>
                        <td>{{ $log->created_at?->format('j M Y, H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-note">{{ __('admin.audit.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.admin>
