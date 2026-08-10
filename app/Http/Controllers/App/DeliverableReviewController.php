<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveDeliverableRequest;
use App\Http\Requests\RequestMarketingCorrectionsRequest;
use App\Models\CreativeRequest;
use App\Models\Deliverable;
use App\Models\DeliverableVersion;
use App\Services\Deliverables\CorrectionRequestService;
use App\Services\Deliverables\DeliverableApprovalService;
use App\Services\Notifications\BusinessNotificationService;
use Illuminate\Http\RedirectResponse;

class DeliverableReviewController extends Controller
{
    public function approve(ApproveDeliverableRequest $request, CreativeRequest $creativeRequest, Deliverable $deliverable, DeliverableVersion $version, DeliverableApprovalService $approval): RedirectResponse
    {
        $this->assert($creativeRequest, $deliverable, $version);
        $approval->approve($version, $request->user(), $request->validated('comments'));

        return back()->with('status', 'El entregable fue aprobado.');
    }

    public function corrections(RequestMarketingCorrectionsRequest $request, CreativeRequest $creativeRequest, Deliverable $deliverable, DeliverableVersion $version, CorrectionRequestService $corrections, BusinessNotificationService $notifications): RedirectResponse
    {
        $this->assert($creativeRequest, $deliverable, $version);
        $corrections->marketing($version, $request->user(), $request->validated(), $notifications);

        return back()->with('status', 'Se solicitaron correcciones al equipo creativo.');
    }

    private function assert(CreativeRequest $request, Deliverable $deliverable, DeliverableVersion $version): void
    {
        abort_unless($deliverable->creative_request_id === $request->id && $version->deliverable_id === $deliverable->id, 404);
    }
}
