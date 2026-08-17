<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['actor_id', 'action', 'subject_type', 'subject_id', 'description', 'ip_address', 'created_at'])]
class AuditLog extends Model
{
    public $timestamps = false;

    protected $table = 'audit_logs';

    public const ACTION_ADMIN_CREATED = 'admin_created';
    public const ACTION_ADMIN_UPDATED = 'admin_updated';
    public const ACTION_ROLE_CHANGED = 'role_changed';
    public const ACTION_ADMIN_ACTIVATED = 'admin_activated';
    public const ACTION_ADMIN_DEACTIVATED = 'admin_deactivated';
    public const ACTION_ADMIN_DELETED = 'admin_deleted';
    public const ACTION_PASSWORD_CHANGED = 'password_changed';
    public const ACTION_PROFILE_UPDATED = 'profile_updated';
    public const ACTION_SETTINGS_CHANGED = 'settings_changed';
    public const ACTION_MEMBERSHIP_SETTINGS_CHANGED = 'membership_settings_changed';
    public const ACTION_MEMBER_STATUS_CHANGED = 'member_status_changed';
    public const ACTION_MANUAL_PAYMENT_RECORDED = 'manual_payment_recorded';

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public static function log(
        string $action,
        ?string $description = null,
        ?Model $subject = null,
        ?int $actorId = null
    ): self {
        return static::create([
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'actor_id' => $actorId ?? auth()->id(),
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
