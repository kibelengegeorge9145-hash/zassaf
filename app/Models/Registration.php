<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'full_name',
    'email',
    'phone',
    'type',
    'reference',
    'message',
    'status',
])]
class Registration extends Model
{
    public const TYPES = ['program', 'event', 'weekend_convo', 'membership'];

    public const STATUSES = ['new', 'contacted', 'registered', 'closed'];

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'program' => __('admin.type_program'),
            'event' => __('admin.type_event'),
            'weekend_convo' => __('admin.type_weekend_convo'),
            'membership' => __('admin.type_membership'),
            default => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new' => __('admin.status_new'),
            'contacted' => __('admin.status_contacted'),
            'registered' => __('admin.status_registered'),
            'closed' => __('admin.status_closed'),
            default => $this->status,
        };
    }
}
