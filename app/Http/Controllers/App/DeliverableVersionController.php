<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use App\Models\Deliverable;
use App\Models\DeliverableVersion;
use Illuminate\View\View;

class DeliverableVersionController extends Controller
{
    public function show(CreativeRequest $creativeRequest, Deliverable $deliverable, DeliverableVersion $version): View
    {
        abort_unless($deliverable->creative_request_id === $creativeRequest->id && $version->deliverable_id === $deliverable->id, 404);
        $this->authorize('view', $creativeRequest);
        abort_unless(in_array($version->status->value, ['marketing_review', 'approved', 'superseded'], true), 404);
        $version->load(['files', 'reviews.reviewer']);

        return view('requests.deliverables.version', compact('creativeRequest', 'deliverable', 'version'));
    }
}
