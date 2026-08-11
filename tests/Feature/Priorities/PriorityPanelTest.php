<?php

namespace Tests\Feature\Priorities;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriorityPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_carolina_can_reorder_requests_and_marketing_can_view_only(): void
    {
        $carolina = User::factory()->create(['name' => 'Ana Carolina Román', 'email' => 'ana.roman@totalground.com', 'role' => UserRole::CREATIVE]);
        $marketing = User::factory()->create(['role' => UserRole::MARKETING]);
        $first = CreativeRequest::factory()->status(RequestStatus::IN_PROGRESS)->create(['title' => 'Primera']);
        $second = CreativeRequest::factory()->status(RequestStatus::IN_PROGRESS)->create(['title' => 'Segunda']);
        $first->forceFill(['priority_order' => 1])->save();
        $second->forceFill(['priority_order' => 2])->save();

        $this->actingAs($carolina)->get(route('priorities.index'))->assertOk()->assertSee('Prioridades de solicitudes');
        $this->actingAs($carolina)->post(route('priorities.move', $second), ['direction' => 'up'])->assertRedirect();
        $this->assertSame(1, $second->fresh()->priority_order);
        $this->assertSame(2, $first->fresh()->priority_order);
        $this->assertDatabaseHas('creative_request_events', ['creative_request_id' => $second->id, 'event' => 'priority_reordered']);

        $this->actingAs($marketing)->get(route('priorities.index'))->assertOk()->assertSee('Vista informativa');
        $this->actingAs($marketing)->post(route('priorities.move', $first), ['direction' => 'up'])->assertForbidden();
    }
}
