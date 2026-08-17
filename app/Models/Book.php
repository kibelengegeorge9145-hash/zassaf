<?php

namespace App\Models;

use App\Models\Concerns\Localizable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'slug',
    'title_en',
    'title_sw',
    'description_en',
    'description_sw',
    'author',
    'cover_path',
    'file_path',
    'status',
    'publication_date',
    'price',
    'currency',
    'preorder_enabled',
    'is_featured',
])]
class Book extends Model
{
    use Localizable;

    public const STATUS_FEATURED = 'featured';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_PREORDER = 'preorder';
    public const STATUS_COMING_SOON = 'coming_soon';

    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'price' => 'decimal:2',
            'preorder_enabled' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getTitleAttribute(): string
    {
        return $this->getLocalized('title');
    }

    public function getDescriptionAttribute(): string
    {
        return $this->getLocalized('description');
    }

    public function scopePublished($query)
    {
        return $query->whereIn('status', ['featured', 'published', 'preorder']);
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_path
            ? asset('storage/'.$this->cover_path)
            : null;
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 0).' '.$this->currency;
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(BookPurchase::class);
    }

    public function hasFile(): bool
    {
        return filled($this->file_path);
    }

    public function isPurchasable(): bool
    {
        return in_array($this->status, [self::STATUS_FEATURED, self::STATUS_PUBLISHED], true)
            && $this->price !== null
            && $this->price > 0
            && $this->hasFile();
    }

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            if (blank($model->slug)) {
                $model->slug = Str::slug($model->title_en).'-'.Str::lower(Str::random(5));
            }
        });
    }
}
