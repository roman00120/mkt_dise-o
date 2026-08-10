<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInternalNoteRequest;
use App\Models\Deliverable;
use App\Services\Comments\CommentAttachmentService;
use App\Services\Comments\CommentService;
use Illuminate\Http\RedirectResponse;

class DeliverableInternalNoteController extends Controller
{
    public function store(StoreInternalNoteRequest $request, Deliverable $deliverable, CommentService $comments): RedirectResponse
    {
        $this->authorize('view', $deliverable);
        abort_unless(! $request->user()->hasRole('marketing'), 403);
        $comment = $comments->create($deliverable, $request->user(), $request->validated('body'), 'internal', $request->validated('mentions', []));
        foreach ($request->file('attachments', []) as $file) {
            app(CommentAttachmentService::class)->store($comment, $request->user(), $file);
        }

        return back()->with('status', 'Nota interna guardada. No será visible para Marketing.');
    }
}
