<?php

namespace Tests\Feature\Creative;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreativeOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_creative_access_is_limited_by_role_and_service(): void
    {
        $design = User::factory()->create(['role' => UserRole::DESIGN]);
        $request = CreativeRequest::factory()->create(['service' => 'design', 'status' => RequestStatus::PENDING]);
        CreativeRequest::factory()->create(['service' => 'video', 'status' => RequestStatus::PENDING]);

        $this->actingAs($design)->get(route('creative.dashboard'))->assertOk()->assertSee('Panel creativo');
        $this->actingAs($design)->get(route('creative.requests.index'))->assertOk()->assertSee($request->folio);
        $this->actingAs($design)->get(route('creative.requests.show', $request))->assertOk()->assertSee('Brief');
        $this->actingAs(User::factory()->create(['role' => UserRole::MARKETING]))->get(route('creative.dashboard'))->assertForbidden();
    }

    public function test_pending_requests_are_shown_even_when_older_than_the_dashboard_recent_items(): void
    {
        $supervisor = User::factory()->create(['role' => UserRole::SUPERVISOR]);
        $pending = CreativeRequest::factory()->create(['service' => 'design', 'status' => RequestStatus::PENDING]);
        CreativeRequest::factory()->count(12)->create(['service' => 'design', 'status' => RequestStatus::IN_PROGRESS]);

        $this->actingAs($supervisor)
            ->get(route('creative.dashboard'))
            ->assertOk()
            ->assertSee($pending->folio);
    }

    public function test_assigned_requests_waiting_for_admin_approval_are_shown_in_validation_queue(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $assigned = CreativeRequest::factory()->create(['service' => 'design', 'status' => RequestStatus::ASSIGNED]);

        $this->actingAs($admin)
            ->get(route('creative.dashboard'))
            ->assertOk()
            ->assertSee($assigned->folio);
    }

    public function test_supervisor_can_validate_and_assign_only_matching_active_role(): void
    {
        $supervisor = User::factory()->create(['role' => UserRole::SUPERVISOR]);
        $designer = User::factory()->create(['role' => UserRole::DESIGN]);
        $video = User::factory()->create(['role' => UserRole::VIDEO]);
        $request = CreativeRequest::factory()->create(['service' => 'design', 'status' => RequestStatus::IN_VALIDATION, 'title' => 'Brief', 'description' => 'Detalle', 'required_date' => today()->addDays(5)]);

        $this->actingAs($supervisor)->post(route('creative.requests.validate', $request), ['assignee_id' => $designer->id, 'operational_priority' => 'high', 'internal_due_date' => today()->addDays(3)->toDateString()])->assertRedirect();
        $this->assertSame(RequestStatus::ASSIGNED, $request->fresh()->status);
        $this->assertSame('high', $request->fresh()->operational_priority->value);
        $this->assertSame($designer->id, $request->fresh()->assignee_id);
        $this->actingAs($supervisor)->post(route('creative.requests.reassign', $request), ['assignee_id' => $video->id, 'reason' => 'Cambio de carga'])->assertSessionHasErrors('assignee_id');
    }

    public function test_assignee_can_request_information_and_marketing_can_respond(): void
    {
        $designer = User::factory()->create(['role' => UserRole::DESIGN]);
        $marketing = User::factory()->create(['role' => UserRole::MARKETING]);
        $request = CreativeRequest::factory()->create(['requester_id' => $marketing->id, 'service' => 'design', 'status' => RequestStatus::ASSIGNED, 'assignee_id' => $designer->id]);

        $this->actingAs($designer)->post(route('creative.requests.request-information', $request), ['message' => 'Faltan medidas', 'category' => 'measures'])->assertRedirect();
        $this->assertSame(RequestStatus::WAITING_FOR_INFORMATION, $request->fresh()->status);
        $this->actingAs($marketing)->post(route('app.requests.provide-information', $request), ['response' => 'Medidas adjuntas en el brief'])->assertRedirect();
        $this->assertSame(RequestStatus::ASSIGNED, $request->fresh()->status);
        $this->assertDatabaseHas('creative_request_events', ['event' => 'information_provided']);
    }
}
