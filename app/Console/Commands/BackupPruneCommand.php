<?php

namespace App\Console\Commands;

use App\Services\Backups\BackupRetentionService;
use Illuminate\Console\Command;

class BackupPruneCommand extends Command
{
    protected $signature = 'app:backup-prune {--dry-run}';

    protected $description = 'Elimina backups fuera de retención; soporta dry-run.';

    public function handle(BackupRetentionService $service): int
    {
        $files = $service->prune((bool) $this->option('dry-run'));
        $this->info(count($files).' archivos '.($this->option('dry-run') ? 'se eliminarían.' : 'eliminados.'));

        return self::SUCCESS;
    }
}
