<?php

namespace Tests\Feature\Requests;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\CreativeRequest;
use App\Models\User;
use App\Services\AI\GeminiRequestReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiRequestReviewTest extends TestCase
{
    use RefreshDatabase;

    private function reviewPayload(string $status = 'ready'): array
    {
        return ['status' => $status, 'summary' => 'Solicitud clara.', 'corrected_text' => 'Texto corregido.', 'spelling_corrections' => [], 'missing_information' => [], 'ambiguous_instructions' => [], 'contradictions' => [], 'recommendations' => []];
    }

    public function test_marketing_can_review_a_request_without_changing_its_status_or_original_text(): void
    {
        config(['gemini.api_key' => 'test-key']);
        $response = ['candidates' => []];
        $response['candidates'][] = ['content' => ['parts' => []]];
        $response['candidates'][0]['content']['parts'][] = ['text' => json_encode($this->reviewPayload())];
        Http::fake(['https://generativelanguage.googleapis.com/*' => Http::response($response, 200)]);
        $user = User::factory()->create(['role' => UserRole::MARKETING]);
        $request = CreativeRequest::factory()->create(['requester_id' => $user->id, 'description' => 'publicasion para redes']);

        $this->actingAs($user)->postJson(route('app.requests.drafts.ai-review', $request))->assertOk()->assertJsonPath('result.status', 'ready');
        $this->assertSame(RequestStatus::DRAFT, $request->fresh()->status);
        $this->assertSame('publicasion para redes', $request->fresh()->description);
        $this->assertSame('ready', $request->fresh()->ai_review_status);
    }

    public function test_invalid_json_and_gemini_failure_are_saved_as_retryable_errors(): void
    {
        config(['gemini.api_key' => 'test-key']);
        $user = User::factory()->create(['role' => UserRole::MARKETING]);
        $request = CreativeRequest::factory()->create(['requester_id' => $user->id]);
        $invalid = ['candidates' => []];
        $invalid['candidates'][] = ['content' => ['parts' => [['text' => '{invalid']]]];
        Http::fake(['https://generativelanguage.googleapis.com/*' => Http::response($invalid, 200)]);
        $this->actingAs($user)->postJson(route('app.requests.drafts.ai-review', $request))->assertStatus(503)->assertJsonPath('ok', false);
        $this->assertSame('error', $request->fresh()->ai_review_status);
        Http::fake(['https://generativelanguage.googleapis.com/*' => Http::failedConnection()]);
        $this->actingAs($user)->postJson(route('app.requests.drafts.ai-review', $request))->assertStatus(503);
    }

    public function test_incomplete_review_is_returned_without_mutating_the_request(): void
    {
        config(['gemini.api_key' => 'test-key']);
        $incomplete = ['status' => 'incomplete', 'summary' => 'Faltan datos.', 'corrected_text' => 'Texto', 'spelling_corrections' => [], 'missing_information' => ['Medidas'], 'ambiguous_instructions' => [], 'contradictions' => [], 'recommendations' => []];
        $response = ['candidates' => []];
        $response['candidates'][] = ['content' => ['parts' => []]];
        $response['candidates'][0]['content']['parts'][] = ['text' => json_encode($incomplete)];
        Http::fake(['https://generativelanguage.googleapis.com/*' => Http::response($response, 200)]);
        $user = User::factory()->create(['role' => UserRole::MARKETING]);
        $request = CreativeRequest::factory()->create(['requester_id' => $user->id, 'description' => 'Necesito un diseño']);

        $this->actingAs($user)->postJson(route('app.requests.drafts.ai-review', $request))->assertOk()->assertJsonPath('result.status', 'incomplete');
        $this->assertSame(RequestStatus::DRAFT, $request->fresh()->status);
    }

    public function test_marketing_can_send_manually_when_review_is_unavailable(): void
    {
        config(['gemini.api_key' => null]);
        $user = User::factory()->create(['role' => UserRole::MARKETING]);
        $request = CreativeRequest::factory()->create(['requester_id' => $user->id, 'title' => 'Enviar manual', 'description' => 'Descripción', 'required_date' => now()->addDays(3), 'requested_priority' => 'medium']);

        $this->actingAs($user)->postJson(route('app.requests.drafts.ai-review', $request))->assertStatus(503);
        $this->actingAs($user)->post(route('app.requests.drafts.submit', $request), ['confirmed' => '1', 'send_without_ai' => '1'])->assertRedirect(route('app.requests.confirmation', $request));
    }

    public function test_marketing_can_apply_the_corrected_text_only_after_confirmation(): void
    {
        $user = User::factory()->create(['role' => UserRole::MARKETING]);
        $request = CreativeRequest::factory()->create(['requester_id' => $user->id, 'description' => 'Texto original']);

        $request->update(['title' => 'Prueva de título']);
        $this->actingAs($user)->postJson(route('app.requests.drafts.ai-review.apply-correction', $request), ['corrected_text' => 'Texto corregido', 'corrections' => [['original' => 'Prueva', 'corrected' => 'Prueba']]])->assertOk();
        $this->assertSame('Texto corregido', $request->fresh()->description);
        $this->assertSame('Prueba de título', $request->fresh()->title);
        $this->assertDatabaseHas('creative_request_events', ['creative_request_id' => $request->id, 'event' => 'ai_correction_applied']);
    }

    public function test_malformed_full_request_text_is_not_applied_as_a_description(): void
    {
        config(['gemini.api_key' => 'test-key']);
        $payload = $this->reviewPayload();
        $payload['corrected_text'] = 'SOLICITUD: {"description":"Texto completo"}';
        $response = ['candidates' => []];
        $response['candidates'][] = ['content' => ['parts' => []]];
        $response['candidates'][0]['content']['parts'][] = ['text' => json_encode($payload)];
        Http::fake(['https://generativelanguage.googleapis.com/*' => Http::response($response, 200)]);
        $request = CreativeRequest::factory()->create(['description' => 'Descripción original']);

        $result = app(GeminiRequestReviewService::class)->review($request);
        $this->assertSame('Descripción original', $result['corrected_text']);
    }

    public function test_creative_users_cannot_run_the_marketing_review(): void
    {
        config(['gemini.api_key' => 'test-key']);
        $request = CreativeRequest::factory()->create();
        $this->actingAs(User::factory()->create(['role' => UserRole::DESIGN]))->postJson(route('app.requests.drafts.ai-review', $request))->assertForbidden();
    }
}
