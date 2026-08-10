<?php

namespace Database\Factories;

use App\Models\SystemSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SystemSettingFactory extends Factory
{
    protected $model = SystemSetting::class;

    public function definition(): array
    {
        return ['key' => 'test.'.fake()->unique()->slug(), 'value' => '1', 'type' => 'integer', 'group' => 'testing', 'is_sensitive' => false];
    }
}
