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
    'topics_en',
    'topics_sw',
    'event_date',
    'event_time',
    'platform_en',
    'platform_sw',
    'speaker_en',
    'speaker_sw',
    'is_published',
])]
class WeekendConvo extends Model
{
    use Localizable;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'event_date' => 'date',
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

    public function getPlatformAttribute(): string
    {
        return $this->getLocalized('platform');
    }

    public function getSpeakerAttribute(): string
    {
        return $this->getLocalized('speaker');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where(function ($q) {
            $q->where('event_date', '>=', now()->toDateString())
                ->orWhereNull('event_date');
        })->orderBy('event_date', 'asc');
    }

    public function scopePast($query)
    {
        return $query->whereNotNull('event_date')
            ->where('event_date', '<', now()->toDateString())
            ->orderBy('event_date', 'desc');
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
