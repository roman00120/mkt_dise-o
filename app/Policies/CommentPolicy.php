<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\CreativeRequest;
use App\Models\User;
use App\Services\Comments\CommentVisibilityService;

class CommentPolicy
{
    public function view(User $user, Comment $comment): bool
    {
        return app(CommentVisibilityService::class)->canView($user, $comment);
    }

    public function createPublic(User $user, CreativeRequest $request): bool
    {
        return $this->resourceAccess($user, $request);
    }

    public function createInternal(User $user, CreativeRequest $request): bool
    {
        return ! $user->hasRole('marketing') && $user->can('viewCreative', $request);
    }

    public function reply(User $user, Comment $comment): bool
    {
        return ! $comment->trashed() && $this->view($user, $comment);
    }

    public function update(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id && ! $comment->trashed() && $comment->created_at?->gt(now()->subMinutes(15));
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id && ! $comment->trashed();
    }

    private function resourceAccess(User $user, CreativeRequest $request): bool
    {
        return $user->hasRole('marketing') ? $user->can('view', $request) : $user->can('viewCreative', $request);
    }
}
