<?php

namespace App\Services\Health;

use Illuminate\Support\Facades\DB;

class QueueHealthCheck
{
    public function check(): array
    {
        try {
            return ['status' => DB::getSchemaBuilder()->hasTable('jobs') ? 'ok' : 'warning'];
        } catch (\Throwable $e) {
            return ['status' => 'error'];
        }
    }
}
