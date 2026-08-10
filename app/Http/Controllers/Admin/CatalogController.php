<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequestTypeRequest;
use App\Http\Requests\UpdateRequestTypeRequest;
use App\Models\CreativeRequest;
use App\Models\RequestType;
use App\Services\Admin\RequestTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        return view('admin.catalogs.index', ['types' => RequestType::orderBy('service')->orderBy('sort_order')->paginate(30)]);
    }

    public function store(StoreRequestTypeRequest $request, RequestTypeService $service): RedirectResponse
    {
        $service->create($request->validated(), $request->user());

        return back()->with('status', 'Tipo de solicitud creado.');
    }

    public function update(UpdateRequestTypeRequest $request, RequestType $requestType, RequestTypeService $service): RedirectResponse
    {
        $this->authorize('update', $requestType);
        $service->update($requestType, $request->validated(), $request->user());

        return back()->with('status', 'Tipo actualizado.');
    }

    public function activate(RequestType $requestType, RequestTypeService $service): RedirectResponse
    {
        $this->authorize('update', $requestType);
        $service->toggle($requestType, request()->user(), true);

        return back();
    }

    public function deactivate(RequestType $requestType, RequestTypeService $service): RedirectResponse
    {
        $this->authorize('update', $requestType);
        abort_if(CreativeRequest::where('request_type', $requestType->key)->where('service', $requestType->service)->exists(), 422, 'No puedes desactivar un tipo usado por solicitudes.');
        $service->toggle($requestType, request()->user(), false);

        return back();
    }
}
