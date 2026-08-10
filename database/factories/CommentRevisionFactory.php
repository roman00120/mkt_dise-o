<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\CommentRevision;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentRevisionFactory extends Factory
{
    protected $model = CommentRevision::class;

    public function definition(): array
    {
        return ['comment_id' => Comment::factory(), 'edited_by' => User::factory(), 'previous_body' => fake()->sentence()];
    }
}
