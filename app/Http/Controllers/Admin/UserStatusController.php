<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStatusActionRequest;
use App\Models\User;
use App\Services\Admin\UserStatusService;
use Illuminate\Http\RedirectResponse;

class UserStatusController extends Controller
{
    public function activate(User $user, UserStatusService $service): RedirectResponse
    {
        $this->authorize('manageStatus', $user);
        $service->activate($user, request()->user());

        return back()->with('status', 'Usuario activado.');
    }

    public function deactivate(UserStatusActionRequest $request, User $user, UserStatusService $service): RedirectResponse
    {
        $this->authorize('manageStatus', $user);
        $service->deactivate($user, $request->user(), $request->validated('reason'));

        return back()->with('status', 'Usuario desactivado.');
    }

    public function suspend(UserStatusActionRequest $request, User $user, UserStatusService $service): RedirectResponse
    {
        $this->authorize('manageStatus', $user);
        $service->suspend($user, $request->user(), $request->validated('reason'));

        return back()->with('status', 'Usuario suspendido.');
    }

    public function restore(User $user, UserStatusService $service): RedirectResponse
    {
        $this->authorize('manageStatus', $user);
        $service->restore($user, request()->user());

        return back()->with('status', 'Acceso restaurado.');
    }
}
