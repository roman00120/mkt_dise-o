<?php

namespace App\Console\Commands;

use App\Services\Backups\RestoreService;
use Illuminate\Console\Command;

class RestoreCommand extends Command
{
    protected $signature = 'app:restore {backup} {--latest} {--dry-run} {--confirm}';

    protected $description = 'Restaura un backup; dry-run no modifica datos.';

    public function handle(RestoreService $service): int
    {
        try {
            $this->line(json_encode($service->restore($this->argument('backup'), (bool) $this->option('dry-run'), (bool) $this->option('confirm')), JSON_PRETTY_PRINT));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
