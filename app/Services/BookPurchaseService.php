<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Access control and record keeping for digital book purchases.
 *
 * Supports two purchase types:
 *  - Member purchases: bound to an authenticated User.
 *  - Guest purchases: user_id is NULL, customer details and a hashed
 *    download token are stored with the purchase record.
 */
class BookPurchaseService
{
    public function purchase(User $user, Book $book, Payment $payment): BookPurchase
    {
        $purchase = BookPurchase::firstOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['payment_id' => $payment->id, 'purchased_at' => $payment->paid_at ?? now()],
        );

        Log::info('Book purchased', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'purchase_id' => $purchase->id,
            'payment_id' => $payment->id,
            'transaction_reference' => $payment->transaction_reference,
        ]);

        return $purchase;
    }

    public function purchaseForGuest(Payment $payment, Book $book): BookPurchase
    {
        $purchase = BookPurchase::firstOrCreate(
            ['payment_id' => $payment->id],
            [
                'user_id' => null,
                'book_id' => $book->id,
                'customer_name' => $payment->customer_name,
                'customer_email' => $payment->customer_email,
                'customer_phone' => $payment->customer_phone,
                'download_token_hash' => $payment->guest_download_token_hash,
                'download_token_expires_at' => now()->addDays(7),
                'purchased_at' => $payment->paid_at ?? now(),
            ],
        );

        Log::info('Book purchased by guest', [
            'book_id' => $book->id,
            'purchase_id' => $purchase->id,
            'payment_id' => $payment->id,
            'customer_email' => $payment->customer_email,
            'transaction_reference' => $payment->transaction_reference,
        ]);

        return $purchase;
    }

    public function hasAccess(User $user, Book $book): bool
    {
        return BookPurchase::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereHas('payment', fn ($query) => $query->where('status', Payment::STATUS_PAID))
            ->exists();
    }

    public function alreadyPurchased(User $user, Book $book): bool
    {
        return $this->hasAccess($user, $book);
    }

    public function guestAlreadyPurchased(string $email, Book $book): bool
    {
        return BookPurchase::query()
            ->whereNull('user_id')
            ->where('book_id', $book->id)
            ->where('customer_email', $email)
            ->whereHas('payment', fn ($query) => $query->where('status', Payment::STATUS_PAID))
            ->exists();
    }

    public function pendingPayment(User $user, Book $book): ?Payment
    {
        return Payment::query()
            ->where('payment_type', Payment::TYPE_BOOK)
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_PROCESSING])
            ->latest('id')
            ->first();
    }

    public function pendingGuestPayment(string $email, Book $book): ?Payment
    {
        return Payment::query()
            ->where('payment_type', Payment::TYPE_BOOK)
            ->whereNull('user_id')
            ->where('book_id', $book->id)
            ->where('customer_email', $email)
            ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_PROCESSING])
            ->latest('id')
            ->first();
    }

    /**
     * Generate a cryptographically secure download token and its stored hash.
     *
     * Only the SHA-256 hash is persisted. The raw token is shown to the buyer
     * once, after the payment has been verified as PAID.
     *
     * @return array{token: string, hash: string}
     */
    public function generateDownloadToken(): array
    {
        $token = bin2hex(random_bytes(32));

        return [
            'token' => $token,
            'hash' => hash('sha256', $token),
        ];
    }

    /**
     * Resolve a guest purchase by its raw download token.
     *
     * Returns null when the token is unknown, expired, not linked to a PAID
     * payment, or the book no longer exists.
     */
    public function purchaseByDownloadToken(string $token): ?BookPurchase
    {
        $purchase = BookPurchase::with(['payment', 'book'])
            ->where('download_token_hash', hash('sha256', $token))
            ->first();

        if (! $purchase) {
            return null;
        }

        if ($purchase->download_token_expires_at && $purchase->download_token_expires_at->isPast()) {
            return null;
        }

        if (! $purchase->payment?->isPaid()) {
            return null;
        }

        if (! $purchase->book) {
            return null;
        }

        return $purchase;
    }
}
