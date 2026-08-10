<?php

namespace App\Console\Commands;

use App\Models\SlaPolicy;
use Illuminate\Console\Command;

class SlaCheckCommand extends Command
{
    protected $signature = 'sla:check';

    protected $description = 'Revisa disponibilidad de políticas SLA sin enviar alertas.';

    public function handle(): int
    {
        $this->info(SlaPolicy::where('is_active', true)->count().' políticas SLA activas.');

        return self::SUCCESS;
    }
}
