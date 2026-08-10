<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use App\Models\Deliverable;
use Illuminate\View\View;

class DeliverableController extends Controller
{
    public function index(CreativeRequest $creativeRequest): View
    {
        $this->authorize('view', $creativeRequest);
        $deliverables = $creativeRequest->deliverables()->with(['currentVersion.files', 'versions'])->get();

        return view('requests.deliverables.index', compact('creativeRequest', 'deliverables'));
    }

    public function show(CreativeRequest $creativeRequest, Deliverable $deliverable): View
    {
        abort_unless($deliverable->creative_request_id === $creativeRequest->id, 404);
        $this->authorize('view', $creativeRequest);
        $deliverable->load(['currentVersion.files', 'versions.creator', 'versions.files', 'versions.reviews', 'comments' => fn ($query) => $query->where('visibility', 'public')->whereNull('parent_id')->withTrashed()->with(['author', 'mentions.user', 'attachments', 'replies.author'])]);

        return view('requests.deliverables.show', compact('creativeRequest', 'deliverable'));
    }
}
