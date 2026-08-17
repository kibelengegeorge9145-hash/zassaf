<x-layouts.public
    :title="__('seo.programs.title')"
    :description="__('seo.programs.desc')"
>
    <x-page-header
        :kicker="__('site.nav.programs')"
        :title="__('programs.heading')"
        :sub="__('programs.sub')"
    />

    <section class="section section-light">
        <div class="container">
            <div class="card-grid">
                @forelse ($programs as $program)
                    <article class="card program-card">
                        <div class="program-icon"><x-icon :name="$program->icon" /></div>
                        <h3>{{ $program->title }}</h3>
                        <p>{{ $program->description }}</p>
                        <a href="{{ route('community') }}" class="link-arrow">{{ __('programs.learn_more') }} <x-icon name="arrow-right" /></a>
                    </article>
                @empty
                    <p class="empty-note">{{ __('programs.empty') }}</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section section-black">
        <div class="container section-center">
            <h2 class="section-title-inverse">{{ __('programs.question_heading') }}</h2>
            <p class="section-sub-inverse">{{ __('programs.question_text') }}</p>
            <a href="{{ route('community') }}" class="btn btn-gold btn-lg">{{ __('programs.question_cta') }}</a>
        </div>
    </section>
</x-layouts.public>
