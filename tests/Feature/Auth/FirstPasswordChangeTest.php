<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FirstPasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_first_access_flag_is_forced_to_change_password(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::MARKETING,
            'password' => Hash::make('Temporary!2026'),
            'must_change_password' => true,
        ]);

        $this->actingAs($user)->get(route('app.dashboard'))
            ->assertRedirect(route('password.change'));
        $this->actingAs($user)->get(route('password.change'))->assertOk()->assertSee('Primer acceso');

        $this->actingAs($user)->post(route('password.change.update'), [
            'password' => 'Secure!NewPassword2026',
            'password_confirmation' => 'Secure!NewPassword2026',
        ])->assertRedirect(route('app.dashboard'));

        $fresh = $user->fresh();
        $this->assertFalse($fresh->must_change_password);
        $this->assertTrue(Hash::check('Secure!NewPassword2026', $fresh->password));
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.password_changed', 'target_user_id' => $user->id]);
    }
}
