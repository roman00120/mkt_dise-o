<?php

namespace App\Services\Comments;

use App\Models\Comment;
use App\Models\CreativeRequest;
use App\Models\Deliverable;
use App\Models\DeliverableVersion;
use App\Models\User;

class CommentVisibilityService
{
    public function canView(User $user, Comment $comment): bool
    {
        if ($comment->isInternal() && $user->hasRole('marketing')) {
            return false;
        }
        $resource = $comment->commentable;
        $request = $resource instanceof CreativeRequest ? $resource : ($resource instanceof DeliverableVersion ? $resource->deliverable->request : $resource->request);

        return $user->can($user->hasRole('marketing') ? 'view' : 'viewCreative', $request);
    }

    public function canMention(User $user, User $mentioned, Comment $comment): bool
    {
        return $mentioned->isActive() && $this->canView($mentioned, $comment) && (! $comment->isInternal() || ! $mentioned->hasRole('marketing'));
    }

    public function allowedCommentable(object $resource): bool
    {
        return $resource instanceof CreativeRequest || $resource instanceof Deliverable || $resource instanceof DeliverableVersion;
    }
}
