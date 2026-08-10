<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Audit\AuditLogService;
use App\Services\Auth\LoginRedirectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ChangePasswordController extends Controller
{
    public function create(): View
    {
        return view('auth.change-password');
    }

    public function update(Request $request, AuditLogService $audit, LoginRedirectService $redirects): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:12', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ], [
            'password.required' => 'Escribe una nueva contraseña.',
            'password.min' => 'La nueva contraseña debe tener al menos 12 caracteres.',
            'password.confirmed' => 'La confirmación debe coincidir exactamente con la nueva contraseña.',
        ]);

        $user = $request->user();
        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'password_changed_at' => now(),
            'must_change_password' => false,
        ])->save();

        DB::table('sessions')->where('user_id', $user->id)->delete();
        $request->session()->regenerate();
        $audit->record('user.password_changed', $user, $user, $user);

        return redirect()->to($redirects->pathFor($user))->with('status', 'Contraseña actualizada correctamente.');
    }
}
