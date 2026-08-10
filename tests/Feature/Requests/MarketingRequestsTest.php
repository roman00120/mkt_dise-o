<?php

namespace Tests\Feature\Requests;

use App\Enums\RequestPriority;
use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\CreativeRequest;
use App\Models\CreativeRequestFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketingRequestsTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_index_is_scoped_and_supports_search_and_filters(): void
    {
        $owner = User::factory()->create(['role' => UserRole::MARKETING]);
        $other = User::factory()->create(['role' => UserRole::MARKETING]);
        CreativeRequest::factory()->create(['requester_id' => $owner->id, 'title' => 'Catálogo A500', 'status' => RequestStatus::PENDING, 'requested_priority' => RequestPriority::HIGH]);
        CreativeRequest::factory()->create(['requester_id' => $other->id, 'title' => 'Privada']);

        $this->actingAs($owner)->get(route('app.requests.index', ['q' => 'A500', 'priority' => 'high']))
            ->assertOk()->assertSee('Catálogo A500')->assertDontSee('Privada');
    }

    public function test_creative_roles_cannot_use_marketing_requests(): void
    {
        foreach ([UserRole::DESIGN, UserRole::VIDEO, UserRole::RENDER] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))->get(route('app.requests.index'))->assertForbidden();
        }
    }

    public function test_detail_and_private_download_require_ownership(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create(['role' => UserRole::MARKETING]);
        $other = User::factory()->create(['role' => UserRole::MARKETING]);
        $request = CreativeRequest::factory()->create(['requester_id' => $owner->id, 'status' => RequestStatus::PENDING]);
        Storage::disk('local')->put('creative-requests/test.pdf', 'content');
        $file = CreativeRequestFile::create(['creative_request_id' => $request->id, 'uploaded_by' => $owner->id, 'original_name' => 'test.pdf', 'stored_name' => 'test.pdf', 'disk' => 'local', 'path' => 'creative-requests/test.pdf', 'mime_type' => 'application/pdf', 'extension' => 'pdf', 'size' => 7, 'category' => 'reference']);

        $this->actingAs($owner)->get(route('app.requests.show', $request))->assertOk()->assertSee($request->folio);
        $this->actingAs($owner)->get(route('app.requests.files.download', [$request, $file]))->assertDownload('test.pdf');
        $this->actingAs($other)->get(route('app.requests.show', $request))->assertForbidden();
    }

    public function test_duplication_creates_a_new_draft_without_files(): void
    {
        $owner = User::factory()->create(['role' => UserRole::MARKETING]);
        $request = CreativeRequest::factory()->create(['requester_id' => $owner->id, 'status' => RequestStatus::PENDING, 'title' => 'Original']);
        $request->detail()->create(['data' => ['format' => 'PDF']]);

        $response = $this->actingAs($owner)->post(route('app.requests.duplicate', $request));
        $copy = CreativeRequest::where('duplicated_from_id', $request->id)->firstOrFail();
        $response->assertRedirect(route('app.requests.drafts.edit', $copy));
        $this->assertSame(RequestStatus::DRAFT, $copy->status);
        $this->assertNotSame($request->folio, $copy->folio);
        $this->assertCount(1, $copy->events);
        $this->assertCount(0, $copy->files);
    }

    public function test_cancellation_requires_reason_and_is_limited_to_allowed_states(): void
    {
        $owner = User::factory()->create(['role' => UserRole::MARKETING]);
        $request = CreativeRequest::factory()->create(['requester_id' => $owner->id, 'status' => RequestStatus::PENDING]);
        $this->actingAs($owner)->post(route('app.requests.cancel', $request), [])->assertSessionHasErrors('reason');
        $this->actingAs($owner)->post(route('app.requests.cancel', $request), ['reason' => 'Ya no se necesita'])->assertRedirect();
        $this->assertSame(RequestStatus::CANCELLED, $request->fresh()->status);
        $this->assertSame(1, $request->fresh()->events()->where('event', 'request_cancelled')->count());
        $this->actingAs($owner)->post(route('app.requests.cancel', $request), ['reason' => 'Otra'])->assertRedirect();
        $this->assertSame(1, $request->fresh()->events()->where('event', 'request_cancelled')->count());
    }
}
