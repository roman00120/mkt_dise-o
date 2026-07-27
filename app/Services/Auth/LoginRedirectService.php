<?php

namespace App\Services\Auth;

use App\Enums\UserRole;
use App\Models\User;

class LoginRedirectService
{
    public function pathFor(User $user): string
    {
        return match ($user->role) {
            UserRole::ADMIN => route('admin.dashboard'),
            UserRole::MARKETING => route('app.dashboard'),
            UserRole::DESIGN => route('creative.design.dashboard'),
            UserRole::VIDEO => route('creative.video.dashboard'),
            UserRole::RENDER => route('creative.render.dashboard'),
            UserRole::SUPERVISOR => route('creative.dashboard'),
        };
    }
}
