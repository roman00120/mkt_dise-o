<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\UserAccessService;
use Illuminate\Http\RedirectResponse;

class UserAccessController extends Controller
{
    public function reset(User $user, UserAccessService $access): RedirectResponse
    {
        $this->authorize('manageStatus', $user);
        $access->sendReset($user, request()->user());

        return back()->with('status', 'Solicitud de restablecimiento enviada mediante Laravel.');
    }
}
