<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PruneCommand extends Command
{
    protected $signature = 'app:prune {--dry-run}';

    protected $description = 'Ejecuta las limpiezas técnicas de retención.';

    public function handle(): int
    {
        $this->call('app:backup-prune', ['--dry-run' => (bool) $this->option('dry-run')]);
        $this->call('storage:prune-temporary', ['--dry-run' => (bool) $this->option('dry-run')]);

        return self::SUCCESS;
    }
}
