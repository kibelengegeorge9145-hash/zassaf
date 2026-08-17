<?php

namespace App\Services\Payments;

use App\Models\Book;
use App\Models\Member;
use App\Models\Payment;
use App\Models\User;
use App\Services\BookPurchaseService;
use App\Services\MembershipService;
use App\Services\Payments\Exceptions\PaymentException;
use App\Support\MembershipConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Creates and records payment transactions. A payment is always created as
 * PENDING and only ever becomes PAID after provider verification.
 */
class PaymentTransactionService
{
    public function createRegistrationPayment(Member $member, string $method): Payment
    {
        if (! MembershipConfig::registrationOpen()) {
            throw new PaymentException('Membership registration is not open.');
        }

        if (! MembershipConfig::paymentEnabled()) {
            throw new PaymentException('Payments are currently disabled.');
        }

        $this->assertSupportedMethod($method);

        return Payment::create([
            'member_id' => $member->id,
            'transaction_reference' => $this->generateTransactionReference(),
            'amount' => MembershipConfig::registrationFee(),
            'payment_type' => Payment::TYPE_REGISTRATION,
            'payment_method' => $method,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    public function createMonthlyPayment(Member $member, string $method): Payment
    {
        if (! MembershipConfig::paymentEnabled()) {
            throw new PaymentException('Payments are currently disabled.');
        }

        if (! app(MembershipService::class)->canPayMonthly($member)) {
            throw new PaymentException('A monthly payment for the current period already exists.');
        }

        $this->assertSupportedMethod($method);

        return Payment::create([
            'member_id' => $member->id,
            'transaction_reference' => $this->generateTransactionReference(),
            'amount' => MembershipConfig::monthlyFee(),
            'payment_type' => Payment::TYPE_MONTHLY,
            'payment_method' => $method,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    public function createBookPayment(User $user, Book $book, string $method): Payment
    {
        if (! MembershipConfig::paymentEnabled()) {
            throw new PaymentException('Payments are currently disabled.');
        }

        if (! $book->isPurchasable()) {
            throw new PaymentException('This book is not available for purchase yet.');
        }

        $purchases = app(BookPurchaseService::class);

        if ($purchases->alreadyPurchased($user, $book)) {
            throw new PaymentException('You already own this book.');
        }

        $pending = $purchases->pendingPayment($user, $book);

        if ($pending) {
            return $pending;
        }

        $this->assertSupportedMethod($method);

        return Payment::create([
            'member_id' => null,
            'user_id' => $user->id,
            'book_id' => $book->id,
            'transaction_reference' => $this->generateTransactionReference(),
            'amount' => $book->price,
            'payment_type' => Payment::TYPE_BOOK,
            'payment_method' => $method,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    public function createGuestBookPayment(array $customer, Book $book, string $method): Payment
    {
        if (! MembershipConfig::paymentEnabled()) {
            throw new PaymentException('Payments are currently disabled.');
        }

        if (! $book->isPurchasable()) {
            throw new PaymentException('This book is not available for purchase yet.');
        }

        $purchases = app(BookPurchaseService::class);
        $email = strtolower(trim($customer['email']));

        if ($purchases->guestAlreadyPurchased($email, $book)) {
            throw new PaymentException('You already own this book.');
        }

        $pending = $purchases->pendingGuestPayment($email, $book);

        if ($pending) {
            return $pending;
        }

        $this->assertSupportedMethod($method);

        $token = $purchases->generateDownloadToken();

        $payment = Payment::create([
            'member_id' => null,
            'user_id' => null,
            'book_id' => $book->id,
            'customer_name' => $customer['name'],
            'customer_email' => $email,
            'customer_phone' => $customer['phone'],
            'guest_download_token_hash' => $token['hash'],
            'transaction_reference' => $this->generateTransactionReference(),
            'amount' => $book->price,
            'payment_type' => Payment::TYPE_BOOK,
            'payment_method' => $method,
            'status' => Payment::STATUS_PENDING,
        ]);

        session()->put("guest_download_token_{$payment->id}", $token['token']);
        Cache::put("guest_download_token_{$payment->id}", $token['token'], now()->addDays(7));

        return $payment;
    }

    public function generateTransactionReference(): string
    {
        do {
            $reference = 'ZP-'.now()->format('ymd').'-'.strtoupper(Str::random(8));
        } while (Payment::where('transaction_reference', $reference)->exists());

        return $reference;
    }

    private function assertSupportedMethod(string $method): void
    {
        if (! array_key_exists($method, Payment::METHODS)) {
            throw new PaymentException("Unsupported payment method [{$method}].");
        }

        $provider = app(PaymentManager::class)->driver();

        if (! $provider->supportsMethod($method)) {
            throw new PaymentException("The active payment provider does not support [{$method}].");
        }
    }
}
