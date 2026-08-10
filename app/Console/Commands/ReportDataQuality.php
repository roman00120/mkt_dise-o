<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Analytics\DataQualityService;
use Illuminate\Console\Command;

class ReportDataQuality extends Command
{
    protected $signature = 'reports:data-quality {--json}';

    protected $description = 'Revisa la calidad de datos analíticos sin modificar registros.';

    public function handle(DataQualityService $quality): int
    {
        $admin = User::where('role', 'admin')->first();
        if (! $admin) {
            $this->error('No existe un administrador.');

            return self::FAILURE;
        } $result = $quality->summary($admin, ['from_date' => now()->startOfYear(), 'to_date' => now()->endOfDay()]);
        $this->option('json') ? $this->line(json_encode($result, JSON_PRETTY_PRINT)) : $this->table(['Métrica', 'Valor'], collect($result)->map(fn ($v, $k) => [$k, $v])->values()->all());

        return self::SUCCESS;
    }
}
