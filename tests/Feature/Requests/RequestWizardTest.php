<?php

namespace Tests\Feature\Requests;

use App\Enums\CreativeService;
use App\Enums\RequestStatus;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RequestWizardTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_can_open_wizard_and_preselect_only_known_service(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('app.requests.create', ['service' => 'video']))->assertOk()->assertSee('Selecciona el tipo de solicitud');
        $this->actingAs($user)->get(route('app.requests.create', ['service' => 'unknown']))->assertOk()->assertSee('tipo de apoyo creativo necesitas');
    }

    public function test_creative_roles_cannot_open_wizard(): void
    {
        $user = User::factory()->create(['role' => 'design']);
        $this->actingAs($user)->get(route('app.requests.create'))->assertForbidden();
    }

    public function test_service_step_creates_persistent_draft_with_unique_folio(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('app.requests.store'), ['step' => 1, 'service' => 'design']);
        $request = CreativeRequest::first();
        $response->assertRedirect(route('app.requests.drafts.edit', ['creativeRequest' => $request, 'step' => 2]));
        $this->assertMatchesRegularExpression('/^TG-'.now()->year.'-\d{4}$/', $request->folio);
        $this->assertSame(RequestStatus::DRAFT, $request->status);
    }

    public function test_brief_and_urgent_priority_are_validated_on_backend(): void
    {
        $user = User::factory()->create();
        $draft = CreativeRequest::factory()->create(['requester_id' => $user->id, 'service' => CreativeService::VIDEO]);
        $this->actingAs($user)->patch(route('app.requests.drafts.update', $draft), ['step' => 3, 'service' => 'video', 'request_type' => 'reel'])->assertSessionHasErrors(['title', 'description', 'details.video_type']);
        $this->actingAs($user)->patch(route('app.requests.drafts.update', $draft), ['step' => 5, 'service' => 'video', 'required_date' => now()->addDays(3)->toDateString(), 'requested_priority' => 'urgent'])->assertSessionHasErrors('urgency_reason');
    }

    public function test_owner_can_submit_once_and_other_user_cannot_edit(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $draft = CreativeRequest::factory()->create(['requester_id' => $owner->id]);
        $this->actingAs($other)->get(route('app.requests.drafts.edit', $draft))->assertForbidden();
        $this->actingAs($owner)->post(route('app.requests.drafts.submit', $draft), ['confirmed' => '1'])->assertSessionHasErrors();
        $draft->update(['title' => 'Título', 'description' => 'Descripción', 'required_date' => now()->addDays(5), 'requested_priority' => 'medium']);
        $this->actingAs($owner)->post(route('app.requests.drafts.submit', $draft), ['confirmed' => '1'])->assertRedirect(route('app.requests.confirmation', $draft));
        $this->assertSame(RequestStatus::PENDING, $draft->fresh()->status);
        $this->actingAs($owner)->post(route('app.requests.drafts.submit', $draft), ['confirmed' => '1'])->assertForbidden();
    }

    public function test_valid_reference_upload_is_private_and_invalid_extension_fails(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $draft = CreativeRequest::factory()->create(['requester_id' => $user->id]);
        $this->actingAs($user)->post(route('app.requests.drafts.files.store', $draft), ['file' => UploadedFile::fake()->create('reference.png', 10, 'image/png'), 'category' => 'reference'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('creative_request_files', ['creative_request_id' => $draft->id, 'category' => 'reference', 'disk' => 'local']);
        $this->actingAs($user)->post(route('app.requests.drafts.files.store', $draft), ['file' => UploadedFile::fake()->create('malware.exe',10)])->assertSessionHasErrors('file');
    }
}
