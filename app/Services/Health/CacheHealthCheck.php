<?php

namespace App\Services\Health;

use Illuminate\Support\Facades\Cache;

class CacheHealthCheck
{
    public function check(): array
    {
        try {
            Cache::put('health-cache', true, 5);

            return ['status' => Cache::get('health-cache') === true ? 'ok' : 'error'];
        } catch (\Throwable $e) {
            return ['status' => 'error'];
        }
    }
}
