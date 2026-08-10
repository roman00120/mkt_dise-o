<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLogService
{
    public function record(string $action, ?User $actor = null, ?Model $auditable = null, ?User $target = null, array $metadata = []): AuditLog
    {
        $safe = collect($metadata)->except(['password', 'token', 'secret', 'ip', 'last_login_ip'])->all();

        return AuditLog::create(['uuid' => (string) Str::uuid(), 'actor_id' => $actor?->id, 'action' => $action, 'auditable_type' => $auditable ? get_class($auditable) : null, 'auditable_id' => $auditable?->getKey(), 'target_user_id' => $target?->id, 'metadata' => $safe, 'ip_address' => request()?->ip()]);
    }
}
