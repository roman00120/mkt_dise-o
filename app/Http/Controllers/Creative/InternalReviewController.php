<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveInternalReviewRequest;
use App\Http\Requests\RequestInternalChangesRequest;
use App\Http\Requests\SubmitInternalReviewRequest;
use App\Models\Deliverable;
use App\Models\DeliverableVersion;
use App\Services\Deliverables\InternalReviewService;
use App\Services\Deliverables\MarketingReviewService;
use Illuminate\Http\RedirectResponse;

class InternalReviewController extends Controller
{
    public function submit(SubmitInternalReviewRequest $request, Deliverable $deliverable, DeliverableVersion $version, InternalReviewService $reviews): RedirectResponse
    {
        abort_unless($version->deliverable_id === $deliverable->id, 404);
        $reviews->submit($version, $request->user());

        return back()->with('status', 'La versión fue enviada a revisión interna.');
    }

    public function submitFinal(SubmitInternalReviewRequest $request, Deliverable $deliverable, DeliverableVersion $version, MarketingReviewService $marketing): RedirectResponse
    {
        abort_unless($version->deliverable_id === $deliverable->id, 404);
        $marketing->submitFinal($version, $request->user());

        return back()->with('status', 'El diseño final fue enviado a Marketing.');
    }

    public function approve(ApproveInternalReviewRequest $request, Deliverable $deliverable, DeliverableVersion $version, InternalReviewService $reviews): RedirectResponse
    {
        abort_unless($version->deliverable_id === $deliverable->id, 404);
        $reviews->approve($version, $request->user());

        return back()->with('status', 'La versión quedó lista para Marketing.');
    }

    public function changes(RequestInternalChangesRequest $request, Deliverable $deliverable, DeliverableVersion $version, InternalReviewService $reviews): RedirectResponse
    {
        abort_unless($version->deliverable_id === $deliverable->id, 404);
        $reviews->requestChanges($version, $request->user(), $request->validated('reason'));

        return back()->with('status', 'Se solicitaron cambios internos.');
    }
}
