<x-layouts.admin :title="__('admin.messages.heading')">
    <div class="admin-section admin-toolbar">
        <div>
            <h2>{{ $message->name }}</h2>
            <p class="admin-sub">{{ __('admin.messages.show_sub') }}</p>
        </div>
        <div class="admin-row-actions">
            <a href="{{ route('admin.messages.index') }}" class="btn btn-ghost">{{ __('admin.actions.back') }}</a>
            <form method="POST" action="{{ route('admin.messages.read', $message) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-ghost">{{ __('admin.actions.mark_read') }}</button>
            </form>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                  onsubmit="return confirm('{{ __('admin.messages.delete_confirm') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><x-icon name="trash" /> {{ __('admin.actions.delete') }}</button>
            </form>
        </div>
    </div>

    <div class="admin-panel">
        <dl class="detail-list">
            <div><dt>{{ __('forms.labels.name') }}</dt><dd>{{ $message->name }}</dd></div>
            <div><dt>{{ __('forms.labels.email') }}</dt><dd><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd></div>
            <div><dt>{{ __('forms.labels.phone') }}</dt><dd>{{ $message->phone ?? '—' }}</dd></div>
            <div><dt>{{ __('admin.messages.subject') }}</dt><dd>{{ $message->subject ?? '—' }}</dd></div>
            <div><dt>{{ __('admin.registrations.submitted') }}</dt><dd>{{ $message->created_at->format('d M Y H:i') }}</dd></div>
        </dl>
        <h3>{{ __('forms.labels.message') }}</h3>
        <p class="admin-message">{{ $message->message }}</p>
    </div>
</x-layouts.admin>
