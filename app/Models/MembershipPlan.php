<?php

namespace App\Models;

use App\Models\Concerns\Localizable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name_en',
    'name_sw',
    'description_en',
    'description_sw',
    'price',
    'registration_fee',
    'monthly_fee',
    'currency',
    'billing_cycle',
    'launch_date',
    'status',
    'is_active',
])]
class MembershipPlan extends Model
{
    use Localizable;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'registration_fee' => 'decimal:2',
            'monthly_fee' => 'decimal:2',
            'launch_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function getFormattedRegistrationFeeAttribute(): string
    {
        return number_format((float) $this->registration_fee, 0).' '.$this->currency;
    }

    public function getFormattedMonthlyFeeAttribute(): string
    {
        return number_format((float) $this->monthly_fee, 0).' '.$this->currency;
    }

    public function getNameAttribute(): string
    {
        return $this->getLocalized('name');
    }

    public function getDescriptionAttribute(): string
    {
        return $this->getLocalized('description');
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 0).' '.$this->currency;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
