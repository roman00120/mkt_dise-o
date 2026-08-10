<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentReplyRequest;
use App\Http\Requests\StoreRequestCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\CreativeRequest;
use App\Services\Comments\CommentAttachmentService;
use App\Services\Comments\CommentDeletionService;
use App\Services\Comments\CommentEditService;
use App\Services\Comments\CommentReplyService;
use App\Services\Comments\CommentService;
use Illuminate\Http\RedirectResponse;

class RequestCommentController extends Controller
{
    public function store(StoreRequestCommentRequest $request, CreativeRequest $creativeRequest, CommentService $comments): RedirectResponse
    {
        abort_unless($request->user()->id === $creativeRequest->requester_id, 403);
        $comment = $comments->create($creativeRequest, $request->user(), $request->validated('body'), 'public', $request->validated('mentions', []));
        $this->storeAttachments($comment, $request, $request->user());

        return back()->with('status', 'Comentario publicado.');
    }

    public function reply(StoreCommentReplyRequest $request, CreativeRequest $creativeRequest, Comment $comment, CommentReplyService $replies): RedirectResponse
    {
        abort_unless($comment->commentable_type === $creativeRequest->getMorphClass() && $comment->commentable_id === $creativeRequest->id, 404);
        $this->authorize('reply', $comment);
        $replies->create($comment, $request->user(), $request->validated('body'), $request->validated('mentions', []));

        return back()->with('status', 'Respuesta publicada.');
    }

    public function update(UpdateCommentRequest $request, CreativeRequest $creativeRequest, Comment $comment, CommentEditService $edits): RedirectResponse
    {
        abort_unless($comment->commentable_type === $creativeRequest->getMorphClass() && $comment->commentable_id === $creativeRequest->id, 404);
        $this->authorize('update', $comment);
        $edits->update($comment, $request->user(), $request->validated('body'), $request->validated('mentions', []));

        return back()->with('status', 'Comentario actualizado.');
    }

    public function destroy(CreativeRequest $creativeRequest, Comment $comment, CommentDeletionService $deletions): RedirectResponse
    {
        abort_unless($comment->commentable_type === $creativeRequest->getMorphClass() && $comment->commentable_id === $creativeRequest->id, 404);
        $this->authorize('delete', $comment);
        $deletions->delete($comment, request()->user());

        return back()->with('status', 'Comentario eliminado.');
    }

    private function storeAttachments(Comment $comment, StoreRequestCommentRequest $request, $user): void
    {
        foreach ($request->file('attachments', []) as $file) {
            app(CommentAttachmentService::class)->store($comment, $user, $file);
        }
    }
}
