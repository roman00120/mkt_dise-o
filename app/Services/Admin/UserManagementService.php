<?php

namespace App\Services\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Department;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserManagementService
{
    public function __construct(private AuditLogService $audit) {}

    public function create(array $data, User $actor): User
    {
        return DB::transaction(function () use ($data, $actor): User {
            $this->validateDepartment($data);
            $user = User::create(['uuid' => (string) Str::uuid(), 'name' => $data['name'], 'email' => strtolower(trim($data['email'])), 'password' => Str::random(48), 'role' => $data['role'], 'status' => $data['status'] ?? UserStatus::INACTIVE, 'department_id' => $data['department_id'] ?? null, 'must_change_password' => true]);
            $this->audit->record('user.created', $actor, $user, $user, ['role' => $user->role->value, 'status' => $user->status->value]);

            return $user;
        });
    }

    public function update(User $target, array $data, User $actor): User
    {
        return DB::transaction(function () use ($target, $data, $actor): User {
            if (isset($data['role']) && $target->hasRole('admin') && $data['role'] !== 'admin' && User::where('role', 'admin')->where('status', 'active')->count() <= 1) {
                throw ValidationException::withMessages(['role' => 'No puedes cambiar el rol del último administrador activo.']);
            }
            $this->validateDepartment($data);
            $changes = ['name' => $data['name'], 'role' => $data['role'], 'department_id' => $data['department_id'] ?? null];
            if (isset($data['email']) && strtolower(trim($data['email'])) !== strtolower($target->email)) {
                $changes += ['email' => strtolower(trim($data['email'])), 'email_verified_at' => null];
            }
            $target->update($changes);
            $this->audit->record('user.updated', $actor, $target, $target, ['changed' => array_keys($changes)]);

            return $target->fresh();
        });
    }

    private function validateDepartment(array $data): void
    {
        if (! empty($data['department_id']) && ! Department::whereKey($data['department_id'])->where('is_active', true)->exists()) {
            throw ValidationException::withMessages(['department_id' => 'El departamento no está activo.']);
        }
        if (isset($data['role']) && ! UserRole::tryFrom($data['role'])) {
            throw ValidationException::withMessages(['role' => 'El rol no es válido.']);
        }
    }
}
