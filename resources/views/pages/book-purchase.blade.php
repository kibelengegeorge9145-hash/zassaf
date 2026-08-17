<x-layouts.public
    :title="__('books.purchase.heading')"
    :description="Str::limit($book->description, 160)"
>
    <x-page-header
        :kicker="__('site.nav.books')"
        :title="__('books.purchase.heading')"
        :sub="__('books.purchase.sub')"
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

                    <div class="featured-book-meta">
                        <span class="chip chip-gold">{{ __('books.status.' . $book->status) }}</span>
                        <span><x-icon name="check" /> {{ __('books.price') }}: <strong>{{ $book->formatted_price }}</strong></span>
                    </div>

                    <x-flash />

                    <div class="checkout-options">
                        <div class="checkout-option">
                            <span class="checkout-option-icon"><x-icon name="user" /></span>
                            <h3>{{ __('books.purchase.member_title') }}</h3>
                            <p>{{ __('books.purchase.member_desc') }}</p>
                            @if (auth()->check() && auth()->user()->isMember())
                                <a href="{{ route('books.checkout', $book) }}" class="btn btn-gold">{{ __('books.purchase.member_button') }}</a>
                            @else
                                <a href="{{ route('member.login') }}" class="btn btn-gold">{{ __('books.purchase.member_login') }}</a>
                            @endif
                        </div>

                        <div class="checkout-option">
                            <span class="checkout-option-icon"><x-icon name="user-plus" /></span>
                            <h3>{{ __('books.purchase.guest_title') }}</h3>
                            <p>{{ __('books.purchase.guest_desc') }}</p>

                            <form method="POST" action="{{ route('books.guest.checkout', $book) }}" class="checkout-guest-form">
                                @csrf

                                <div class="form-field">
                                    <label for="customer_name">{{ __('books.purchase.full_name') }} <span class="req">*</span></label>
                                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required maxlength="255">
                                    @error('customer_name') <span class="field-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-field">
                                    <label for="customer_email">{{ __('books.purchase.email') }} <span class="req">*</span></label>
                                    <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required maxlength="255">
                                    @error('customer_email') <span class="field-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-field">
                                    <label for="customer_phone">{{ __('books.purchase.phone') }} <span class="req">*</span></label>
                                    <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required maxlength="40">
                                    @error('customer_phone') <span class="field-error">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-field form-field-full">
                                    <label>{{ __('membership.payments.choose_method') }}</label>
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
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-gold">{{ __('books.purchase.continue_payment') }}</button>
                                    <a href="{{ route('books.show', $book) }}" class="btn btn-ghost">{{ __('books.back') }}</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
