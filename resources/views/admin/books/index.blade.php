<x-layouts.admin :title="__('admin.books.heading')">
    <div class="admin-section admin-toolbar">
        <div>
            <h2>{{ __('admin.books.heading') }}</h2>
            <p class="admin-sub">{{ __('admin.books.index_sub') }}</p>
        </div>
        <div class="admin-toolbar-actions">
            <a href="{{ route('admin.books.purchases') }}" class="btn btn-ghost">
                <x-icon name="receipt" /> {{ __('admin.books.purchases.heading') }}
            </a>
            <a href="{{ route('admin.books.create') }}" class="btn btn-gold">
                <x-icon name="plus" /> {{ __('admin.books.create') }}
            </a>
        </div>
    </div>

    <div class="admin-panel">
        @forelse ($books as $book)
            <div class="admin-row">
                <div class="admin-row-main">
                    <span class="admin-row-thumb">
                        @if ($book->cover_url)
                            <img src="{{ $book->cover_url }}" alt="" loading="lazy">
                        @else
                            <x-icon name="book-open" />
                        @endif
                    </span>
                    <div>
                        <strong>{{ $book->title_en }}</strong>
                        <small>{{ $book->author }} · {{ __('admin.books.statuses.' . $book->status) }}</small>
                    </div>
                </div>
                <span class="chip {{ $book->status === 'featured' ? 'chip-gold' : '' }}">{{ __('admin.books.statuses.' . $book->status) }}</span>
                @if ($book->hasFile())
                    <span class="chip chip-gold" title="{{ $book->file_path }}"><x-icon name="download" /> PDF</span>
                @else
                    <span class="chip">No PDF</span>
                @endif
                <span class="chip">{{ $book->purchases_count }} {{ __('admin.books.sales') }}</span>
                <div class="admin-row-actions">
                    <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-ghost btn-sm">
                        <x-icon name="pencil" /> {{ __('admin.actions.edit') }}
                    </a>
                    <form method="POST" action="{{ route('admin.books.destroy', $book) }}"
                          onsubmit="return confirm('{{ __('admin.delete_confirm') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><x-icon name="trash" /> {{ __('admin.actions.delete') }}</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="empty-note">{{ __('admin.books.empty') }}</p>
        @endforelse
    </div>
</x-layouts.admin>
