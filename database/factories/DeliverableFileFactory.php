<?php

namespace Database\Factories;

use App\Models\DeliverableFile;
use App\Models\DeliverableVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DeliverableFileFactory extends Factory
{
    protected $model = DeliverableFile::class;

    public function definition(): array
    {
        return ['uuid' => (string) Str::uuid(), 'deliverable_version_id' => DeliverableVersion::factory(), 'uploaded_by' => User::factory(), 'original_name' => 'preview.pdf', 'stored_name' => 'preview.pdf', 'disk' => 'local', 'path' => 'deliverables/demo/preview.pdf', 'mime_type' => 'application/pdf', 'extension' => 'pdf', 'size' => 100, 'checksum' => hash('sha256', 'demo'), 'category' => 'preview', 'is_primary' => true];
    }
}
