<x-layouts.admin :title="__('admin.messages.heading')">
    <div class="admin-section">
        <h2>{{ __('admin.messages.heading') }}</h2>
        <p class="admin-sub">{{ __('admin.messages.index_sub') }}</p>
    </div>

    <div class="admin-panel">
        @forelse ($messages as $message)
            <a href="{{ route('admin.messages.show', $message) }}" class="admin-row admin-row-link">
                <div class="admin-row-main">
                    <div>
                        <strong>{{ $message->name }} @if ($message->subject) — {{ $message->subject }} @endif</strong>
                        <small>{{ \Illuminate\Support\Str::limit($message->message, 90) }}</small>
                        <small class="admin-row-sub">{{ $message->created_at->format('d M Y H:i') }}</small>
                    </div>
                </div>
                <span class="chip {{ $message->is_read ? '' : 'chip-gold' }}">{{ $message->read_label }}</span>
            </a>
        @empty
            <p class="empty-note">{{ __('admin.messages.empty') }}</p>
        @endforelse
    </div>
</x-layouts.admin>
