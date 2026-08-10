<?php

namespace App\Http\Controllers\App;

use App\Enums\CreativeService;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\WizardStepRequest;
use App\Models\CreativeRequest;
use App\Services\Requests\RecommendedDateService;
use App\Services\Requests\RequestDraftService;
use App\Support\RequestCatalog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequestWizardController extends Controller
{
    public function create(Request $request): View
    {
        $service = CreativeService::tryFrom($request->string('service')->toString());

        return view('requests.wizard', ['requestModel' => null, 'step' => $service ? 2 : 1, 'service' => $service?->value, 'catalog' => $service ? RequestCatalog::types($service) : [], 'services' => RequestCatalog::services()]);
    }

    public function store(WizardStepRequest $request, RequestDraftService $drafts)
    {
        $this->authorize('create', CreativeRequest::class);
        $model = $drafts->create($request->validated(), $request->user()->id);

        return redirect()->route('app.requests.drafts.edit', ['creativeRequest' => $model, 'step' => 2])->with('status', 'Borrador guardado');
    }

    public function edit(CreativeRequest $creativeRequest, Request $request): View
    {
        $this->authorize('view', $creativeRequest);
        if (! $creativeRequest->isDraft()) {
            return view('requests.confirmation', ['requestModel' => $creativeRequest]);
        }
        $service = $creativeRequest->service;
        $step = min(max((int) $request->integer('step', $creativeRequest->current_step), 1), 6);

        return view('requests.wizard', ['requestModel' => $creativeRequest->load(['detail', 'files']), 'step' => $step, 'service' => $service->value, 'catalog' => RequestCatalog::types($service), 'services' => RequestCatalog::services()]);
    }

    public function update(WizardStepRequest $request, CreativeRequest $creativeRequest, RequestDraftService $drafts, RecommendedDateService $dates)
    {
        $this->authorize('update', $creativeRequest);
        $step = (int) $request->input('step');
        $data = $request->validated();
        $service = $data['service'] ?? $creativeRequest->service;
        if ($step === 5 && $dates->isShort($service, $data['required_date']) && blank($data['urgency_reason'] ?? null)) {
            return back()->withErrors(['urgency_reason' => 'Explica el motivo de la fecha corta para continuar.'])->withInput();
        }
        $model = $drafts->update($creativeRequest, $data, $step);

        return redirect()->route('app.requests.drafts.edit', ['creativeRequest' => $model, 'step' => min($step + 1, 6)])->with('status', 'Cambios guardados');
    }

    public function autosave(WizardStepRequest $request, CreativeRequest $creativeRequest, RequestDraftService $drafts)
    {
        $this->authorize('update', $creativeRequest);
        $model = $drafts->update($creativeRequest, $request->validated(), (int) $request->input('step', 1));

        return response()->json(['saved' => true, 'saved_at' => $model->last_autosaved_at?->toIso8601String()]);
    }
}
