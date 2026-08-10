<?php

namespace App\Console\Commands;

use App\Services\Storage\StorageAuditService;
use Illuminate\Console\Command;

class StorageAuditCommand extends Command
{
    protected $signature = 'storage:audit {--json}';

    protected $description = 'Audita el almacenamiento privado sin exponer contenidos.';

    public function handle(StorageAuditService $service): int
    {
        $result = $service->audit();
        $this->line($this->option('json') ? json_encode($result, JSON_PRETTY_PRINT) : json_encode($result));

        return $result['missing'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
