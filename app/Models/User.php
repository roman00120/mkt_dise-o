<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            $user->uuid ??= (string) Str::uuid();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'status',
        'role',
        'department_id',
        'password_changed_at',
        'must_change_password',
        'suspended_at',
        'deactivated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'status' => UserStatus::class,
            'role' => UserRole::class,
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'must_change_password' => 'boolean',
            'suspended_at' => 'datetime',
            'deactivated_at' => 'datetime',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function hasRole(UserRole|string ...$roles): bool
    {
        $currentRole = $this->role instanceof UserRole ? $this->role->value : $this->role;

        return in_array($currentRole, array_map(fn ($role) => $role instanceof UserRole ? $role->value : $role, $roles), true);
    }

    public function assignedCreativeRequests()
    {
        return $this->hasMany(CreativeRequest::class, 'assignee_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function notificationPreferences()
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
