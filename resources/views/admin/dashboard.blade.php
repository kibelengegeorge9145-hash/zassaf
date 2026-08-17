<x-layouts.admin :title="__('admin.overview')">
    <div class="admin-section">
        <h2>{{ __('admin.welcome', ['name' => auth()->user()->name]) }}</h2>
        <p class="admin-sub">{{ __('admin.overview_text') }}</p>
    </div>

    <div class="stat-grid">
        <a href="{{ route('admin.programs.index') }}" class="stat-card">
            <span class="stat-icon"><x-icon name="sparkles" /></span>
            <strong>{{ $stats['programs'] }}</strong>
            <span>{{ __('admin.stats.programs') }}</span>
        </a>
        <a href="{{ route('admin.events.index') }}" class="stat-card">
            <span class="stat-icon"><x-icon name="calendar" /></span>
            <strong>{{ $stats['events'] }}</strong>
            <span>{{ __('admin.stats.events') }}</span>
        </a>
        <a href="{{ route('admin.convos.index') }}" class="stat-card">
            <span class="stat-icon"><x-icon name="mic" /></span>
            <strong>{{ $stats['convos'] }}</strong>
            <span>{{ __('admin.stats.convos') }}</span>
        </a>
        <a href="{{ route('admin.books.index') }}" class="stat-card">
            <span class="stat-icon"><x-icon name="book-open" /></span>
            <strong>{{ $stats['books'] }}</strong>
            <span>{{ __('admin.stats.books') }}</span>
        </a>
        <a href="{{ route('admin.registrations.index') }}" class="stat-card">
            <span class="stat-icon"><x-icon name="clipboard" /></span>
            <strong>{{ $stats['registrations'] }}</strong>
            <span>{{ __('admin.stats.registrations') }}</span>
        </a>
        <a href="{{ route('admin.messages.index') }}" class="stat-card">
            <span class="stat-icon"><x-icon name="message-circle" /></span>
            <strong>{{ $stats['unread_messages'] }}</strong>
            <span>{{ __('admin.stats.unread_messages') }}</span>
        </a>
        @if (auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.administrators.index') }}" class="stat-card">
                <span class="stat-icon"><x-icon name="users" /></span>
                <strong>{{ $stats['users'] }}</strong>
                <span>{{ __('admin.stats.users') }}</span>
            </a>
        @endif
    </div>

    <div class="admin-columns">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <h3>{{ __('admin.stats.registrations') }}</h3>
                <a href="{{ route('admin.registrations.index') }}" class="link-arrow">{{ __('site.cta.view_all') }}</a>
            </div>

            @forelse ($recentRegistrations as $registration)
                <a href="{{ route('admin.registrations.show', $registration) }}" class="admin-list-row">
                    <div>
                        <strong>{{ $registration->full_name }}</strong>
                        <small>{{ $registration->email }}</small>
                    </div>
                    <span class="chip">{{ $registration->type_label }}</span>
                    <span class="chip {{ $registration->status === 'new' ? 'chip-gold' : '' }}">{{ $registration->status_label }}</span>
                </a>
            @empty
                <p class="empty-note">{{ __('admin.registrations.empty') }}</p>
            @endforelse
        </div>

        <div class="admin-panel">
            <div class="admin-panel-head">
                <h3>{{ __('admin.nav.messages') }}</h3>
                <a href="{{ route('admin.messages.index') }}" class="link-arrow">{{ __('site.cta.view_all') }}</a>
            </div>

            @forelse ($recentMessages as $message)
                <a href="{{ route('admin.messages.show', $message) }}" class="admin-list-row">
                    <div>
                        <strong>{{ $message->name }}</strong>
                        <small>{{ \Illuminate\Support\Str::limit($message->subject ?? $message->message, 48) }}</small>
                    </div>
                    <span class="chip {{ $message->is_read ? '' : 'chip-gold' }}">{{ $message->read_label }}</span>
                </a>
            @empty
                <p class="empty-note">{{ __('admin.messages.empty') }}</p>
            @endforelse
        </div>
    </div>
</x-layouts.admin>
