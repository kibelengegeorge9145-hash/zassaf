<x-layouts.public
    :title="__('seo.weekend.title')"
    :description="__('seo.weekend.desc')"
>
    <x-page-header
        :kicker="__('site.nav.weekend_convo')"
        :title="__('weekend.heading')"
        :sub="__('weekend.sub')"
    />

    {{-- WHAT IS IT --}}
    <section class="section section-light">
        <div class="container">
            <div class="two-col">
                <div>
                    <p class="kicker">{{ __('site.nav.weekend_convo') }}</p>
                    <h2>{{ __('weekend.what_heading') }}</h2>
                    <p>{{ __('weekend.what_text') }}</p>
                    <h3 class="sub-heading">{{ __('weekend.why_heading') }}</h3>
                    <ul class="check-list">
                        @foreach (__('weekend.why_points') as $point)
                            <li><x-icon name="check" /> {{ $point }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="convo-visual" aria-hidden="true">
                    <div class="convo-visual-rings">
                        <span></span><span></span><span></span>
                    </div>
                    <div class="convo-visual-mic"><x-icon name="mic" /></div>
                </div>
            </div>
        </div>
    </section>

    {{-- TOPICS --}}
    <section class="section section-offset">
        <div class="container">
            <x-section-heading :title="__('weekend.topics_heading')" :sub="__('weekend.topics_sub')" />

            <div class="chip-cloud">
                @php
                    $topicWords = [
                        __('weekend.topics_heading'),
                        __('home.values.knowledge.title'),
                        __('home.values.leadership.title'),
                        __('home.values.growth.title'),
                        __('home.mission_heading'),
                        __('programs.heading'),
                        __('site.nav.community'),
                    ];
                    $topics = collect(array_merge(
                        $upcoming->pluck('topics')->flatten()->all(),
                        ['Personal Growth', 'Leadership', 'Careers', 'Entrepreneurship', 'Society']
                    ))->unique()->take(10);
                @endphp
                @foreach ($topics as $topic)
                    @if ($topic)
                        <span class="chip chip-outline">{{ $topic }}</span>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    {{-- UPCOMING --}}
    <section class="section section-light">
        <div class="container">
            <x-section-heading :title="__('weekend.upcoming_heading')" />

            @forelse ($upcoming as $convo)
                <article class="card convo-card-wide">
                    <div class="convo-wide-date">
                        <span class="convo-wide-day">{{ $convo->event_date?->format('d') ?? '--' }}</span>
                        <span class="convo-wide-month">{{ $convo->event_date?->format('M Y') ?? __('weekend.empty') }}</span>
                    </div>
                    <div class="convo-wide-body">
                        <h3>{{ $convo->title }}</h3>
                        <p>{{ $convo->description }}</p>

                        @if ($convo->topics)
                            <div class="chip-cloud chip-cloud-sm">
                                @foreach ($convo->topics as $topic)
                                    <span class="chip chip-outline">{{ $topic }}</span>
                                @endforeach
                            </div>
                        @endif

                        <ul class="convo-meta">
                            @if ($convo->event_date)
                                <li><x-icon name="calendar" /> {{ $convo->event_date->format('d M Y') }}</li>
                            @endif
                            @if ($convo->event_time)
                                <li><x-icon name="clock" /> {{ $convo->event_time }}</li>
                            @endif
                            @if ($convo->platform)
                                <li><x-icon name="video" /> {{ $convo->platform }}</li>
                            @endif
                            @if ($convo->speaker)
                                <li><x-icon name="mic" /> {{ $convo->speaker }}</li>
                            @endif
                        </ul>
                    </div>
                    <div class="convo-wide-cta">
                        <a href="{{ route('community') }}" class="btn btn-gold">{{ __('weekend.register') }}</a>
                    </div>
                </article>
            @empty
                <p class="empty-note">{{ __('weekend.empty') }}</p>
            @endforelse
        </div>
    </section>

    {{-- PREVIOUS --}}
    @if ($past->isNotEmpty())
        <section class="section section-offset">
            <div class="container">
                <x-section-heading :title="__('weekend.previous_heading')" />

                <div class="past-list">
                    @foreach ($past as $convo)
                        <article class="past-item">
                            <div class="past-item-date">
                                <x-icon name="calendar" />
                                <span>{{ $convo->event_date?->format('d M Y') ?? '—' }}</span>
                            </div>
                            <div>
                                <h3>{{ $convo->title }}</h3>
                                @if ($convo->speaker)
                                    <p><x-icon name="mic" /> {{ $convo->speaker }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section section-black">
        <div class="container section-center">
            <h2 class="section-title-inverse">{{ __('programs.question_heading') }}</h2>
            <p class="section-sub-inverse">{{ __('programs.question_text') }}</p>
            <a href="{{ route('community') }}" class="btn btn-gold btn-lg">{{ __('programs.question_cta') }}</a>
        </div>
    </section>
</x-layouts.public>
