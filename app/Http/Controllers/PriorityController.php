<?php

namespace App\Http\Controllers;

use App\Models\CreativeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PriorityController extends Controller
{
    public function index(Request $request): View
    {
        return view('priorities.index', ['requests' => $this->orderedRequests(), 'canManage' => $this->canManage($request)]);
    }

    public function move(Request $request, CreativeRequest $creativeRequest): RedirectResponse
    {
        abort_unless($this->canManage($request), 403);
        $direction = $request->validate(['direction' => ['required', 'in:up,down']])['direction'];
        DB::transaction(function () use ($creativeRequest, $direction, $request): void {
            $items = $this->orderedRequests(true)->values();
            $current = $items->search(fn (CreativeRequest $item): bool => $item->id === $creativeRequest->id);
            abort_if($current === false, 404);
            $target = $direction === 'up' ? $current - 1 : $current + 1;
            if ($target < 0 || $target >= $items->count()) {
                return;
            }
            $currentItem = $items->get($current);
            $items->put($current, $items->get($target));
            $items->put($target, $currentItem);
            foreach ($items as $position => $item) {
                $item->forceFill(['priority_order' => $position + 1])->saveQuietly();
            }
            $creativeRequest->events()->create(['actor_id' => $request->user()->id, 'event' => 'priority_reordered', 'metadata' => ['position' => $target + 1, 'direction' => $direction]]);
        });

        return back()->with('status', 'El orden de prioridades fue actualizado.');
    }

    private function orderedRequests(bool $lock = false)
    {
        $query = CreativeRequest::query()->with(['requester', 'assignee'])->whereNotIn('status', ['draft', 'completed', 'cancelled', 'rejected'])->orderByRaw('priority_order IS NULL')->orderBy('priority_order')->orderBy('created_at');

        return ($lock ? $query->lockForUpdate() : $query)->get();
    }

    private function canManage(Request $request): bool
    {
        return $request->user()->hasRole('admin', 'supervisor') || $request->user()->email === 'ana.roman@totalground.com';
    }
}
