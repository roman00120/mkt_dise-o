<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use App\Services\Admin\DepartmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        return view('admin.departments.index', ['departments' => Department::withCount('users')->orderBy('name')->paginate(25)]);
    }

    public function create(): View
    {
        return view('admin.departments.create');
    }

    public function store(StoreDepartmentRequest $request, DepartmentService $service): RedirectResponse
    {
        $department = $service->create($request->validated(), $request->user());

        return redirect()->route('admin.departments.index')->with('status', 'Departamento creado.');
    }

    public function edit(Department $department): View
    {
        $this->authorize('update', $department);

        return view('admin.departments.edit', compact('department'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department, DepartmentService $service): RedirectResponse
    {
        $this->authorize('update', $department);
        $service->update($department, $request->validated(), $request->user());

        return back()->with('status', 'Departamento actualizado.');
    }

    public function activate(Department $department, DepartmentService $service): RedirectResponse
    {
        $this->authorize('update', $department);
        $service->toggle($department, request()->user(), true);

        return back();
    }

    public function deactivate(Department $department, DepartmentService $service): RedirectResponse
    {
        $this->authorize('update', $department);
        abort_if($department->users()->exists(), 422, 'No puedes desactivar un departamento con usuarios asociados.');
        $service->toggle($department, request()->user(), false);

        return back();
    }
}
