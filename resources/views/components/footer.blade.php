@php
    $brandLogoPath = setting('logo_path');
    $brandLogoUrl = $brandLogoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($brandLogoPath)
        ? asset('storage/'.$brandLogoPath)
        : null;

    $socials = [
        ['label' => __('contact.whatsapp'), 'icon' => 'whatsapp', 'url' => setting('whatsapp_url')],
        ['label' => __('contact.email'), 'icon' => 'mail', 'url' => setting('email') ? 'mailto:' . setting('email') : null],
        ['label' => __('contact.instagram'), 'icon' => 'instagram', 'url' => setting('instagram_url')],
        ['label' => __('contact.facebook'), 'icon' => 'facebook', 'url' => setting('facebook_url')],
        ['label' => __('contact.tiktok'), 'icon' => 'tiktok', 'url' => setting('tiktok_url')],
        ['label' => __('contact.telegram'), 'icon' => 'telegram', 'url' => setting('telegram_url')],
    ];

    $quickLinks = [
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

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="brand footer-brand-logo" aria-label="{{ setting('org_name', 'Zassaf Elite Community') }}">
                    @if ($brandLogoUrl)
                        <img src="{{ $brandLogoUrl }}" alt="{{ setting('org_name', 'Zassaf Elite Community') }}" class="brand-logo">
                    @else
                        <span class="brand-mark" aria-hidden="true">Z</span>
                    @endif
                    <span class="brand-text">
                        <span class="brand-name">{{ setting('org_name', __('site.brand_name')) }}</span>
                        <span class="brand-sub">Think. Grow. Lead.</span>
                    </span>
                </a>
                <p class="footer-tagline">{{ setting('tagline', __('site.tagline')) }}</p>
                <p class="footer-open">{{ __('site.footer.open_free') }}</p>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">{{ __('site.footer.quick_links') }}</h4>
                <ul class="footer-links">
                    @foreach ($quickLinks as $link)
                        <li><a href="{{ route($link['route']) }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading">{{ __('site.footer.connect') }}</h4>
                <ul class="footer-links footer-socials">
                    @foreach ($socials as $social)
                        @if ($social['url'])
                            <li>
                                <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer">
                                    <x-icon :name="$social['icon']" />
                                    <span>{{ $social['label'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>{{ __('site.footer.rights', ['year' => now()->format('Y')]) }}</p>
            <div class="footer-legal">
                <a href="{{ route('privacy') }}">{{ __('site.footer.privacy') }}</a>
                <a href="{{ route('terms') }}">{{ __('site.footer.terms') }}</a>
            </div>
        </div>
    </div>
</footer>
