<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentAttachment;
use App\Models\CreativeRequest;
use Illuminate\Support\Facades\Storage;

class CommentAttachmentDownloadController extends Controller
{
    public function __invoke(CreativeRequest $creativeRequest, Comment $comment, CommentAttachment $attachment)
    {
        abort_unless($comment->commentable_type === $creativeRequest->getMorphClass() && $comment->commentable_id === $creativeRequest->id && $attachment->comment_id === $comment->id, 404);
        $this->authorize('download', $attachment);
        abort_unless(Storage::disk($attachment->disk)->exists($attachment->path), 404);

        return Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name, ['Content-Type' => $attachment->mime_type]);
    }
}
