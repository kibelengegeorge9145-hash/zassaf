<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'username', 'email', 'phone', 'gender', 'date_of_birth', 'location', 'password', 'role', 'is_active', 'profile_photo', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, CanResetPassword;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_MEMBER = 'member';

    public const ROLES = [self::ROLE_SUPER_ADMIN, self::ROLE_EDITOR, self::ROLE_MEMBER];

    public function sendPasswordResetNotification($token)
    {
        if ($this->canAdmin()) {
            $this->notify(new \App\Notifications\AdminPasswordReset($token));

            return;
        }

        $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isEditor(): bool
    {
        return $this->role === self::ROLE_EDITOR;
    }

    public function isMember(): bool
    {
        return $this->role === self::ROLE_MEMBER;
    }

    public function canAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isEditor();
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function bookPurchases(): HasMany
    {
        return $this->hasMany(BookPurchase::class);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => __('admin.roles.super_admin'),
            self::ROLE_EDITOR => __('admin.roles.editor'),
            self::ROLE_MEMBER => __('membership.role_member'),
            default => ucfirst((string) $this->role),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? __('admin.users.active') : __('admin.users.inactive');
    }

    public function getInitialsAttribute(): string
    {
        $initials = collect(explode(' ', trim((string) $this->name)))
            ->filter()
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take(2)
            ->implode('');

        return $initials ?: 'ZA';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->profile_photo) {
            return null;
        }

        return asset('storage/' . ltrim($this->profile_photo, '/'));
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Get the display fallback for a missing username.
     */
    public function getUsernameAttribute(?string $value): string
    {
        return $value ?? Str::slug($this->name, '');
    }
}
