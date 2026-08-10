<?php

namespace Database\Factories;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return ['uuid' => (string) Str::uuid(), 'action' => 'user.updated', 'metadata' => ['source' => 'test'], 'ip_address' => '127.0.0.1'];
    }
}
