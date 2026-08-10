<?php

namespace Database\Factories;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationPreferenceFactory extends Factory
{
    protected $model = NotificationPreference::class;

    public function definition(): array
    {
        return ['user_id' => User::factory(), 'event_type' => fake()->unique()->randomElement(NotificationPreference::TYPES), 'in_app' => true, 'email' => false];
    }
}
