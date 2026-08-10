<?php

namespace Tests\Feature\Production;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present_without_exposing_secrets(): void
    {
        $response = $this->get('/');
        $response->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Security-Policy');
        $this->assertStringNotContainsString((string) config('app.key'), $response->getContent());
    }

    public function test_detailed_health_requires_an_active_authenticated_user(): void
    {
        $this->get(route('health.details'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
            ->get(route('health.details'))->assertOk()->assertJsonPath('status', 'ok');
    }

    public function test_environment_command_does_not_print_app_key(): void
    {
        $result = $this->artisan('app:validate-environment', ['--json' => true, '--no-db' => true, '--no-mail' => true]);
        $result->assertExitCode(0);
        $result->expectsOutputToContain('APP_KEY');
        $result->doesntExpectOutputToContain((string) config('app.key'));
    }
}
