<?php

namespace Tests\Feature\Production;

use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\CreativeRequest;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Database\Seeders\DevelopmentSeeder;
use Database\Seeders\ProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionDataAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_seeder_only_creates_operational_catalogs(): void
    {
        $this->seed(ProductionSeeder::class);

        $this->assertDatabaseCount('creative_requests', 0);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseHas('request_types', ['is_active' => true]);
        $this->assertDatabaseCount('departments', 3);
        $this->assertDatabaseHas('departments', ['code' => 'administration', 'name' => 'Administración']);
        $this->assertDatabaseHas('departments', ['code' => 'marketing', 'name' => 'Marketing']);
        $this->assertDatabaseHas('departments', ['code' => 'design', 'name' => 'Diseño']);
    }

    public function test_demo_seeder_is_blocked_in_production(): void
    {
        config()->set('app.env', 'production');

        try {
            $this->expectException(\LogicException::class);
            (new DemoSeeder)->run();
        } finally {
            config()->set('app.env', 'testing');
        }
    }

    public function test_development_seeder_is_blocked_in_production(): void
    {
        config()->set('app.env', 'production');

        try {
            $this->expectException(\RuntimeException::class);
            (new DevelopmentSeeder)->run();
        } finally {
            config()->set('app.env', 'testing');
        }
    }

    public function test_marketing_dashboard_cannot_show_another_users_request(): void
    {
        $owner = User::factory()->create(['role' => UserRole::MARKETING]);
        $other = User::factory()->create(['role' => UserRole::MARKETING]);
        CreativeRequest::factory()->status(RequestStatus::PENDING)->create([
            'requester_id' => $other->id,
            'title' => 'Solicitud privada de otro usuario',
        ]);

        $this->actingAs($owner)
            ->get(route('app.dashboard'))
            ->assertOk()
            ->assertDontSee('Solicitud privada de otro usuario');
    }

    public function test_primary_views_have_no_mojibake_markers(): void
    {
        foreach ([
            resource_path('views/app/dashboard.blade.php'),
            resource_path('views/requests/wizard.blade.php'),
        ] as $path) {
            $this->assertStringNotContainsString('Ã', file_get_contents($path));
            $this->assertStringNotContainsString('Â', file_get_contents($path));
        }
    }
}
