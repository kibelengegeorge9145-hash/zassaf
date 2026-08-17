@props(['title' => 'Dashboard'])

@php
    $user = auth()->user();

    $groups = [
        ['label' => __('admin.nav.dashboard'), 'items' => [
            ['label' => __('admin.nav.dashboard'), 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'icon' => 'grid'],
        ]],
        ['label' => __('admin.nav.group_content'), 'items' => [
            ['label' => __('admin.nav.programs'), 'route' => 'admin.programs.index', 'pattern' => 'admin.programs.*', 'icon' => 'sparkles'],
            ['label' => __('admin.nav.weekend_convos'), 'route' => 'admin.convos.index', 'pattern' => 'admin.convos.*', 'icon' => 'mic'],
            ['label' => __('admin.nav.books'), 'route' => 'admin.books.index', 'pattern' => 'admin.books.*', 'icon' => 'book-open'],
            ['label' => __('admin.nav.events'), 'route' => 'admin.events.index', 'pattern' => 'admin.events.*', 'icon' => 'calendar'],
        ]],
        ['label' => __('admin.nav.group_community'), 'items' => [
            ['label' => __('admin.nav.registrations'), 'route' => 'admin.registrations.index', 'pattern' => 'admin.registrations.*', 'icon' => 'clipboard'],
            ['label' => __('admin.nav.members'), 'route' => 'admin.members.index', 'pattern' => 'admin.members.*', 'icon' => 'users'],
            ['label' => __('admin.nav.payments'), 'route' => 'admin.payments.index', 'pattern' => 'admin.payments.*', 'icon' => 'receipt'],
        ]],
        ['label' => __('admin.nav.group_communication'), 'items' => [
            ['label' => __('admin.nav.messages'), 'route' => 'admin.messages.index', 'pattern' => 'admin.messages.*', 'icon' => 'message-circle'],
        ]],
        ['label' => __('admin.nav.group_account'), 'items' => [
            ['label' => __('admin.nav.profile'), 'route' => 'admin.profile', 'pattern' => 'admin.profile*', 'icon' => 'user'],
        ]],
    ];

    $systemItems = [
        ['label' => __('admin.nav.settings'), 'route' => 'admin.settings.edit', 'pattern' => 'admin.settings.*', 'icon' => 'settings'],
    ];

    if ($user->isSuperAdmin()) {
        $systemItems[] = ['label' => __('admin.nav.membership_settings'), 'route' => 'admin.membership_settings.edit', 'pattern' => 'admin.membership_settings.*', 'icon' => 'shield'];
        $systemItems[] = ['label' => __('admin.nav.administrators'), 'route' => 'admin.administrators.index', 'pattern' => 'admin.administrators.*', 'icon' => 'users'];
        $systemItems[] = ['label' => __('admin.nav.audit_logs'), 'route' => 'admin.audit.index', 'pattern' => 'admin.audit.*', 'icon' => 'shield'];
    }

    $groups[] = ['label' => __('admin.nav.group_system'), 'items' => $systemItems];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} · {{ __('admin.dashboard') }} — Zassaf</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('head')
</head>
<body class="admin-body">
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand admin-brand">
            <span class="brand-mark" aria-hidden="true">Z</span>
            <span class="brand-text">
                <span class="brand-name">Zassaf Elite</span>
                <span class="brand-sub">{{ __('admin.admin_panel') }}</span>
            </span>
        </a>

        <nav class="admin-nav" aria-label="Admin">
            @foreach ($groups as $group)
                <div class="admin-nav-group">
                    <span class="admin-nav-label">{{ $group['label'] }}</span>
                    @foreach ($group['items'] as $item)
                        <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['pattern']) ? 'is-active' : '' }}">
                            <x-icon :name="$item['icon']" />
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>

        <div class="admin-sidebar-footer">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="admin-nav-logout">
                    <x-icon name="logout" />
                    <span>{{ __('admin.logout') }}</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="nav-toggle" id="adminNavToggle" type="button" aria-controls="adminSidebar" aria-expanded="false" aria-label="Menu">
                <x-icon name="menu" />
            </button>
            <h1>{{ $title }}</h1>
            <div class="admin-topbar-actions">
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="admin-topbar-link">{{ __('admin.back_to_site') }}</a>
                <button type="button" class="admin-notif" aria-label="{{ __('admin.nav.notifications') }}">
                    <x-icon name="bell" />
                </button>
                <div class="admin-profile-menu" id="adminProfileMenu">
                    <button type="button" class="admin-profile-trigger" id="adminProfileTrigger" aria-expanded="false" aria-haspopup="menu">
                        <x-avatar :user="$user" class="avatar-sm" />
                        <span class="admin-profile-name">{{ $user->name }}</span>
                        <x-icon name="chevron-down" class="admin-profile-caret" />
                    </button>
                    <div class="admin-dropdown" role="menu">
                        <div class="admin-dropdown-head">
                            <x-avatar :user="$user" class="avatar-md" />
                            <div>
                                <strong>{{ $user->name }}</strong>
                                <small>{{ $user->role_label }}</small>
                            </div>
                        </div>
                        <div class="admin-dropdown-divider"></div>
                        <a href="{{ route('admin.profile') }}" role="menuitem">
                            <x-icon name="user" /> {{ __('admin.profile.my_profile') }}
                        </a>
                        <a href="{{ route('admin.profile', '#security') }}" role="menuitem">
                            <x-icon name="key" /> {{ __('admin.profile.change_password') }}
                        </a>
                        <a href="{{ route('admin.settings.edit') }}" role="menuitem">
                            <x-icon name="settings" /> {{ __('admin.profile.account_settings') }}
                        </a>
                        <div class="admin-dropdown-divider"></div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" role="menuitem" class="admin-dropdown-logout">
                                <x-icon name="logout" /> {{ __('admin.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="admin-content">
            <x-flash />
            {{ $slot }}
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
