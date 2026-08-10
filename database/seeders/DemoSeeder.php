<?php

namespace Database\Seeders;

use App\Enums\CreativeService;
use App\Enums\RequestPriority;
use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\CreativeRequest;
use App\Models\Department;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (in_array((string) config('app.env'), ['production', 'staging'], true)) {
            throw new \LogicException('Los seeders de desarrollo o demostración no pueden ejecutarse en producción.');
        }

        $password = env('LOCAL_AUTH_PASSWORD');
        if (! is_string($password) || mb_strlen($password) < 12) {
            throw new \LogicException('Define LOCAL_AUTH_PASSWORD con al menos 12 caracteres para cargar datos demo.');
        }

        foreach ([
            ['Administrador', 'admin@totalground.local', UserRole::ADMIN],
            ['Andrea Martínez', 'marketing@totalground.local', UserRole::MARKETING],
            ['Luis Hernández', 'design@totalground.local', UserRole::DESIGN],
            ['Mariana Torres', 'video@totalground.local', UserRole::VIDEO],
            ['Carlos Ramírez', 'render@totalground.local', UserRole::RENDER],
            ['Sofía Navarro', 'supervisor@totalground.local', UserRole::SUPERVISOR],
        ] as [$name, $email, $role]) {
            $user = User::updateOrCreate(['email' => $email], [
                'name' => $name,
                'password' => Hash::make($password),
                'status' => UserStatus::ACTIVE,
                'role' => $role,
                'password_changed_at' => now(),
            ]);

            foreach (NotificationPreference::TYPES as $type) {
                NotificationPreference::updateOrCreate(
                    ['user_id' => $user->id, 'event_type' => $type],
                    ['in_app' => true, 'email' => false]
                );
            }
        }

        $marketing = User::where('email', 'marketing@totalground.local')->firstOrFail();
        $video = User::where('email', 'video@totalground.local')->firstOrFail();
        $render = User::where('email', 'render@totalground.local')->firstOrFail();
        $supervisor = User::where('email', 'supervisor@totalground.local')->firstOrFail();

        foreach ([
            ['TG-2026-7001', CreativeService::DESIGN, RequestStatus::PENDING, 'Catálogo de temporada', null],
            ['TG-2026-7002', CreativeService::VIDEO, RequestStatus::ASSIGNED, 'Video de lanzamiento', $video],
            ['TG-2026-7003', CreativeService::RENDER, RequestStatus::IN_PROGRESS, 'Render de exhibidor', $render],
        ] as [$folio, $service, $status, $title, $assignee]) {
            CreativeRequest::updateOrCreate(['folio' => $folio], [
                'uuid' => (string) str()->uuid(),
                'requester_id' => $marketing->id,
                'service' => $service,
                'request_type' => 'digital',
                'title' => $title,
                'description' => 'Solicitud de demostración local.',
                'objective' => 'Validar el flujo operativo.',
                'required_date' => today()->addDays(7),
                'requested_priority' => RequestPriority::MEDIUM,
                'operational_priority' => $assignee ? RequestPriority::HIGH : null,
                'status' => $status,
                'assignee_id' => $assignee?->id,
                'assigned_by' => $assignee ? $supervisor->id : null,
                'assigned_at' => $assignee ? now() : null,
                'internal_due_date' => $assignee ? today()->addDays(5) : null,
                'last_status_changed_at' => now(),
            ]);
        }

        Department::query()->where('code', 'marketing')->update(['is_active' => true]);
    }
}
