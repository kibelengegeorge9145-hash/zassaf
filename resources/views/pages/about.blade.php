    @php
        $valueIcons = [
            'knowledge' => 'lightbulb',
            'growth' => 'rocket',
            'leadership' => 'shield',
            'community' => 'users',
        ];
    @endphp

    <x-layouts.public
        :title="__('seo.about.title')"
        :description="__('seo.about.desc')"
    >
    <x-page-header
        :kicker="__('site.nav.about')"
        :title="__('home.who_kicker')"
        :sub="__('site.tagline')"
    />

    {{-- WHO WE ARE --}}
    <section class="section section-light">
        <div class="container container-narrow">
            <x-section-heading :title="__('home.who_heading')" />

            <div class="prose">
                <p>{{ __('home.who_text') }}</p>
                <p class="who-alt" lang="{{ app()->getLocale() === 'sw' ? 'en' : 'sw' }}">
                    {{ app()->getLocale() === 'sw' ? __('home.who_text_en') : __('home.who_text_sw') }}
                </p>
            </div>
        </div>
    </section>

    {{-- VISION & MISSION --}}
    <section class="section section-offset">
        <div class="container">
            <div class="vm-grid">
                <article class="card vm-card">
                    <div class="vm-icon"><x-icon name="eye" /></div>
                    <h3>{{ __('home.vision_heading') }}</h3>
                    <p>{{ __('home.vision_text') }}</p>
                </article>
                <article class="card vm-card vm-card-dark">
                    <div class="vm-icon"><x-icon name="target" /></div>
                    <h3>{{ __('home.mission_heading') }}</h3>
                    <p>{{ __('home.mission_text') }}</p>
                </article>
            </div>
        </div>
    </section>

    {{-- VALUES --}}
    <section class="section section-light">
        <div class="container">
            <x-section-heading :title="__('home.values_heading')" />

            <div class="card-grid card-grid-4">
                @foreach (__('home.values') as $key => $value)
                    <article class="card value-card">
                        <div class="program-icon"><x-icon :name="$valueIcons[$key] ?? 'sparkles'" /></div>
                        <h3>{{ $value['title'] }}</h3>
                        <p>{{ $value['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="section section-black">
        <div class="container section-center">
            <h2 class="section-title-inverse">{{ __('programs.question_heading') }}</h2>
            <p class="section-sub-inverse">{{ __('programs.question_text') }}</p>
            <a href="{{ route('community') }}" class="btn btn-gold btn-lg">{{ __('programs.question_cta') }}</a>
        </div>
    </section>
</x-layouts.public>
