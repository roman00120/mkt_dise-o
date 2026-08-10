<?php

namespace Database\Factories;

use App\Models\SavedReportView;
use Illuminate\Database\Eloquent\Factories\Factory;

class SavedReportViewFactory extends Factory
{
    protected $model = SavedReportView::class;

    public function definition(): array
    {
        return ['name' => $this->faker->unique()->words(2, true), 'report' => 'executive', 'filters' => ['period' => '30'], 'is_default' => false];
    }
}
