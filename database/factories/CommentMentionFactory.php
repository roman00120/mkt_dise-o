<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\CommentMention;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentMentionFactory extends Factory
{
    protected $model = CommentMention::class;

    public function definition(): array
    {
        return ['comment_id' => Comment::factory(), 'mentioned_user_id' => User::factory()];
    }
}
