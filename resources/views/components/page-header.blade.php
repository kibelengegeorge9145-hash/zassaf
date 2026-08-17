@props(['kicker' => null, 'title' => null, 'sub' => null])

<section class="page-hero">
    <div class="hero-glow hero-glow-a" aria-hidden="true"></div>
    <div class="container page-hero-inner">
        @if ($kicker)
            <p class="kicker">{{ $kicker }}</p>
        @endif
        @if ($title)
            <h1>{{ $title }}</h1>
        @endif
        @if ($sub)
            <p class="page-hero-sub">{{ $sub }}</p>
        @endif
    </div>
</section>
