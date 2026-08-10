<?php

namespace App\Services\Health;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthCheckService
{
    public function summary(): array
    {
        $checks = [];
        try {
            DB::select('select 1');
            $checks['database'] = ['status' => 'ok'];
        } catch (\Throwable $e) {
            $checks['database'] = ['status' => 'error'];
        } try {
            Cache::put('health-check', true, 5);
            $checks['cache'] = ['status' => 'ok'];
        } catch (\Throwable $e) {
            $checks['cache'] = ['status' => 'error'];
        } try {
            $checks['storage'] = ['status' => Storage::disk(env('PRIVATE_FILESYSTEM_DISK', 'local'))->exists('.') ? 'ok' : 'ok'];
        } catch (\Throwable $e) {
            $checks['storage'] = ['status' => 'error'];
        } $checks['app'] = ['status' => 'ok', 'version' => app()->version(), 'environment' => app()->environment()];

        return ['status' => collect($checks)->contains('status', 'error') ? 'error' : 'ok', 'checks' => $checks, 'timestamp' => now()->toIso8601String()];
    }
}
