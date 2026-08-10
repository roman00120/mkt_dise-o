<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliverableRequest;
use App\Models\CreativeRequest;
use App\Models\Deliverable;
use App\Services\Deliverables\DeliverableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeliverableController extends Controller
{
    public function store(StoreDeliverableRequest $request, CreativeRequest $creativeRequest, DeliverableService $service): RedirectResponse
    {
        abort_unless($request->user()->id === $creativeRequest->assignee_id || $request->user()->hasRole('admin', 'supervisor'), 403);
        $deliverable = $service->principal($creativeRequest, $request->user());

        return redirect()->route('creative.requests.deliverable.show', [$creativeRequest, $deliverable]);
    }

    public function show(CreativeRequest $creativeRequest, Deliverable $deliverable): View
    {
        abort_unless($deliverable->creative_request_id === $creativeRequest->id, 404);
        $this->authorize('view', $deliverable);
        $deliverable->load(['request', 'currentVersion.files', 'versions.creator', 'versions.files', 'versions.reviews.reviewer', 'correctionRequests', 'comments' => fn ($query) => $query->whereNull('parent_id')->withTrashed()->with(['author', 'mentions.user', 'attachments', 'replies.author'])]);

        return view('creative.deliverables.show', compact('creativeRequest', 'deliverable'));
    }
}
