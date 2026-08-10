<?php

namespace Database\Factories;

use App\Models\RequestStatusPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestStatusPeriodFactory extends Factory
{
    protected $model = RequestStatusPeriod::class;

    public function definition(): array
    {
        $start = now()->subHours(3);

        return ['status' => 'in_progress', 'started_at' => $start, 'ended_at' => now(), 'duration_seconds' => 10800];
    }
}
