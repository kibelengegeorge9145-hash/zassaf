<x-layouts.public
    :title="__('seo.events.title')"
    :description="__('seo.events.desc')"
>
    <x-page-header
        :kicker="__('site.nav.programs')"
        :title="__('events.heading')"
        :sub="__('events.sub')"
    />

    <section class="section section-light">
        <div class="container">
            <x-section-heading :title="__('events.upcoming')" align="left" />

            @forelse ($events as $event)
                <article class="card event-row">
                    <div class="event-date">
                        <span class="event-day">{{ $event->event_date->format('d') }}</span>
                        <span class="event-month">{{ $event->event_date->format('M') }}</span>
                    </div>
                    <div class="event-body">
                        <h3>{{ $event->title }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($event->description, 140) }}</p>
                        <ul class="convo-meta">
                            @if ($event->event_date)
                                <li><x-icon name="calendar" /> {{ $event->event_date->format('d M Y') }}</li>
                            @endif
                            @if ($event->event_time)
                                <li><x-icon name="clock" /> {{ $event->event_time }}</li>
                            @endif
                            @if ($event->location)
                                <li><x-icon name="map-pin" /> {{ $event->location }}</li>
                            @endif
                        </ul>
                    </div>
                    <div class="event-cta">
                        <a href="#register" class="btn btn-outline-dark">{{ __('events.register') }}</a>
                    </div>
                </article>
            @empty
                <p class="empty-note">{{ __('events.empty') }}</p>
            @endforelse
        </div>
    </section>

    <x-registration-form :programs="[]" :events="$events" :include-membership="true" />
</x-layouts.public>
