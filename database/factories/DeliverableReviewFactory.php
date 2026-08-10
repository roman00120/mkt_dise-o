<?php

namespace Database\Factories;

use App\Models\DeliverableReview;
use App\Models\DeliverableVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DeliverableReviewFactory extends Factory
{
    protected $model = DeliverableReview::class;

    public function definition(): array
    {
        return ['uuid' => (string) Str::uuid(), 'deliverable_version_id' => DeliverableVersion::factory(), 'reviewer_id' => User::factory(), 'review_type' => 'internal', 'decision' => 'approved', 'comments' => 'Revisado'];
    }
}
