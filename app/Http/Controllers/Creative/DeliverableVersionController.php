<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliverableVersionRequest;
use App\Http\Requests\UpdateDeliverableVersionRequest;
use App\Models\Deliverable;
use App\Models\DeliverableVersion;
use App\Services\Deliverables\DeliverableVersionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeliverableVersionController extends Controller
{
    public function store(StoreDeliverableVersionRequest $request, Deliverable $deliverable, DeliverableVersionService $versions): RedirectResponse
    {
        $this->authorize('create', $deliverable);
        $versions->assertCanCreate($deliverable);
        $version = $versions->create($deliverable, $request->user(), $request->validated('notes'), $request->validated('internal_notes'));

        return redirect()->route('creative.deliverables.versions.edit', [$deliverable, $version]);
    }

    public function edit(Deliverable $deliverable, DeliverableVersion $version): View
    {
        $this->assertRelation($deliverable, $version);
        $this->authorize('view', $version);
        $version->load(['files', 'corrections' => fn ($q) => $q->where('status', 'open')]);

        return view('creative.deliverables.versions.edit', compact('deliverable', 'version'));
    }

    public function update(UpdateDeliverableVersionRequest $request, Deliverable $deliverable, DeliverableVersion $version): RedirectResponse
    {
        $this->assertRelation($deliverable, $version);
        $version->update($request->validated());

        return back()->with('status', 'La versión fue guardada.');
    }

    private function assertRelation(Deliverable $deliverable, DeliverableVersion $version): void
    {
        abort_unless($version->deliverable_id === $deliverable->id, 404);
    }
}
