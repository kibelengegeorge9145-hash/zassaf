<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'member_id',
    'user_id',
    'book_id',
    'customer_name',
    'customer_email',
    'customer_phone',
    'guest_download_token_hash',
    'transaction_reference',
    'provider_reference',
    'amount',
    'payment_type',
    'payment_method',
    'status',
    'failure_reason',
    'paid_at',
])]
class Payment extends Model
{
    public const TYPE_REGISTRATION = 'registration';
    public const TYPE_MONTHLY = 'monthly';
    public const TYPE_BOOK = 'book';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_REFUNDED = 'refunded';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PROCESSING,
        self::STATUS_PAID,
        self::STATUS_FAILED,
        self::STATUS_CANCELLED,
        self::STATUS_EXPIRED,
        self::STATUS_REFUNDED,
    ];

    public const METHODS = [
        'mobile_money' => 'Mobile Money',
        'card' => 'Card',
        'bank' => 'Bank',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        $currency = $this->book?->currency
            ?? $this->member?->plan?->currency
            ?? 'TZS';

        return number_format((float) $this->amount, 0).' '.$currency;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->payment_type) {
            self::TYPE_REGISTRATION => __('membership.statuses.type_registration'),
            self::TYPE_MONTHLY => __('membership.statuses.type_monthly'),
            self::TYPE_BOOK => __('membership.statuses.type_book'),
            default => ucfirst((string) $this->payment_type),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => __('membership.statuses.payment_pending'),
            self::STATUS_PROCESSING => __('membership.statuses.payment_processing'),
            self::STATUS_PAID => __('membership.statuses.payment_paid'),
            self::STATUS_FAILED => __('membership.statuses.payment_failed'),
            self::STATUS_CANCELLED => __('membership.statuses.payment_cancelled'),
            self::STATUS_EXPIRED => __('membership.statuses.payment_expired'),
            self::STATUS_REFUNDED => __('membership.statuses.payment_refunded'),
            default => ucfirst((string) $this->status),
        };
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'mobile_money' => __('membership.payments.mobile_money'),
            'card' => __('membership.payments.card'),
            'bank' => __('membership.payments.bank'),
            default => ucfirst((string) $this->payment_method),
        };
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isGuestBookPayment(): bool
    {
        return $this->payment_type === self::TYPE_BOOK
            && $this->user_id === null
            && filled($this->customer_email);
    }
}
