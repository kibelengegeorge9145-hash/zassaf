<x-layouts.public :title="__('books.guest_success.heading')">
    <x-page-header
        :kicker="__('membership.payments.heading')"
        :title="__('books.guest_success.heading')"
        :sub="__('books.guest_success.sub')"
    />

    <section class="section section-light">
        <div class="container">
            <div class="payment-gateway-card">
                <span class="chip chip-gold"><x-icon name="check" /> {{ __('books.guest_success.paid') }}</span>

                <ul class="detail-list">
                    <li>
                        <span>{{ __('books.guest_success.book') }}</span>
                        <strong>{{ $payment->book?->title ?? '—' }}</strong>
                    </li>
                    <li>
                        <span>{{ __('books.guest_success.amount') }}</span>
                        <strong>{{ $payment->formatted_amount }}</strong>
                    </li>
                    <li>
                        <span>{{ __('books.guest_success.reference') }}</span>
                        <strong>{{ $payment->transaction_reference }}</strong>
                    </li>
                </ul>

                @if ($token)
                    <p class="empty-note">{{ __('books.guest_success.ready') }}</p>
                    <div class="form-actions">
                        <a href="{{ route('guest.download', $token) }}" class="btn btn-gold">
                            <x-icon name="download" /> {{ __('books.download') }}
                        </a>
                    </div>

                    @if ($emailConfigured)
                        <p class="featured-book-note">{{ __('books.guest_success.email_sent') }}</p>
                    @else
                        <p class="featured-book-note">{{ __('books.guest_success.email_unconfigured') }}</p>
                    @endif
                @else
                    <p class="empty-note">{{ __('books.guest_success.token_unavailable') }}</p>
                @endif

                <div class="form-actions">
                    <a href="{{ route('books') }}" class="btn btn-ghost">{{ __('books.back') }}</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
