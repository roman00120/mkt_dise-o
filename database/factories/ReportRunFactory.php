<?php

namespace Database\Factories;

use App\Models\ReportRun;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportRunFactory extends Factory
{
    protected $model = ReportRun::class;

    public function definition(): array
    {
        return ['status' => 'pending', 'format' => 'csv'];
    }
}
