<?php

namespace Tests\Feature\App;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Database\Seeders\ProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProductionSeeder::class);
    }

    public function test_marketing_dashboard_renders_database_backed_empty_summary(): void
    {
        $user = User::factory()->create(['name' => 'Andrea Martínez', 'role' => UserRole::MARKETING]);

        $this->actingAs($user)->get(route('app.dashboard'))
            ->assertOk()
            ->assertSee('Andrea Martínez')
            ->assertSee('Nueva solicitud')
            ->assertSee('Solicitudes activas')
            ->assertSee('Diseño Gráfico')
            ->assertSee('No tienes solicitudes que requieran atención.')
            ->assertSee('Solicitudes recientes')
            ->assertSee('Pendientes de revisión')
            ->assertSee('Actividad reciente');
    }

    public function test_dashboard_allows_admin_and_supervisor_but_rejects_creative_roles(): void
    {
        foreach ([UserRole::ADMIN, UserRole::SUPERVISOR] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('app.dashboard'))
                ->assertOk();
        }

        foreach ([UserRole::DESIGN, UserRole::VIDEO, UserRole::RENDER] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('app.dashboard'))
                ->assertForbidden();
        }
    }

    public function test_inactive_and_suspended_users_cannot_access_dashboard(): void
    {
        foreach ([UserStatus::INACTIVE, UserStatus::SUSPENDED] as $status) {
            $this->actingAs(User::factory()->create(['status' => $status]))
                ->get(route('app.dashboard'))
                ->assertRedirect(route('login'));
        }
    }

    public function test_dashboard_ignores_removed_demo_query_states_and_keeps_empty_state(): void
    {
        $user = User::factory()->create(['role' => UserRole::MARKETING]);

        foreach ([['demo' => 'empty'], ['demo' => 'loading'], ['demo' => 'error'], ['filter' => 'review']] as $query) {
            $this->actingAs($user)->get(route('app.dashboard', $query))
                ->assertOk()
                ->assertSee('No tienes solicitudes que requieran atención.');
        }
    }

    public function test_marketing_placeholder_routes_are_protected(): void
    {
        $user = User::factory()->create();
        $routes = ['app.requests.create', 'app.requests.index', 'app.profile', 'app.notifications'];

        foreach ($routes as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get(route($route))->assertOk();
            $response->assertSee($route === 'app.requests.create' ? 'Crea una nueva solicitud' : 'Módulo en construcción');
        }
    }
}
