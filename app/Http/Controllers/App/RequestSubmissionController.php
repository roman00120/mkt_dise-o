<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use App\Services\Requests\RequestSubmissionService;
use Illuminate\Http\Request;

class RequestSubmissionController extends Controller
{
    public function submit(Request $request, CreativeRequest $creativeRequest, RequestSubmissionService $submission)
    {
        $this->authorize('update', $creativeRequest);
        abort_unless($creativeRequest->isDraft(), 409);
        $request->validate(['confirmed' => ['accepted']]);
        $this->validateFinal($creativeRequest);
        $model = $submission->submit($creativeRequest);

        return redirect()->route('app.requests.confirmation', $model);
    }

    public function confirmation(CreativeRequest $creativeRequest)
    {
        $this->authorize('view', $creativeRequest);

        return view('requests.confirmation', ['requestModel' => $creativeRequest->load(['detail', 'files'])]);
    }

    private function validateFinal(CreativeRequest $model): void
    {
        validator($model->toArray(), ['service' => ['required'], 'request_type' => ['required'], 'title' => ['required'], 'description' => ['required'], 'required_date' => ['required'], 'requested_priority' => ['required']])->validate();
        abort_if($model->requested_priority->value === 'urgent' && blank($model->urgency_reason), 422, 'Justifica la urgencia para continuar.');
    }
}
