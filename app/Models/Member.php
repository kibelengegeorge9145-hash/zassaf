<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

#[Fillable(['user_id', 'plan_id', 'membership_number', 'status', 'joined_at', 'expires_at'])]
class Member extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ACTIVE,
        self::STATUS_EXPIRED,
        self::STATUS_SUSPENDED,
        self::STATUS_CANCELLED,
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function registrationPayment(): ?Payment
    {
        return $this->payments()
            ->where('payment_type', Payment::TYPE_REGISTRATION)
            ->latest('id')
            ->first();
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->expires_at
            && $this->expires_at->isFuture();
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function canRenew(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_EXPIRED], true);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => __('membership.statuses.member_pending'),
            self::STATUS_ACTIVE => __('membership.statuses.member_active'),
            self::STATUS_EXPIRED => __('membership.statuses.member_expired'),
            self::STATUS_SUSPENDED => __('membership.statuses.member_suspended'),
            self::STATUS_CANCELLED => __('membership.statuses.member_cancelled'),
            default => ucfirst((string) $this->status),
        };
    }

    public function getNextPaymentDueDateAttribute(): ?Carbon
    {
        if (! $this->expires_at) {
            return null;
        }

        return $this->expires_at->copy()->addDay();
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
