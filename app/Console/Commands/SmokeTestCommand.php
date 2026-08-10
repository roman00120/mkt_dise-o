<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SmokeTestCommand extends Command
{
    protected $signature = 'app:smoke-test';

    protected $description = 'Ejecuta comprobaciones mínimas de aplicación y base de datos.';

    public function handle(): int
    {
        try {
            DB::select('select 1');
            $this->info('Database: OK');
            $this->info('Application: OK');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Smoke test failed.');

            return self::FAILURE;
        }
    }
}
