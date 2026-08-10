<?php

namespace App\Services\Notifications;

use App\Models\Comment;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationRecipientResolver
{
    public function forComment(Comment $comment): Collection
    {
        $request = $this->requestFor($comment);
        $users = collect();

        if ($comment->visibility === 'public') {
            $users = $users->merge([$request->requester, $request->assignee]);
            if ($request->assignee) {
                $users = $users->merge(User::query()->where('role', 'supervisor')->where('status', 'active')->get());
            }
        } else {
            $users = $users->merge([$request->assignee]);
            $users = $users->merge(User::query()->where('role', 'supervisor')->where('status', 'active')->get());
        }

        $users = $users->merge($comment->mentions->load('user')->pluck('user'));

        return $users->filter(fn (?User $user) => $user?->isActive())
            ->unique('id')
            ->reject(fn (User $user) => $user->id === $comment->user_id)
            ->values();
    }

    private function requestFor(Comment $comment): CreativeRequest
    {
        $resource = $comment->commentable;
        if ($resource instanceof CreativeRequest) {
            return $resource;
        }

        return $resource->request;
    }
}
