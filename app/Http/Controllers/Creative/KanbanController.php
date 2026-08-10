<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreativeRequestIndexRequest;
use App\Models\CreativeRequest;
use App\Queries\CreativeRequestQuery;
use Illuminate\View\View;

class KanbanController extends Controller
{
    public function __invoke(CreativeRequestIndexRequest $request, CreativeRequestQuery $query): View
    {
        $this->authorize('viewCreativePanel', CreativeRequest::class);

        return view('creative.requests.kanban', ['columns' => $query->kanban($request->user(), $request->validated()), 'filters' => $request->validated()]);
    }
}
