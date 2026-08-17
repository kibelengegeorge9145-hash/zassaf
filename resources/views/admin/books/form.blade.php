@php $editing = isset($book) && $book->exists; @endphp

<x-layouts.admin :title="$editing ? __('admin.books.edit') : __('admin.books.create')">
    <div class="admin-section">
        <h2>{{ $editing ? __('admin.books.edit') : __('admin.books.create') }}</h2>
    </div>

    <form method="POST" action="{{ $editing ? route('admin.books.update', $book) : route('admin.books.store') }}" enctype="multipart/form-data" class="form-card admin-form">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        <div class="form-grid">
            <div class="form-field">
                <label for="title_en">{{ __('admin.books.fields.title_en') }} <span class="req">*</span></label>
                <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $book->title_en ?? '') }}" required maxlength="255">
            </div>

            <div class="form-field">
                <label for="title_sw">{{ __('admin.books.fields.title_sw') }}</label>
                <input type="text" id="title_sw" name="title_sw" value="{{ old('title_sw', $book->title_sw ?? '') }}" maxlength="255">
            </div>

            <div class="form-field form-field-full">
                <label for="description_en">{{ __('admin.books.fields.description_en') }} <span class="req">*</span></label>
                <textarea id="description_en" name="description_en" rows="4" required maxlength="4000">{{ old('description_en', $book->description_en ?? '') }}</textarea>
            </div>

            <div class="form-field form-field-full">
                <label for="description_sw">{{ __('admin.books.fields.description_sw') }}</label>
                <textarea id="description_sw" name="description_sw" rows="4" maxlength="4000">{{ old('description_sw', $book->description_sw ?? '') }}</textarea>
            </div>

            <div class="form-field">
                <label for="author">{{ __('admin.books.fields.author') }} <span class="req">*</span></label>
                <input type="text" id="author" name="author" value="{{ old('author', $book->author ?? '') }}" required maxlength="255">
            </div>

            <div class="form-field">
                <label for="status">{{ __('admin.books.fields.status') }}</label>
                <select id="status" name="status">
                    @foreach (['featured', 'published', 'preorder', 'coming_soon'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $book->status ?? 'coming_soon') === $status)>{{ __('admin.books.statuses.' . $status) }}</option>
                    @endforeach
                </select>
                <p class="field-hint">{{ __('admin.books.fields.pdf_status_hint') }}</p>
            </div>

            <div class="form-field">
                <label for="publication_date">{{ __('admin.books.fields.publication_date') }}</label>
                <input type="date" id="publication_date" name="publication_date" value="{{ old('publication_date', $book->publication_date?->format('Y-m-d') ?? '') }}">
            </div>

            <div class="form-field">
                <label for="price">{{ __('admin.books.fields.price') }}</label>
                <input type="number" id="price" name="price" min="0" step="0.01" value="{{ old('price', $book->price ?? '') }}">
            </div>

            <div class="form-field">
                <label for="currency">{{ __('admin.books.fields.currency') }}</label>
                <input type="text" id="currency" name="currency" value="{{ old('currency', $book->currency ?? 'TZS') }}" maxlength="10">
            </div>

            <div class="form-field">
                <label for="cover">{{ __('admin.books.fields.cover') }}</label>
                <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/webp">
                @error('cover') <span class="field-error">{{ $message }}</span> @enderror
                @if ($editing && $book->cover_path)
                    <p class="field-hint">{{ __('admin.books.fields.current_cover') }}: {{ $book->cover_path }}</p>
                @endif
            </div>

            <div class="form-field form-field-full">
                <label for="pdf">{{ __('admin.books.fields.pdf') }}</label>
                <input type="file" id="pdf" name="pdf" accept="application/pdf">
                @error('pdf') <span class="field-error">{{ $message }}</span> @enderror
                <p class="field-hint">
                    @if ($editing && $book->file_path)
                        {{ __('admin.books.fields.current_pdf') }}: {{ $book->file_path }}
                    @else
                        {{ __('admin.books.fields.pdf_none') }}
                    @endif
                    · {{ __('admin.books.fields.pdf_hint') }}
                </p>
            </div>

            <div class="form-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="preorder_enabled" value="1" @checked(old('preorder_enabled', $book->preorder_enabled ?? false))>
                    <span>{{ __('admin.books.fields.preorder_enabled') }}</span>
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $book->is_featured ?? false))>
                    <span>{{ __('admin.books.fields.is_featured') }}</span>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-gold">{{ __('admin.actions.save') }}</button>
            <a href="{{ route('admin.books.index') }}" class="btn btn-ghost">{{ __('admin.actions.cancel') }}</a>
        </div>
    </form>
</x-layouts.admin>
