<?php

namespace Database\Factories;

use App\Models\CorrectionRequest;
use App\Models\Deliverable;
use App\Models\DeliverableVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CorrectionRequestFactory extends Factory
{
    protected $model = CorrectionRequest::class;

    public function definition(): array
    {
        return ['uuid' => (string) Str::uuid(), 'creative_request_id' => fn () => Deliverable::factory()->create()->creative_request_id, 'deliverable_id' => Deliverable::factory(), 'deliverable_version_id' => DeliverableVersion::factory(), 'requested_by' => User::factory(), 'type' => 'marketing', 'status' => 'open', 'summary' => 'Ajustar contenido', 'details' => 'Detalle de corrección'];
    }
}
