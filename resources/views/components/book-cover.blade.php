@props(['title' => '', 'author' => '', 'status' => 'coming_soon', 'coverUrl' => null, 'size' => 'normal'])

@if ($coverUrl)
    <img src="{{ $coverUrl }}" alt="{{ $title }}" loading="lazy" class="book-cover book-cover-img {{ $size }}">
@else
    <div class="book-cover {{ $size }}" data-status="{{ $status }}" aria-hidden="true">
        <span class="book-cover-mark">Z</span>
        <span class="book-cover-title">{{ $title }}</span>
        @if ($author)
            <span class="book-cover-author">{{ $author }}</span>
        @endif
        <span class="book-cover-brand">ZASSAF&nbsp;ELITE</span>
    </div>
@endif
