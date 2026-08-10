<?php

namespace App\Services\Exports;

use App\Models\CreativeRequest;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequestExportService
{
    public function export(array $filters, User $actor): StreamedResponse
    {
        app(AuditLogService::class)->record('export.requests', $actor, null, null, ['filters' => array_intersect_key($filters, array_flip(['service', 'status']))]);
        $query = CreativeRequest::query()->with(['requester', 'assignee'])->when($filters['service'] ?? null, fn ($q, $v) => $q->where('service', $v))->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v));

        return response()->streamDownload(function () use ($query): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Folio', 'Título', 'Servicio', 'Solicitante', 'Responsable', 'Estado', 'Fecha requerida']);
            $query->chunk(100, function ($requests) use ($out): void {
                foreach ($requests as $request) {
                    fputcsv($out, [$request->folio, $request->title, $request->service->value, $request->requester?->email, $request->assignee?->email, $request->status->value, $request->required_date?->toDateString()]);
                }
            });
            fclose($out);
        }, 'requests.csv', ['Content-Type' => 'text/csv']);
    }
}
