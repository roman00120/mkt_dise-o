<?php

namespace App\Services\Admin;

use App\Models\Department;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepartmentService
{
    public function __construct(private AuditLogService $audit) {}

    public function create(array $data, User $actor): Department
    {
        return DB::transaction(function () use ($data, $actor) {
            $department = Department::create(['uuid' => (string) Str::uuid(), 'name' => trim($data['name']), 'code' => strtoupper(trim($data['code'])), 'description' => $data['description'] ?? null, 'is_active' => true, 'created_by' => $actor->id]);
            $this->audit->record('department.created', $actor, $department);

            return $department;
        });
    }

    public function update(Department $department, array $data, User $actor): Department
    {
        $department->update(['name' => trim($data['name']), 'code' => strtoupper(trim($data['code'])), 'description' => $data['description'] ?? null, 'updated_by' => $actor->id]);
        $this->audit->record('department.updated', $actor, $department);

        return $department->fresh();
    }

    public function toggle(Department $department, User $actor, bool $active): Department
    {
        $department->update(['is_active' => $active, 'updated_by' => $actor->id]);
        $this->audit->record($active ? 'department.activated' : 'department.deactivated', $actor, $department);

        return $department->fresh();
    }
}
