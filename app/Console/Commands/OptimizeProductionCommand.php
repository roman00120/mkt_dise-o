<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OptimizeProductionCommand extends Command
{
    protected $signature = 'app:optimize-production';

    protected $description = 'Prepara caches de configuración, rutas y vistas.';

    public function handle(): int
    {
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');

        return self::SUCCESS;
    }
}
