<?php

namespace Tests\Feature\Deliverables;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeliverableFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_creative_can_create_version_upload_and_submit_for_internal_review(): void
    {
        Storage::fake('local');
        [$marketing, $designer, $supervisor, $request] = $this->requestSet();
        $this->actingAs($designer)->post(route('creative.requests.deliverables.store', $request))->assertRedirect();
        $deliverable = $request->deliverables()->firstOrFail();
        $version = $deliverable->currentVersion;
        $this->actingAs($designer)->post(route('creative.deliverables.versions.files.store', [$deliverable, $version]), ['file' => UploadedFile::fake()->create('preview.pdf', 20, 'application/pdf'), 'category' => 'preview', 'is_primary' => 1])->assertRedirect();
        $this->actingAs($designer)->patch(route('creative.deliverables.versions.update', [$deliverable, $version]), ['notes' => 'Entrega lista'])->assertRedirect();
        $this->actingAs($designer)->post(route('creative.deliverables.versions.submit-internal', [$deliverable, $version]))->assertRedirect();
        $this->assertSame('internal_review', $version->fresh()->status->value);
        $this->assertTrue(Storage::disk('local')->exists($version->fresh()->files->first()->path));
    }

    public function test_supervisor_sends_version_to_marketing_and_marketing_can_approve_only_current_version(): void
    {
        Storage::fake('local');
        [$marketing, $designer, $supervisor, $request] = $this->requestSet(RequestStatus::IN_PROGRESS);
        $deliverable = $this->actingAs($designer)->post(route('creative.requests.deliverables.store', $request))->assertRedirect()->getSession()->get('url');
        $deliverable = $request->deliverables()->firstOrFail();
        $version = $deliverable->currentVersion;
        $this->actingAs($designer)->post(route('creative.deliverables.versions.files.store', [$deliverable, $version]), ['file' => UploadedFile::fake()->create('final.pdf', 10, 'application/pdf'), 'category' => 'final', 'is_primary' => 1]);
        $version->update(['notes' => 'Entrega']);
        $this->actingAs($designer)->post(route('creative.deliverables.versions.submit-internal', [$deliverable, $version]));
        $this->actingAs($supervisor)->post(route('creative.deliverables.versions.internal-approve', [$deliverable, $version]))->assertRedirect();
        $this->actingAs($supervisor)->post(route('creative.deliverables.versions.send-marketing', [$deliverable, $version]))->assertRedirect();
        $this->assertSame('marketing_review', $request->fresh()->status->value);
        $this->actingAs($marketing)->post(route('app.requests.deliverables.approve', [$request, $deliverable, $version]), ['confirmed' => 1])->assertRedirect();
        $this->assertContains($request->fresh()->status->value, ['approved', 'completed']);
        $this->assertSame($version->id, $deliverable->fresh()->approved_version_id);
    }

    public function test_marketing_corrections_create_new_iteration_and_do_not_edit_old_version(): void
    {
        $marketing = User::factory()->create(['role' => UserRole::MARKETING]);
        $designer = User::factory()->create(['role' => UserRole::DESIGN]);
        $request = CreativeRequest::factory()->create(['requester_id' => $marketing->id, 'service' => 'design', 'status' => RequestStatus::MARKETING_REVIEW, 'assignee_id' => $designer->id]);
        $deliverable = $request->deliverables()->create(['uuid' => fake()->uuid(), 'created_by' => $designer->id, 'title' => 'Entrega', 'status' => 'marketing_review']);
        $version = $deliverable->versions()->create(['uuid' => fake()->uuid(), 'version_number' => 1, 'created_by' => $designer->id, 'status' => 'marketing_review', 'notes' => 'Entrega']);
        $deliverable->update(['current_version_id' => $version->id]);
        $this->actingAs($marketing)->post(route('app.requests.deliverables.request-corrections', [$request, $deliverable, $version]), ['summary' => 'Ajustar color', 'details' => 'Usar el tono de marca'])->assertRedirect();
        $this->assertSame('corrections_requested', $request->fresh()->status->value);
        $this->assertSame('marketing_changes_requested', $version->fresh()->status->value);
        $this->actingAs($designer)->post(route('creative.deliverables.versions.store', $deliverable), ['notes' => 'Nueva versión']);
        $this->assertSame(2, $deliverable->versions()->max('version_number'));
        $this->assertSame('marketing_changes_requested', $version->fresh()->status->value);
    }

    private function requestSet(RequestStatus $status = RequestStatus::IN_PROGRESS): array
    {
        $marketing = User::factory()->create(['role' => UserRole::MARKETING]);
        $designer = User::factory()->create(['role' => UserRole::DESIGN]);
        $supervisor = User::factory()->create(['role' => UserRole::SUPERVISOR]);
        $request = CreativeRequest::factory()->create(['requester_id' => $marketing->id, 'service' => 'design', 'status' => $status, 'assignee_id' => $designer->id, 'title' => 'Entrega', 'description' => 'Brief']);

        return [$marketing, $designer, $supervisor, $request];
    }
}
