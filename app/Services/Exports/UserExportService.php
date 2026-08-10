<?php

namespace App\Services\Exports;

use App\Models\User;
use App\Services\Audit\AuditLogService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserExportService
{
    public function export(array $filters, User $actor): StreamedResponse
    {
        app(AuditLogService::class)->record('export.users', $actor, null, null, ['filters' => array_intersect_key($filters, array_flip(['role', 'status', 'department_id']))]);
        $query = User::query()->with('department')->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))->when($filters['role'] ?? null, fn ($q, $v) => $q->where('role', $v));

        return response()->streamDownload(function () use ($query): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Nombre', 'Correo', 'Rol', 'Departamento', 'Estado', 'Último acceso', 'Creado']);
            $query->chunk(100, function ($users) use ($out): void {
                foreach ($users as $user) {
                    fputcsv($out, [$user->name, $user->email, $user->role->value, $user->department?->name, $user->status->value, $user->last_login_at?->toIso8601String(), $user->created_at?->toIso8601String()]);
                }
            });
            fclose($out);
        }, 'users.csv', ['Content-Type' => 'text/csv']);
    }
}
