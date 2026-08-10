<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use App\Services\Deliverables\DeliverableFileService;
use App\Services\Deliverables\DeliverableService;
use App\Services\Deliverables\DeliverableVersionService;
use App\Services\Deliverables\MarketingReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuickDeliverableController extends Controller
{
    public function __invoke(
        Request $request,
        CreativeRequest $creativeRequest,
        DeliverableService $deliverableService,
        DeliverableVersionService $versionService,
        DeliverableFileService $fileService,
        MarketingReviewService $marketingService
    ): RedirectResponse {
        $request->validate([
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:51200'],
            'file' => ['nullable', 'file', 'max:51200'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            $uploadedFiles = $request->file('files');
        } elseif ($request->hasFile('file')) {
            $uploadedFiles = [$request->file('file')];
        }

        if (empty($uploadedFiles)) {
            return back()->withErrors(['files' => 'Debes seleccionar al menos una imagen o archivo entregable.']);
        }

        $user = $request->user();

        abort_unless(
            $user->id === $creativeRequest->assignee_id || $user->hasRole('admin', 'supervisor'),
            403,
            'No estás asignado a esta solicitud.'
        );

        if (in_array($creativeRequest->status->value, ['assigned', 'in_validation', 'pending'], true)) {
            $creativeRequest->update([
                'status' => 'in_progress',
                'last_status_changed_at' => now(),
            ]);
        }

        $deliverable = $deliverableService->principal($creativeRequest, $user);

        $version = $versionService->create(
            $deliverable,
            $user,
            $request->input('notes', 'Entrega de diseño final'),
            null
        );

        foreach ($uploadedFiles as $index => $uploadedFile) {
            $fileService->store(
                $version,
                $uploadedFile,
                'final_design',
                $index === 0,
                'Archivo entregable final'
            );
        }

        $marketingService->submitFinal($version, $user);

        $count = count($uploadedFiles);
        $message = $count > 1
            ? "¡{$count} imágenes/archivos subidos y enviados a Marketing exitosamente!"
            : '¡Entregable subido y enviado a Marketing exitosamente!';

        return back()->with('status', $message);
    }
}
