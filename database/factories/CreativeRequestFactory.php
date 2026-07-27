<?php

namespace Database\Factories;

use App\Enums\CreativeService;
use App\Enums\RequestPriority;
use App\Enums\RequestStatus;
use App\Models\CreativeRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreativeRequestFactory extends Factory
{
    protected $model = CreativeRequest::class;

    public function definition(): array
    {
        return ['uuid' => fake()->uuid(), 'folio' => 'TG-'.now()->year.'-'.fake()->unique()->numerify('####'), 'requester_id' => User::factory(), 'service' => CreativeService::DESIGN, 'request_type' => 'digital', 'title' => 'Pieza de campaña', 'description' => 'Descripción de prueba', 'requested_priority' => RequestPriority::MEDIUM, 'status' => RequestStatus::DRAFT, 'current_step' => 3];
    }
}
