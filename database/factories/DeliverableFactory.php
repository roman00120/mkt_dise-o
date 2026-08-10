<?php

namespace Database\Factories;

use App\Enums\DeliverableStatus;
use App\Models\CreativeRequest;
use App\Models\Deliverable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DeliverableFactory extends Factory
{
    protected $model = Deliverable::class;

    public function definition(): array
    {
        return ['uuid' => (string) Str::uuid(), 'creative_request_id' => CreativeRequest::factory(), 'created_by' => User::factory(), 'status' => DeliverableStatus::DRAFT, 'title' => 'Entregable de prueba'];
    }
}
