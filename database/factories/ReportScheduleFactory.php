<?php

namespace Database\Factories;

use App\Models\ReportSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportScheduleFactory extends Factory
{
    protected $model = ReportSchedule::class;

    public function definition(): array
    {
        return ['report' => 'executive', 'frequency' => 'weekly', 'filters' => ['period' => '7'], 'format' => 'csv', 'is_active' => true, 'next_run_at' => now()->addWeek()];
    }
}
