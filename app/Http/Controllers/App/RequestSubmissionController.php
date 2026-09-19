<?php

namespace App\Http\Controllers\App;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use App\Models\User;
use App\Notifications\CreativeRequestSubmittedNotification;
use App\Services\Requests\RequestSubmissionService;
use App\Services\AI\GeminiRequestReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Throwable;

class RequestSubmissionController extends Controller
{
    public function applyCorrection(Request $request, CreativeRequest $creativeRequest)
    {
        $this->authorize('update', $creativeRequest);
        abort_unless($creativeRequest->isDraft(), 409);
        $data = $request->validate(['corrected_text' => ['required', 'string', 'max:10000'], 'corrections' => ['array', 'max:50'], 'corrections.*.original' => ['required', 'string', 'max:200'], 'corrections.*.corrected' => ['required', 'string', 'max:200']]);
        $fields = ['title', 'description', 'objective', 'target_audience', 'channel', 'urgency_reason', 'other_request_type'];
        $updates = ['description' => $data['corrected_text']];
        foreach ($fields as $field) {
            $value = (string) $creativeRequest->{$field};
            foreach ($data['corrections'] ?? [] as $correction) $value = str_ireplace($correction['original'], $correction['corrected'], $value);
            if ($field !== 'description' && $value !== (string) $creativeRequest->{$field}) $updates[$field] = $value;
        }
        $creativeRequest->update($updates + ['last_autosaved_at' => now()]);
        $creativeRequest->events()->create(['actor_id' => $request->user()->id, 'event' => 'ai_correction_applied']);

        return response()->json(['ok' => true]);
    }

    public function review(Request $request, CreativeRequest $creativeRequest, GeminiRequestReviewService $reviewer)
    {
        $this->authorize('update', $creativeRequest);
        abort_unless($creativeRequest->isDraft(), 409);
        $creativeRequest->update(['ai_review_status' => 'reviewing', 'ai_review_error' => null]);

        try {
            $result = $reviewer->review($creativeRequest->fresh(['detail', 'files']));
            $creativeRequest->update(['ai_review_status' => $result['status'], 'ai_review_result' => $result, 'ai_reviewed_at' => now(), 'ai_review_error' => null]);
            return response()->json(['ok' => true, 'result' => $result]);
        } catch (Throwable $exception) {
            report($exception);
            $creativeRequest->update(['ai_review_status' => 'error', 'ai_review_error' => 'No fue posible completar la revisión.']);
            return response()->json(['ok' => false, 'message' => 'La revisión no está disponible. Puedes reintentar o enviar manualmente.'], 503);
        }
    }

    public function submit(Request $request, CreativeRequest $creativeRequest, RequestSubmissionService $submission)
    {
        $this->authorize('update', $creativeRequest);
        abort_unless($creativeRequest->isDraft(), 409);
        $request->validate(['confirmed' => ['accepted']]);
        $this->validateFinal($creativeRequest);
        $model = $submission->submit($creativeRequest);
        $admins = User::query()->where('role', UserRole::ADMIN)->where('status', 'active')->get();
        Notification::send($admins, new CreativeRequestSubmittedNotification($model->load('requester')));

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
