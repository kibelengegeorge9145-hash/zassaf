@props(['kicker' => null, 'title' => null, 'sub' => null, 'align' => 'center'])

<div class="section-head {{ $align === 'left' ? 'is-left' : '' }}">
    @if ($kicker)
        <p class="kicker">{{ $kicker }}</p>
    @endif
    @if ($title)
        <h2>{{ $title }}</h2>
    @endif
    @if ($sub)
        <p class="section-sub">{{ $sub }}</p>
    @endif
</div>
