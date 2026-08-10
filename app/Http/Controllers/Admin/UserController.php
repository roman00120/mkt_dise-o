<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserIndexRequest;
use App\Http\Requests\StoreAdminUserRequest;
use App\Http\Requests\UpdateAdminUserRequest;
use App\Models\Department;
use App\Models\User;
use App\Queries\AdminUserQuery;
use App\Services\Admin\UserAccessService;
use App\Services\Admin\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(AdminUserIndexRequest $request, AdminUserQuery $users): View
    {
        return view('admin.users.index', ['users' => $users->paginate($request->validated()), 'filters' => $request->validated(), 'departments' => Department::where('is_active', true)->orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('admin.users.create', ['departments' => Department::where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(StoreAdminUserRequest $request, UserManagementService $users, UserAccessService $access): RedirectResponse
    {
        $user = $users->create($request->validated(), $request->user());
        if ($user->isActive()) {
            $access->sendReset($user, $request->user());
        }

        return redirect()->route('admin.users.show', $user)->with('status', 'Usuario creado. Se generó un enlace de acceso.');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        return view('admin.users.show', ['managedUser' => $user->load('department')]);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', ['managedUser' => $user, 'departments' => Department::where('is_active', true)->orderBy('name')->get()]);
    }

    public function update(UpdateAdminUserRequest $request, User $user, UserManagementService $users): RedirectResponse
    {
        $this->authorize('update', $user);
        $users->update($user, $request->validated(), $request->user());

        return back()->with('status', 'Usuario actualizado.');
    }
}
