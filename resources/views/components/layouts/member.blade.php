@props(['title' => 'Dashboard'])

@php
    $user = auth()->user();

    $groups = [
        ['label' => __('membership.nav.member_area'), 'items' => [
            ['label' => __('membership.nav.dashboard'), 'route' => 'member.dashboard', 'pattern' => 'member.dashboard', 'icon' => 'grid'],
            ['label' => __('membership.nav.membership'), 'route' => 'member.membership', 'pattern' => 'member.membership', 'icon' => 'users'],
            ['label' => __('membership.nav.library'), 'route' => 'member.library', 'pattern' => 'member.library', 'icon' => 'book-open'],
            ['label' => __('membership.nav.payments'), 'route' => 'member.payments.index', 'pattern' => 'member.payments.*', 'icon' => 'receipt'],
            ['label' => __('membership.nav.profile'), 'route' => 'member.profile', 'pattern' => 'member.profile*', 'icon' => 'user'],
        ]],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} · {{ __('membership.nav.member_area') }} — Zassaf</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('head')
</head>
<body class="admin-body">
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="{{ route('member.dashboard') }}" class="brand admin-brand">
            <span class="brand-mark" aria-hidden="true">Z</span>
            <span class="brand-text">
                <span class="brand-name">Zassaf Elite</span>
                <span class="brand-sub">{{ __('membership.nav.member_area') }}</span>
            </span>
        </a>

        <nav class="admin-nav" aria-label="{{ __('membership.nav.member_area') }}">
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
            <form method="POST" action="{{ route('member.logout') }}">
                @csrf
                <button type="submit" class="admin-nav-logout">
                    <x-icon name="logout" />
                    <span>{{ __('membership.nav.logout') }}</span>
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
                        <a href="{{ route('member.profile') }}" role="menuitem">
                            <x-icon name="user" /> {{ __('membership.nav.profile') }}
                        </a>
                        <div class="admin-dropdown-divider"></div>
                        <form method="POST" action="{{ route('member.logout') }}">
                            @csrf
                            <button type="submit" role="menuitem" class="admin-dropdown-logout">
                                <x-icon name="logout" /> {{ __('membership.nav.logout') }}
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
