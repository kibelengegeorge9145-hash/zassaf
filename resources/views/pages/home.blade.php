<x-layouts.public>
    {{-- HERO --}}
    <section class="hero">
        <div class="hero-glow hero-glow-a" aria-hidden="true"></div>
        <div class="hero-glow hero-glow-b" aria-hidden="true"></div>
        <div class="container hero-inner">
            <p class="hero-kicker">{{ __('home.hero_kicker') }}</p>
            <h1 class="hero-title">Zassaf&nbsp;Elite <span class="hero-title-sub">Community</span></h1>
            <p class="hero-motto">{{ __('home.hero_motto') }}</p>
            <p class="hero-sub">{{ __('home.hero_subtitle') }}</p>
            <div class="hero-actions">
                <a href="{{ route('community') }}" class="btn btn-gold btn-lg">{{ __('home.hero_join') }}</a>
                <a href="{{ route('programs') }}" class="btn btn-outline-light btn-lg">{{ __('home.hero_explore') }}</a>
            </div>
            <div class="hero-scroll" aria-hidden="true"><span></span></div>
        </div>
    </section>

    {{-- WHO WE ARE --}}
    <section class="section section-light">
        <div class="container who-grid">
            <div class="who-intro">
                <p class="kicker">{{ __('home.who_kicker') }}</p>
                <h2>{{ __('home.who_heading') }}</h2>
            </div>
            <div class="who-body">
                <p class="who-main">{{ __('home.who_text') }}</p>
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

    {{-- PROGRAMS PREVIEW --}}
    <section class="section section-light">
        <div class="container">
            <x-section-heading :title="__('home.programs_heading')" :sub="__('home.programs_sub')" />

            <div class="card-grid">
                @forelse ($programs as $program)
                    <article class="card program-card">
                        <div class="program-icon"><x-icon :name="$program->icon" /></div>
                        <h3>{{ $program->title }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($program->description, 130) }}</p>
                        <a href="{{ route('programs') }}" class="link-arrow">{{ __('site.cta.learn_more') }} <x-icon name="arrow-right" /></a>
                    </article>
                @empty
                    <p class="empty-note">{{ __('programs.empty') }}</p>
                @endforelse
            </div>

            <div class="section-center">
                <a href="{{ route('programs') }}" class="btn btn-outline-dark">{{ __('home.programs_more') }}</a>
            </div>
        </div>
    </section>

    {{-- WEEKEND CONVO --}}
    <section class="section section-dark convo-preview">
        <div class="container">
            <div class="convo-grid">
                <div class="convo-intro">
                    <p class="kicker">{{ __('site.nav.weekend_convo') }}</p>
                    <h2>{{ __('home.weekend_heading') }}</h2>
                    <p>{{ __('home.weekend_sub') }}</p>
                    <blockquote class="convo-quote">
                        <x-icon name="quote" />
                        <p>{{ __('home.weekend_preview_question') }}</p>
                    </blockquote>
                    <a href="{{ route('weekend-convo') }}" class="btn btn-gold">{{ __('home.weekend_more') }}</a>
                </div>

                @if ($upcomingConvo)
                    <article class="card convo-card">
                        <div class="convo-card-head">
                            <span class="chip chip-gold">{{ __('weekend.upcoming_heading') }}</span>
                            <span class="convo-date">
                                <x-icon name="calendar" />
                                {{ $upcomingConvo->event_date?->format('d M Y') ?? __('weekend.empty') }}
                            </span>
                        </div>
                        <h3>{{ $upcomingConvo->title }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($upcomingConvo->description, 120) }}</p>
                        <ul class="convo-meta">
                            @if ($upcomingConvo->event_time)
                                <li><x-icon name="clock" /> {{ $upcomingConvo->event_time }}</li>
                            @endif
                            @if ($upcomingConvo->platform)
                                <li><x-icon name="video" /> {{ $upcomingConvo->platform }}</li>
                            @endif
                            @if ($upcomingConvo->speaker)
                                <li><x-icon name="mic" /> {{ $upcomingConvo->speaker }}</li>
                            @endif
                        </ul>
                        <a href="{{ route('community') }}" class="btn btn-gold btn-block">{{ __('site.cta.join_convo') }}</a>
                    </article>
                @else
                    <article class="card convo-card">
                        <span class="chip chip-gold">{{ __('weekend.upcoming_heading') }}</span>
                        <h3>{{ __('weekend.empty') }}</h3>
                        <a href="{{ route('community') }}" class="btn btn-gold btn-block">{{ __('site.cta.join_convo') }}</a>
                    </article>
                @endif
            </div>
        </div>
    </section>

    {{-- BOOKS FEATURED --}}
    @if ($featuredBook)
        <section class="section section-light">
            <div class="container">
                <div class="featured-book">
                    <div class="featured-book-cover">
                        <x-book-cover :title="$featuredBook->title" :author="$featuredBook->author" :status="$featuredBook->status" :cover-url="$featuredBook->cover_url" size="large" />
                    </div>
                    <div class="featured-book-body">
                        <p class="kicker">{{ __('books.featured_heading') }}</p>
                        <h2>{{ $featuredBook->title }}</h2>
                        <p class="featured-book-author">{{ __('books.by') }} {{ $featuredBook->author }}</p>
                        <p>{{ $featuredBook->description }}</p>
                        <div class="featured-book-meta">
                            <span class="chip">{{ __('books.status.' . $featuredBook->status) }}</span>
                            @if ($featuredBook->publication_date)
                                <span>{{ __('books.release') }}: {{ $featuredBook->publication_date->format('M Y') }}</span>
                            @endif
                            @if ($featuredBook->price !== null)
                                <span>{{ __('books.price') }}: <strong>{{ $featuredBook->formatted_price }}</strong></span>
                            @endif
                        </div>
                        @if ($featuredBook->preorder_enabled)
                            <a href="{{ setting('whatsapp_url', '#') }}" target="_blank" rel="noopener noreferrer" class="btn btn-gold">{{ __('site.cta.preorder') }}</a>
                        @endif
                        <a href="{{ route('books') }}" class="btn btn-outline-dark">{{ __('site.cta.view_all') }}</a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- COMMUNITY / MEMBERSHIP --}}
    <section class="section section-black community-band">
        <div class="container">
            <div class="community-band-grid">
                <div>
                    <p class="kicker">{{ __('site.nav.community') }}</p>
                    <h2>{{ __('home.community_heading') }}</h2>
                    <p>{{ __('home.community_text') }}</p>
                    <a href="{{ route('community') }}" class="btn btn-gold">{{ __('home.community_cta') }}</a>
                </div>
                <div class="membership-card">
                    <p class="kicker">{{ __('home.membership_heading') }}</p>
                    <p>{{ __('home.membership_text') }}</p>
                    <div class="membership-plans">
                        @foreach ($plans as $plan)
                            <div class="membership-plan">
                                <span>{{ $plan->name }}</span>
                                <strong>{{ $plan->formatted_price }}</strong>
                            </div>
                        @endforeach
                    </div>
                    <a href="#register" class="btn btn-gold btn-block">{{ __('home.membership_register') }}</a>
                </div>
            </div>
        </div>
    </section>

    {{-- REGISTRATION --}}
    <x-registration-form :programs="$programs" :events="$upcomingEvents" :include-membership="true" />

</x-layouts.public>
