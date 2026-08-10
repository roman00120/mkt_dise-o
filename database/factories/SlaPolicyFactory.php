<?php

namespace Database\Factories;

use App\Models\SlaPolicy;
use Illuminate\Database\Eloquent\Factories\Factory;

class SlaPolicyFactory extends Factory
{
    protected $model = SlaPolicy::class;

    public function definition(): array
    {
        return ['name' => $this->faker->sentence(3), 'metric' => 'initial_response', 'target_minutes' => 480, 'warning_threshold_percent' => 80, 'business_hours_only' => true, 'is_active' => true, 'effective_from' => today()];
    }
}
