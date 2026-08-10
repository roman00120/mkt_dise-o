<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return ['uuid' => (string) Str::uuid(), 'commentable_type' => (new CreativeRequest)->getMorphClass(), 'commentable_id' => CreativeRequest::factory(), 'user_id' => User::factory(), 'visibility' => 'public', 'body' => fake()->sentence()];
    }

    public function internal(): static
    {
        return $this->state(fn () => ['visibility' => 'internal']);
    }
}
