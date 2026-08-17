<x-layouts.public
    :title="__('seo.books.title')"
    :description="__('seo.books.desc')"
>
    <x-page-header
        :kicker="__('site.nav.books')"
        :title="__('books.heading')"
        :sub="__('books.sub')"
    />

    {{-- FEATURED --}}
    @if ($featured)
        <section class="section section-light">
            <div class="container">
                <div class="featured-book">
                    <div class="featured-book-cover">
                        <a href="{{ route('books.show', $featured) }}">
                            <x-book-cover :title="$featured->title" :author="$featured->author" :status="$featured->status" :cover-url="$featured->cover_url" size="large" />
                        </a>
                    </div>
                    <div class="featured-book-body">
                        <p class="kicker">{{ __('books.featured_heading') }}</p>
                        <h2>{{ $featured->title }}</h2>
                        <p class="featured-book-author">{{ __('books.by') }} {{ $featured->author }}</p>
                        <p>{{ $featured->description }}</p>

                        <div class="featured-book-meta">
                            <span class="chip chip-gold">{{ __('books.status.' . $featured->status) }}</span>
                            @if ($featured->publication_date)
                                <span><x-icon name="calendar" /> {{ __('books.release') }}: {{ $featured->publication_date->format('M Y') }}</span>
                            @endif
                            @if ($featured->price !== null)
                                <span><x-icon name="check" /> {{ __('books.price') }}: <strong>{{ $featured->formatted_price }}</strong></span>
                            @endif
                        </div>

                        <div class="featured-book-actions">
                            <a href="{{ route('books.show', $featured) }}" class="btn btn-gold">{{ __('books.details') }}</a>
                            @if ($featured->preorder_enabled && setting('whatsapp_url'))
                                <a href="{{ setting('whatsapp_url') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark">{{ __('site.cta.preorder') }}</a>
                            @endif
                            <a href="{{ route('community') }}" class="btn btn-outline-dark">{{ __('books.notify') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- OTHER PUBLISHED --}}
    @if ($others->isNotEmpty())
        <section class="section section-offset">
            <div class="container">
                <div class="books-grid">
                    @foreach ($others as $book)
                        <article class="card book-card">
                            <a href="{{ route('books.show', $book) }}">
                                <x-book-cover :title="$book->title" :author="$book->author" :status="$book->status" :cover-url="$book->cover_url" />
                            </a>
                            <div class="book-card-body">
                                <span class="chip">{{ __('books.status.' . $book->status) }}</span>
                                <h3><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></h3>
                                <p class="book-author">{{ __('books.by') }} {{ $book->author }}</p>
                                <p>{{ \Illuminate\Support\Str::limit($book->description, 110) }}</p>
                                @if ($book->isPurchasable())
                                    <a href="{{ route('books.show', $book) }}" class="link-arrow">{{ __('books.details') }}</a>
                                @elseif ($book->preorder_enabled && setting('whatsapp_url'))
                                    <a href="{{ setting('whatsapp_url') }}" target="_blank" rel="noopener noreferrer" class="btn btn-gold btn-sm">{{ __('site.cta.preorder') }}</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- COMING SOON --}}
    <section class="section section-dark">
        <div class="container">
            <x-section-heading :title="__('books.coming_soon_heading')" :sub="__('books.coming_soon_sub')" />

            @if ($comingSoon->isNotEmpty())
                <div class="books-grid">
                    @foreach ($comingSoon as $book)
                        <article class="card book-card book-card-dark">
                            <a href="{{ route('books.show', $book) }}">
                                <x-book-cover :title="$book->title" :author="$book->author" :status="'coming_soon'" :cover-url="$book->cover_url" />
                            </a>
                            <div class="book-card-body">
                                <span class="chip chip-gold">{{ __('books.status.coming_soon') }}</span>
                                <h3><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></h3>
                                <p class="book-author">{{ __('books.by') }} {{ $book->author }}</p>
                                <p>{{ \Illuminate\Support\Str::limit($book->description, 110) }}</p>
                                <a href="{{ route('books.show', $book) }}" class="link-arrow">{{ __('books.details') }}</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <p class="empty-note">{{ __('books.empty') }}</p>
            @endif
        </div>
    </section>
</x-layouts.public>
