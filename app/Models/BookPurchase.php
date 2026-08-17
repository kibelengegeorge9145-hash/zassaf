<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'book_id',
    'payment_id',
    'customer_name',
    'customer_email',
    'customer_phone',
    'download_token_hash',
    'download_token_expires_at',
    'purchased_at',
])]
class BookPurchase extends Model
{
    protected function casts(): array
    {
        return [
            'download_token_expires_at' => 'datetime',
            'purchased_at' => 'datetime',
        ];
    }

    public function isGuestPurchase(): bool
    {
        return $this->user_id === null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
