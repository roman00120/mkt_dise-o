<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequestCommentRequest;
use App\Models\CreativeRequest;
use App\Models\Deliverable;
use App\Services\Comments\CommentAttachmentService;
use App\Services\Comments\CommentService;
use Illuminate\Http\RedirectResponse;

class DeliverableCommentController extends Controller
{
    public function store(StoreRequestCommentRequest $request, CreativeRequest $creativeRequest, Deliverable $deliverable, CommentService $comments): RedirectResponse
    {
        abort_unless($deliverable->creative_request_id === $creativeRequest->id, 404);
        abort_unless($request->user()->id === $creativeRequest->requester_id, 403);
        $comment = $comments->create($deliverable, $request->user(), $request->validated('body'), 'public', $request->validated('mentions', []));
        foreach ($request->file('attachments', []) as $file) {
            app(CommentAttachmentService::class)->store($comment, $request->user(), $file);
        }

        return back()->with('status', 'Comentario publicado.');
    }
}
