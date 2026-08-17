<x-layouts.public
    :title="__('404.title')"
    :description="__('404.desc')"
>
    <section class="section section-light not-found">
        <div class="container section-center">
            <p class="not-found-code">404</p>
            <h1 class="not-found-title">{{ __('404.title') }}</h1>
            <p class="section-sub">{{ __('404.desc') }}</p>
            <div class="hero-actions">
                <a href="{{ route('home') }}" class="btn btn-gold btn-lg">{{ __('site.nav.home') }}</a>
                <a href="{{ route('community') }}" class="btn btn-outline-dark btn-lg">{{ __('site.cta.join_community') }}</a>
            </div>
        </div>
    </section>
</x-layouts.public>
