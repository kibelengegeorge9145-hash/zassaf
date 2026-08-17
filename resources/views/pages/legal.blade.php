@php
    $content = __('legal.'.$type);
    $title = $type === 'privacy' ? __('site.footer.privacy') : __('site.footer.terms');
    $seo = $type === 'privacy' ? __('seo.privacy') : __('seo.terms');
@endphp

<x-layouts.public
    :title="$seo['title']"
    :description="$seo['desc']"
>
    <x-page-header :title="$title" :sub="$content['updated']" />

    <section class="section section-light">
        <div class="container container-narrow">
            <article class="prose prose-legal">
                <p>{{ $content['intro'] }}</p>

                @foreach ($content['sections'] as $section)
                    <h2>{{ $section['title'] }}</h2>
                    <p>{{ $section['body'] }}</p>
                @endforeach
            </article>
        </div>
    </section>
</x-layouts.public>
