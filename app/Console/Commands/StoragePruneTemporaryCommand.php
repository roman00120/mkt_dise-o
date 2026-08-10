<?php

namespace App\Console\Commands;

use App\Services\Storage\TemporaryFilePruner;
use Illuminate\Console\Command;

class StoragePruneTemporaryCommand extends Command
{
    protected $signature = 'storage:prune-temporary {--dry-run}';

    protected $description = 'Limpia archivos temporales fuera de retención.';

    public function handle(TemporaryFilePruner $pruner): int
    {
        $files = $pruner->prune((bool) $this->option('dry-run'));
        $this->info(count($files).' temporales '.($this->option('dry-run') ? 'detectados.' : 'eliminados.'));

        return self::SUCCESS;
    }
}
