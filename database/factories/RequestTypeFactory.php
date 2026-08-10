<?php

namespace Database\Factories;

use App\Models\RequestType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RequestTypeFactory extends Factory
{
    protected $model = RequestType::class;

    public function definition(): array
    {
        return ['uuid' => (string) Str::uuid(), 'service' => fake()->randomElement(['design', 'video', 'render']), 'key' => fake()->unique()->slug(2), 'label' => fake()->sentence(2), 'description' => fake()->sentence(), 'is_active' => true, 'sort_order' => 0];
    }
}
