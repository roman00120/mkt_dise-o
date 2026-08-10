<?php

namespace Database\Factories;

use App\Enums\DeliverableVersionStatus;
use App\Models\Deliverable;
use App\Models\DeliverableVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DeliverableVersionFactory extends Factory
{
    protected $model = DeliverableVersion::class;

    public function definition(): array
    {
        return ['uuid' => (string) Str::uuid(), 'deliverable_id' => Deliverable::factory(), 'version_number' => 1, 'created_by' => User::factory(), 'status' => DeliverableVersionStatus::DRAFT, 'notes' => 'Notas de entrega'];
    }
}
