<?php

namespace App\Services\Dashboard;

use Carbon\Carbon;

class MarketingDashboardService
{
    public function forUser(string $filter = 'all'): array
    {
        $today = now()->startOfDay();
        $requests = collect([
            ['id' => 'TG-2026-014', 'title' => 'Catálogo de producto A500', 'service' => 'Diseño Gráfico', 'status' => 'En espera de información', 'owner' => 'Mariana Torres', 'due_at' => $today->copy()->addDays(2), 'updated_at' => now()->subHours(2), 'priority' => 'Alta'],
            ['id' => 'TG-2026-011', 'title' => 'Video corporativo de seguridad', 'service' => 'Video', 'status' => 'En revisión de Marketing', 'owner' => 'Luis Hernández', 'due_at' => $today->copy()->addDays(4), 'updated_at' => now()->subHours(5), 'priority' => 'Media'],
            ['id' => 'TG-2026-012', 'title' => 'Render de exhibidor', 'service' => 'Render 3D', 'status' => 'En proceso', 'owner' => 'Carlos Ramírez', 'due_at' => $today->copy()->addDays(7), 'updated_at' => now()->subDay(), 'priority' => 'Media'],
            ['id' => 'TG-2026-008', 'title' => 'Etiqueta para nueva línea', 'service' => 'Diseño Gráfico', 'status' => 'Aprobada', 'owner' => 'Mariana Torres', 'due_at' => $today->copy()->subDays(2), 'updated_at' => now()->subDays(2), 'priority' => 'Baja'],
            ['id' => 'TG-2026-006', 'title' => 'Reel para lanzamiento', 'service' => 'Video', 'status' => 'Correcciones solicitadas', 'owner' => 'Luis Hernández', 'due_at' => $today->copy()->addDays(3), 'updated_at' => now()->subDays(3), 'priority' => 'Alta'],
            ['id' => 'TG-2026-003', 'title' => 'Mockup de empaque', 'service' => 'Render 3D', 'status' => 'Finalizada', 'owner' => 'Carlos Ramírez', 'due_at' => $today->copy()->subDays(6), 'updated_at' => now()->subDays(5), 'priority' => 'Baja'],
        ])->map(function (array $request) use ($today): array {
            $request['date_health'] = $this->dateHealth($request['due_at'], in_array($request['status'], ['Aprobada', 'Finalizada'], true), $today);

            return $request;
        });

        $filtered = $requests->filter(fn (array $request): bool => match ($filter) {
            'pending' => in_array($request['status'], ['Pendiente', 'En espera de información', 'Correcciones solicitadas'], true),
            'in-progress' => $request['status'] === 'En proceso',
            'review' => str_contains($request['status'], 'revisión'),
            'completed' => in_array($request['status'], ['Aprobada', 'Finalizada'], true),
            default => true,
        })->values();

        return [
            'metrics' => [
                ['label' => 'Solicitudes activas', 'value' => 12, 'context' => '3 requieren atención', 'icon' => 'layers-3', 'tone' => 'primary'],
                ['label' => 'En proceso', 'value' => 7, 'context' => 'Actualizadas esta semana', 'icon' => 'loader-circle', 'tone' => 'info'],
                ['label' => 'Pendientes de revisión', 'value' => 3, 'context' => 'Tienen entregable disponible', 'icon' => 'search-check', 'tone' => 'warning'],
                ['label' => 'Próximas a vencer', 'value' => 2, 'context' => 'Dentro de 3 días', 'icon' => 'alarm-clock', 'tone' => 'danger'],
            ],
            'attentionItems' => $requests->filter(fn (array $request): bool => in_array($request['status'], ['En espera de información', 'En revisión de Marketing', 'Correcciones solicitadas'], true))->sortBy('due_at')->values()->all(),
            'recentRequests' => $filtered->all(),
            'pendingDeliverables' => [
                ['request_id' => 'TG-2026-011', 'title' => 'Video corporativo de seguridad', 'service' => 'Video', 'file' => 'video-seguridad-v03.mp4', 'version' => 'v03', 'delivered_at' => now()->subHours(5), 'owner' => 'Luis Hernández'],
                ['request_id' => 'TG-2026-012', 'title' => 'Render de exhibidor', 'service' => 'Render 3D', 'file' => 'exhibidor-frontal-v02.png', 'version' => 'v02', 'delivered_at' => now()->subDay(), 'owner' => 'Carlos Ramírez'],
            ],
            'recentActivity' => [
                ['icon' => 'upload-cloud', 'tone' => 'info', 'text' => 'Luis Hernández subió una nueva versión', 'entity' => 'Video corporativo de seguridad', 'at' => now()->subHours(5), 'author' => 'Luis Hernández'],
                ['icon' => 'message-circle', 'tone' => 'design', 'text' => 'Mariana Torres comentó en', 'entity' => 'Render de exhibidor', 'at' => now()->subHours(8), 'author' => 'Mariana Torres'],
                ['icon' => 'play-circle', 'tone' => 'info', 'text' => 'La solicitud cambió a En proceso', 'entity' => 'TG-2026-008 · Etiqueta para nueva línea', 'at' => now()->subDay(), 'author' => null],
                ['icon' => 'check-circle', 'tone' => 'success', 'text' => 'Se aprobó', 'entity' => 'Etiqueta para nueva línea', 'at' => now()->subDays(2), 'author' => 'Andrea Martínez'],
            ],
            'serviceCards' => [
                ['key' => 'design', 'name' => 'Diseño Gráfico', 'description' => 'Materiales impresos, digitales y comunicación visual.', 'icon' => 'pen-tool', 'tone' => 'design'],
                ['key' => 'video', 'name' => 'Video', 'description' => 'Producción, edición y contenido audiovisual.', 'icon' => 'video', 'tone' => 'video'],
                ['key' => 'render', 'name' => 'Render 3D', 'description' => 'Visualización de productos, espacios y conceptos.', 'icon' => 'box', 'tone' => 'render'],
            ],
            'filter' => $filter,
        ];
    }

    public function dateHealth(Carbon|string|null $date, bool $completed = false, ?Carbon $today = null): string
    {
        if (! $date) {
            return 'without_date';
        }

        if ($completed) {
            return 'on_time';
        }

        $date = $date instanceof Carbon ? $date->copy()->startOfDay() : Carbon::parse($date)->startOfDay();
        $today ??= now()->startOfDay();

        return $date->isBefore($today) ? 'overdue' : ($today->diffInDays($date, true) <= 3 ? 'due_soon' : 'on_time');
    }
}
