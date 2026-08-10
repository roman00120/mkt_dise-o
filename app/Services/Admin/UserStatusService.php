<?php

namespace App\Services\Admin;

use App\Enums\UserStatus;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserStatusService
{
    public function __construct(private AuditLogService $audit) {}

    public function activate(User $target, User $actor): User
    {
        return $this->set($target, $actor, UserStatus::ACTIVE, 'user.activated');
    }

    public function restore(User $target, User $actor): User
    {
        return $this->set($target, $actor, UserStatus::ACTIVE, 'user.access_restored', true);
    }

    public function deactivate(User $target, User $actor, string $reason): User
    {
        return $this->set($target, $actor, UserStatus::INACTIVE, 'user.deactivated', false, $reason);
    }

    public function suspend(User $target, User $actor, string $reason): User
    {
        if ($target->id === $actor->id) {
            throw ValidationException::withMessages(['user' => 'No puedes suspender tu propio usuario.']);
        }

        return $this->set($target, $actor, UserStatus::SUSPENDED, 'user.suspended', false, $reason);
    }

    private function set(User $target, User $actor, UserStatus $status, string $action, bool $clearDates = false, ?string $reason = null): User
    {
        return DB::transaction(function () use ($target, $actor, $status, $action, $clearDates, $reason): User {
            $locked = User::query()->lockForUpdate()->findOrFail($target->id);
            $this->guard($locked, $actor, $status);
            $data = ['status' => $status, 'remember_token' => null];
            if ($status === UserStatus::SUSPENDED) {
                $data['suspended_at'] = now();
            }
            if ($status === UserStatus::INACTIVE) {
                $data['deactivated_at'] = now();
            }
            if ($clearDates) {
                $data += ['suspended_at' => null, 'deactivated_at' => null];
            }
            $locked->update($data);
            $this->audit->record($action, $actor, $locked, $locked, ['reason' => $reason]);

            return $locked->fresh();
        });
    }

    private function guard(User $target, User $actor, UserStatus $status): void
    {
        if ($target->id === $actor->id && in_array($status, [UserStatus::INACTIVE, UserStatus::SUSPENDED], true)) {
            throw ValidationException::withMessages(['user' => 'No puedes bloquear tu propio usuario.']);
        }
        if ($target->hasRole('admin') && $status !== UserStatus::ACTIVE && User::query()->where('role', 'admin')->where('status', 'active')->count() <= 1) {
            throw ValidationException::withMessages(['user' => 'Debe existir al menos un administrador activo.']);
        }
        if ($target->hasRole('supervisor') && $status !== UserStatus::ACTIVE && User::query()->where('role', 'supervisor')->where('status', 'active')->count() <= 1) {
            throw ValidationException::withMessages(['user' => 'Debe existir al menos un supervisor activo.']);
        }
    }
}
