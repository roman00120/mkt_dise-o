<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInternalNoteRequest;
use App\Models\CreativeRequest;
use App\Services\Comments\CommentAttachmentService;
use App\Services\Comments\CommentService;
use Illuminate\Http\RedirectResponse;

class InternalNoteController extends Controller
{
    public function store(StoreInternalNoteRequest $request, CreativeRequest $creativeRequest, CommentService $comments): RedirectResponse
    {
        abort_unless(! $request->user()->hasRole('marketing') && ($request->user()->hasRole('supervisor') || $creativeRequest->assignee_id === $request->user()->id || $request->user()->role?->value === $creativeRequest->service?->value), 403);
        $comment = $comments->create($creativeRequest, $request->user(), $request->validated('body'), 'internal', $request->validated('mentions', []));
        foreach ($request->file('attachments', []) as $file) {
            app(CommentAttachmentService::class)->store($comment, $request->user(), $file);
        }

        return back()->with('status', 'Nota interna guardada. No será visible para Marketing.');
    }
}
