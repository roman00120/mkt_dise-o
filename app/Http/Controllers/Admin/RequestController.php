<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequestIndexRequest;
use App\Models\CreativeRequest;
use App\Queries\AdminRequestQuery;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function index(AdminRequestIndexRequest $request, AdminRequestQuery $query): View
    {
        return view('admin.requests.index', ['requests' => $query->paginate($request->validated()), 'filters' => $request->validated()]);
    }

    public function show(CreativeRequest $creativeRequest): View
    {
        return view('admin.requests.show', ['requestModel' => $creativeRequest->load(['requester', 'assignee', 'events.actor', 'deliverables'])]);
    }
}
