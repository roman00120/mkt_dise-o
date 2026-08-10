<?php

namespace Database\Factories;

use App\Models\BusinessCalendarHoliday;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessCalendarHolidayFactory extends Factory
{
    protected $model = BusinessCalendarHoliday::class;

    public function definition(): array
    {
        return ['date' => $this->faker->unique()->dateTimeBetween('now', '+1 year'), 'name' => $this->faker->sentence(3), 'is_active' => true];
    }
}
