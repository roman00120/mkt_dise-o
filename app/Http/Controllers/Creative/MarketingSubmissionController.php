<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendDeliverableToMarketingRequest;
use App\Models\Deliverable;
use App\Models\DeliverableVersion;
use App\Services\Deliverables\MarketingReviewService;
use Illuminate\Http\RedirectResponse;

class MarketingSubmissionController extends Controller
{
    public function __invoke(SendDeliverableToMarketingRequest $request, Deliverable $deliverable, DeliverableVersion $version, MarketingReviewService $service): RedirectResponse
    {
        abort_unless($version->deliverable_id === $deliverable->id, 404);
        $service->send($version, $request->user());

        return back()->with('status', 'El entregable fue enviado a Marketing.');
    }
}
