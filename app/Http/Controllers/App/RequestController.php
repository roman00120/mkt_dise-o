<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexCreativeRequestRequest;
use App\Models\CreativeRequest;
use App\Services\Requests\MarketingRequestQuery;
use App\Services\Requests\RecommendedDateService;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function index(IndexCreativeRequestRequest $request, MarketingRequestQuery $query): View
    {
        $filters = $request->validated();
        $requests = $query->paginate($request->user(), $filters);

        return view('requests.index', ['requests' => $requests, 'metrics' => $query->metrics($request->user()), 'filters' => $filters]);
    }

    public function show(CreativeRequest $creativeRequest, RecommendedDateService $dates): View
    {
        $this->authorize('view', $creativeRequest);
        $creativeRequest->load(['requester', 'detail', 'files.uploader', 'events.actor', 'duplicatedFrom', 'comments' => fn ($query) => $query->where('visibility', 'public')->whereNull('parent_id')->withTrashed()->with(['author', 'mentions.user', 'attachments', 'replies.author', 'replies.attachments'])->latest()]);

        return view('requests.show', ['request' => $creativeRequest, 'dateHealth' => $this->dateHealth($creativeRequest, $dates)]);
    }

    private function dateHealth(CreativeRequest $request, RecommendedDateService $dates): string
    {
        if (! $request->required_date) {
            return 'Sin fecha';
        }
        if (in_array($request->status?->value, ['approved', 'completed'], true)) {
            return 'Finalizada';
        }
        if ($request->required_date->isBefore(today())) {
            return 'Vencida';
        }

        return today()->diffInDays($request->required_date) <= 3 ? 'Próxima a vencer' : 'En tiempo';
    }
}
