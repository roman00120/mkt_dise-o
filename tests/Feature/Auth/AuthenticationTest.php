<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_marketing_to_marketing_area_and_updates_last_access(): void
    {
        $user = User::factory()->create(['role' => UserRole::MARKETING]);

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('app.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
        $this->assertSame('127.0.0.1', $user->fresh()->last_login_ip);
    }

    public function test_each_role_has_its_own_redirect(): void
    {
        foreach ([
            UserRole::ADMIN->value => 'admin.dashboard',
            UserRole::DESIGN->value => 'creative.design.dashboard',
            UserRole::VIDEO->value => 'creative.video.dashboard',
            UserRole::RENDER->value => 'creative.render.dashboard',
            UserRole::SUPERVISOR->value => 'creative.dashboard',
        ] as $role => $route) {
            $user = User::factory()->create(['role' => $role]);

            $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
                ->assertRedirect(route($route));

            $this->post(route('logout'));
        }
    }

    public function test_invalid_inactive_or_suspended_users_receive_safe_message(): void
    {
        foreach ([UserStatus::INACTIVE, UserStatus::SUSPENDED] as $status) {
            $user = User::factory()->create(['status' => $status]);

            $this->from(route('login'))->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
                ->assertRedirect(route('login'))
                ->assertSessionHasErrors('email');
        }

        $this->from(route('login'))->post(route('login.store'), ['email' => 'missing@totalground.local', 'password' => 'password'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }

    public function test_login_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 6; $attempt++) {
            $response = $this->post(route('login.store'), ['email' => 'rate@totalground.local', 'password' => 'wrong']);
        }

        $response->assertStatus(429);
    }

    public function test_logout_requires_post_and_invalidates_session(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['marker' => true]);

        $this->get('/logout')->assertStatus(405);
        $this->post(route('logout'))->assertRedirect(route('login'))->assertSessionHas('status');
        $this->assertGuest();
    }

    public function test_guest_is_redirected_and_roles_cannot_cross_access(): void
    {
        $this->get(route('app.dashboard'))->assertRedirect(route('login'));

        $user = User::factory()->create(['role' => UserRole::MARKETING]);
        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('app.dashboard'))->assertOk()->assertSee('Dashboard de Marketing');
    }

    public function test_password_recovery_is_generic_for_unknown_email(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['email' => 'unknown@totalground.local'])
            ->assertRedirect()
            ->assertSessionHas('status', 'Si existe una cuenta asociada a ese correo, recibirás un enlace de recuperación.');

        Notification::assertNothingSent();
    }

    public function test_confirm_password_works_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('password.confirm.store'), ['password' => 'password'])
            ->assertRedirect('/');
    }

    public function test_public_registration_route_does_not_exist(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register')->assertNotFound();
    }
}
