<x-layouts.public
    :title="$book->title"
    :description="Str::limit($book->description, 160)"
>
    <x-page-header
        :kicker="__('site.nav.books')"
        :title="$book->title"
        :sub="__('books.by_author', ['author' => $book->author])"
    />

    <section class="section section-light">
        <div class="container">
            <div class="featured-book">
                <div class="featured-book-cover">
                    <x-book-cover :title="$book->title" :author="$book->author" :status="$book->status" :cover-url="$book->cover_url" size="large" />
                </div>
                <div class="featured-book-body">
                    <p class="kicker">{{ __('books.details') }}</p>
                    <h2>{{ $book->title }}</h2>
                    <p class="featured-book-author">{{ __('books.by_author', ['author' => $book->author]) }}</p>
                    <p>{{ $book->description }}</p>

                    <div class="featured-book-meta">
                        <span class="chip chip-gold">{{ __('books.status.' . $book->status) }}</span>
                        @if ($book->publication_date)
                            <span><x-icon name="calendar" /> {{ __('books.release') }}: {{ $book->publication_date->format('M Y') }}</span>
                        @endif
                        @if ($book->price !== null)
                            <span><x-icon name="check" /> {{ __('books.price') }}: <strong>{{ $book->formatted_price }}</strong></span>
                        @endif
                    </div>

                    <div class="featured-book-actions">
                        @if ($owned)
                            <a href="{{ route('member.library') }}" class="btn btn-gold">{{ __('books.you_own') }}</a>
                            <a href="{{ route('books.read', $book) }}" class="btn btn-outline-dark">{{ __('books.read_online') }}</a>
                            <a href="{{ route('books.download', $book) }}" class="btn btn-outline-dark">{{ __('books.download') }}</a>
                        @elseif ($pendingPayment)
                            <a href="{{ route('books.checkout', $book) }}" class="btn btn-gold">{{ __('books.pending_button') }}</a>
                        @elseif ($book->isPurchasable())
                            <a href="{{ route('books.purchase', $book) }}" class="btn btn-gold">{{ __('books.buy_now') }} · {{ $book->formatted_price }}</a>
                        @elseif ($book->preorder_enabled && setting('whatsapp_url'))
                            <a href="{{ setting('whatsapp_url') }}" target="_blank" rel="noopener noreferrer" class="btn btn-gold">{{ __('books.whatsapp_order') }}</a>
                        @else
                            <a href="{{ route('community') }}" class="btn btn-gold">{{ __('books.notify') }}</a>
                        @endif

                        <a href="{{ route('books') }}" class="btn btn-ghost">{{ __('books.back') }}</a>
                    </div>

                    @if ($owned)
                        <p class="featured-book-note">{{ __('books.own_note') }}</p>
                    @elseif ($pendingPayment)
                        <p class="featured-book-note">{{ __('books.pending_note') }}</p>
                    @elseif (! $book->isPurchasable())
                        <p class="featured-book-note">{{ __('books.not_purchasable') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
