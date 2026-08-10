<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequestCommentRequest;
use App\Models\Deliverable;
use App\Services\Comments\CommentAttachmentService;
use App\Services\Comments\CommentService;
use Illuminate\Http\RedirectResponse;

class DeliverableCommentController extends Controller
{
    public function store(StoreRequestCommentRequest $request, Deliverable $deliverable, CommentService $comments): RedirectResponse
    {
        $this->authorize('view', $deliverable);
        $comment = $comments->create($deliverable, $request->user(), $request->validated('body'), 'public', $request->validated('mentions', []));
        foreach ($request->file('attachments', []) as $file) {
            app(CommentAttachmentService::class)->store($comment, $request->user(), $file);
        }

        return back()->with('status', 'Comentario publicado.');
    }
}
