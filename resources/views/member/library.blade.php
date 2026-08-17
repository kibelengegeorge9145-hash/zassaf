<x-layouts.member :title="__('books.library.heading')">
    <div class="admin-section admin-toolbar">
        <div>
            <h2>{{ __('books.library.heading') }}</h2>
            <p class="admin-sub">{{ __('books.library.sub') }}</p>
        </div>
        <a href="{{ route('books') }}" class="btn btn-gold">
            <x-icon name="plus" /> {{ __('books.library.browse') }}
        </a>
    </div>

    @if ($purchases->isEmpty())
        <div class="admin-panel">
            <p class="empty-note">{{ __('books.library.empty') }}</p>
            <a href="{{ route('books') }}" class="btn btn-gold">{{ __('books.library.browse') }}</a>
        </div>
    @else
        <div class="books-grid">
            @foreach ($purchases as $purchase)
                <article class="card book-card">
                    <a href="{{ route('books.show', $purchase->book) }}">
                        <x-book-cover :title="$purchase->book->title" :author="$purchase->book->author" :status="$purchase->book->status" :cover-url="$purchase->book->cover_url" />
                    </a>
                    <div class="book-card-body">
                        <span class="chip chip-gold">{{ __('books.you_own') }}</span>
                        <h3><a href="{{ route('books.show', $purchase->book) }}">{{ $purchase->book->title }}</a></h3>
                        <p class="book-author">{{ __('books.by') }} {{ $purchase->book->author }}</p>
                        @if ($purchase->purchased_at)
                            <p class="book-author">{{ __('books.library.purchased_on', ['date' => $purchase->purchased_at->format('d M Y')]) }}</p>
                        @endif
                        @if ($purchase->book->hasFile())
                            <div class="book-card-actions">
                                <a href="{{ route('books.read', $purchase->book) }}" class="btn btn-gold btn-sm">
                                    <x-icon name="book-open" /> {{ __('books.read_online') }}
                                </a>
                                <a href="{{ route('books.download', $purchase->book) }}" class="btn btn-outline-dark btn-sm">
                                    <x-icon name="download" /> {{ __('books.download') }}
                                </a>
                            </div>
                        @else
                            <p class="book-author">{{ __('books.library.pdf_pending') }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-layouts.member>
