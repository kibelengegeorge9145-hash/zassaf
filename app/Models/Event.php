<?php

namespace App\Models;

use App\Models\Concerns\Localizable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title_en',
    'title_sw',
    'description_en',
    'description_sw',
    'event_date',
    'event_time',
    'location_en',
    'location_sw',
    'is_published',
])]
class Event extends Model
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

    public function getLocationAttribute(): string
    {
        return $this->getLocalized('location');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date', 'asc');
    }
}
