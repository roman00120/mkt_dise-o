<?php

namespace App\Console\Commands;

use App\Services\Analytics\StatusPeriodService;
use Illuminate\Console\Command;

class RebuildStatusPeriods extends Command
{
    protected $signature = 'reports:rebuild-status-periods {--dry-run} {--request=}';

    protected $description = 'Reconstruye periodos de estado a partir de eventos sin modificar solicitudes.';

    public function handle(StatusPeriodService $service): int
    {
        $result = $service->rebuild($this->option('request') ? (int) $this->option('request') : null, (bool) $this->option('dry-run'));
        $this->table(['Métrica', 'Valor'], [['periodos', $result['periods']], ['inconsistencias', count($result['inconsistencies'])]]);

        return self::SUCCESS;
    }
}
