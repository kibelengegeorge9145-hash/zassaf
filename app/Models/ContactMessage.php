<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'email',
    'phone',
    'subject',
    'message',
    'is_read',
])]
class ContactMessage extends Model
{
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function getReadLabelAttribute(): string
    {
        return $this->is_read
            ? __('admin.read')
            : __('admin.unread');
    }
}
