@php
    $brandLogoPath = setting('logo_path');
    $brandLogoUrl = $brandLogoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($brandLogoPath)
        ? asset('storage/'.$brandLogoPath)
        : null;

    $currentLocale = app()->getLocale();
    $otherLocale = $currentLocale === 'sw' ? 'en' : 'sw';

    $navLinks = [
        ['label' => __('site.nav.home'), 'route' => 'home'],
        ['label' => __('site.nav.about'), 'route' => 'about'],
        ['label' => __('site.nav.programs'), 'route' => 'programs'],
        ['label' => __('site.nav.weekend_convo'), 'route' => 'weekend-convo'],
        ['label' => __('site.nav.books'), 'route' => 'books'],
        ['label' => __('site.nav.community'), 'route' => 'community'],
        ['label' => __('site.nav.membership'), 'route' => 'membership'],
        ['label' => __('site.nav.contact'), 'route' => 'contact'],
    ];
@endphp

<header class="site-header" id="siteHeader">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="brand" aria-label="{{ setting('org_name', 'Zassaf Elite Community') }}">
            @if ($brandLogoUrl)
                <img src="{{ $brandLogoUrl }}" alt="{{ setting('org_name', 'Zassaf Elite Community') }}" class="brand-logo">
            @else
                <span class="brand-mark" aria-hidden="true">Z</span>
            @endif
            <span class="brand-text">
                <span class="brand-name">{{ __('site.brand_short') }}</span>
                <span class="brand-sub">{{ setting('motto', __('site.motto')) }}</span>
            </span>
        </a>

        <nav class="site-nav" id="siteNav" aria-label="Primary">
            <ul>
                @foreach ($navLinks as $link)
                    <li>
                        <a href="{{ route($link['route']) }}"
                           class="{{ request()->routeIs($link['route']) ? 'is-active' : '' }}">{{ $link['label'] }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="header-actions">
            <div class="lang-switch" role="group" aria-label="{{ __('site.lang.label') }}">
                <a href="{{ route('locale.switch', ['locale' => 'en']) }}"
                   class="{{ $currentLocale === 'en' ? 'is-active' : '' }}"
                   aria-current="{{ $currentLocale === 'en' ? 'true' : 'false' }}">{{ __('site.lang.en') }}</a>
                <span class="lang-sep" aria-hidden="true">|</span>
                <a href="{{ route('locale.switch', ['locale' => 'sw']) }}"
                   class="{{ $currentLocale === 'sw' ? 'is-active' : '' }}"
                   aria-current="{{ $currentLocale === 'sw' ? 'true' : 'false' }}">{{ __('site.lang.sw') }}</a>
            </div>

            <a href="{{ route('community') }}" class="btn btn-gold btn-sm header-cta">{{ __('site.cta.join') }}</a>

            <button class="nav-toggle" id="navToggle" type="button" aria-controls="siteNav" aria-expanded="false" aria-label="Menu">
                <span class="nav-toggle-open"><x-icon name="menu" /></span>
                <span class="nav-toggle-close"><x-icon name="close" /></span>
            </button>
        </div>
    </div>
</header>
