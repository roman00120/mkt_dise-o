<?php

namespace App\Services\Health;

use Illuminate\Support\Facades\DB;

class DatabaseHealthCheck
{
    public function check(): array
    {
        try {
            DB::select('select 1');

            return ['status' => 'ok', 'driver' => DB::connection()->getDriverName()];
        } catch (\Throwable $e) {
            return ['status' => 'error'];
        }
    }
}
