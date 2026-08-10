<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return ['uuid' => (string) Str::uuid(), 'name' => $name, 'code' => Str::upper(Str::random(6)), 'description' => fake()->sentence(), 'is_active' => true];
    }
}
