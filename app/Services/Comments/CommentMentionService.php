<?php

namespace App\Services\Comments;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CommentMentionService
{
    public function __construct(private CommentVisibilityService $visibility) {}

    public function sync(Comment $comment, array $ids): Collection
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (count($ids) > 10) {
            throw ValidationException::withMessages(['mentions' => 'Puedes mencionar hasta 10 usuarios.']);
        }
        $users = User::query()->whereIn('id', $ids)->get();
        if ($users->count() !== count($ids)) {
            throw ValidationException::withMessages(['mentions' => 'Una mención no es válida.']);
        }
        foreach ($users as $user) {
            if (! $this->visibility->canMention($comment->author, $user, $comment)) {
                throw ValidationException::withMessages(['mentions' => 'No puedes mencionar a ese usuario en esta conversación.']);
            }
            $comment->mentions()->firstOrCreate(['mentioned_user_id' => $user->id]);
        }

        return $users;
    }
}
