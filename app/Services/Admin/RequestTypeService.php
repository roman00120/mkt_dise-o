<?php

namespace App\Services\Admin;

use App\Models\RequestType;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Support\Str;

class RequestTypeService
{
    public function __construct(private AuditLogService $audit) {}

    public function create(array $data, User $actor): RequestType
    {
        $type = RequestType::create(['uuid' => (string) Str::uuid(), 'service' => $data['service'], 'key' => strtolower(trim($data['key'])), 'label' => trim($data['label']), 'description' => $data['description'] ?? null, 'sort_order' => $data['sort_order'] ?? 0, 'created_by' => $actor->id]);
        $this->audit->record('request_type.created', $actor, $type);

        return $type;
    }

    public function update(RequestType $type, array $data, User $actor): RequestType
    {
        $type->update(['label' => trim($data['label']), 'description' => $data['description'] ?? null, 'sort_order' => $data['sort_order'] ?? 0, 'updated_by' => $actor->id]);
        $this->audit->record('request_type.updated', $actor, $type);

        return $type->fresh();
    }

    public function toggle(RequestType $type, User $actor, bool $active): RequestType
    {
        $type->update(['is_active' => $active, 'updated_by' => $actor->id]);
        $this->audit->record($active ? 'request_type.activated' : 'request_type.deactivated', $actor, $type);

        return $type->fresh();
    }
}
