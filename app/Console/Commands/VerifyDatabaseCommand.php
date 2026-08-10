<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VerifyDatabaseCommand extends Command
{
    protected $signature = 'app:verify-database {--json} {--strict} {--skip-data-quality}';

    protected $description = 'Verifica conexión, migraciones y tablas críticas sin modificar datos.';

    public function handle(): int
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable) {
            $this->error('No se pudo conectar a la base de datos.');

            return self::FAILURE;
        }
        $required = ['users', 'creative_requests', 'creative_request_events', 'jobs', 'failed_jobs', 'request_status_periods', 'sla_policies'];
        $missing = collect($required)->filter(fn ($table) => ! Schema::hasTable($table))->values()->all();
        $ran = DB::table('migrations')->pluck('migration')->all();
        $files = array_keys(app('migrator')->getMigrationFiles(database_path('migrations')));
        $pending = array_values(array_diff($files, $ran));
        $result = ['driver' => DB::connection()->getDriverName(), 'missing_tables' => $missing, 'pending_migrations' => $pending];
        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT));
        } else {
            $this->table(['Clave', 'Valor'], collect($result)->map(fn ($value, $key) => [$key, is_array($value) ? implode(',', $value) : $value])->all());
        }

        return $missing !== [] || $pending !== [] ? self::FAILURE : self::SUCCESS;
    }
}
