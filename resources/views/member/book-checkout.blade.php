<x-layouts.member :title="__('books.checkout')">
    <div class="admin-section">
        <h2>{{ __('books.checkout') }}</h2>
        <p class="admin-sub">{{ $book->title }} · {{ $book->formatted_price }}</p>
    </div>

    <form method="POST" action="{{ route('books.buy', $book) }}" class="form-card admin-form">
        @csrf

        <div class="form-field form-field-full">
            <label>{{ __('membership.payments.choose_method') }}</label>
            <p class="field-hint">{{ __('membership.payments.method_hint') }}</p>
        </div>

        <div class="form-grid">
            @foreach (\App\Models\Payment::METHODS as $method => $label)
                <label class="method-card">
                    <input type="radio" name="payment_method" value="{{ $method }}" required>
                    <span class="method-card-body">
                        <strong>{{ __('membership.payments.'.$method) }}</strong>
                        <small>{{ $book->formatted_price }}</small>
                    </span>
                </label>
            @endforeach
        </div>

        @error('payment_method') <span class="field-error">{{ $message }}</span> @enderror

        <div class="form-actions">
            <button type="submit" class="btn btn-gold">{{ __('membership.payments.start') }}</button>
            <a href="{{ route('books.show', $book) }}" class="btn btn-ghost">{{ __('membership.payments.cancel') }}</a>
        </div>
    </form>
</x-layouts.member>
