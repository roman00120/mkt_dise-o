<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreativeRequestIndexRequest;
use App\Models\CreativeRequest;
use App\Models\User;
use App\Queries\CreativeRequestQuery;
use App\Services\Requests\RequestOperationalService;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function index(CreativeRequestIndexRequest $request, CreativeRequestQuery $query): View
    {
        return view('creative.requests.index', ['requests' => $query->paginate($request->user(), $request->validated()), 'filters' => $request->validated()]);
    }

    public function show(CreativeRequest $creativeRequest, RequestOperationalService $operations): View
    {
        $this->authorize('viewCreative', $creativeRequest);
        $creativeRequest->load(['requester', 'assignee', 'detail', 'files.uploader', 'events.actor', 'informationRequests.requester', 'informationRequests.responder', 'comments' => fn ($query) => $query->whereNull('parent_id')->withTrashed()->with(['author', 'mentions.user', 'attachments', 'replies.author', 'replies.attachments'])->latest()]);
        $members = User::query()->where('status', 'active')->whereIn('role', [$creativeRequest->service->value, 'creative'])->orderBy('name')->get();

        return view('creative.requests.show', compact('creativeRequest', 'members') + ['operationalHealth' => $operations->health($creativeRequest)]);
    }
}
