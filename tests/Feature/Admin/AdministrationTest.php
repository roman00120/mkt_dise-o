<?php

namespace Tests\Feature\Admin;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdministrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_active_admin_can_access_the_administration_area(): void
    {
        $this->actingAs(User::factory()->create(['role' => UserRole::MARKETING]))
            ->get(route('admin.dashboard'))->assertForbidden();

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Panel de administración');
    }

    public function test_admin_can_create_user_and_audit_event_is_recorded(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Nuevo operador', 'email' => 'operator@example.test', 'role' => 'design', 'status' => 'inactive',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'operator@example.test', 'role' => 'design', 'status' => 'inactive']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.created', 'actor_id' => $admin->id]);
    }

    public function test_admin_can_manage_catalog_and_csv_exports_never_include_passwords(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->actingAs($admin)->post(route('admin.catalogs.request-types.store'), ['service' => 'design', 'key' => 'social', 'label' => 'Social'])->assertRedirect();
        $this->assertDatabaseHas('request_types', ['service' => 'design', 'key' => 'social']);

        $this->actingAs($admin)->post(route('password.confirm.store'), ['password' => 'password']);
        $response = $this->get(route('admin.exports.users'));
        $response->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringNotContainsString('password', strtolower($response->streamedContent()));
        $this->assertGreaterThan(0, AuditLog::where('action', 'export.users')->count());
    }

    public function test_admin_can_soft_delete_a_request_in_any_state(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $request = CreativeRequest::factory()->status(RequestStatus::COMPLETED)->create(['title' => 'Solicitud histórica']);

        $this->actingAs($admin)->post(route('password.confirm.store'), ['password' => 'password'])->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.requests.destroy', $request))->assertRedirect(route('admin.requests.index'));

        $this->assertSoftDeleted('creative_requests', ['id' => $request->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'request.deleted', 'actor_id' => $admin->id, 'auditable_id' => $request->id]);
    }
}
