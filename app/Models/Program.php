<?php

namespace App\Models;

use App\Models\Concerns\Localizable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'slug',
    'title_en',
    'title_sw',
    'description_en',
    'description_sw',
    'icon',
    'is_published',
    'sort_order',
])]
class Program extends Model
{
    use Localizable;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
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
        return $query->where('is_published', true);
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
