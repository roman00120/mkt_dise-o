<?php

namespace App\Console\Commands;

use App\Services\Backups\BackupService;
use App\Services\Backups\BackupVerificationService;
use Illuminate\Console\Command;

class BackupCommand extends Command
{
    protected $signature = 'app:backup {--all} {--verify}';

    protected $description = 'Genera backups privados de base de datos y archivos.';

    public function handle(BackupService $backup, BackupVerificationService $verify): int
    {
        try {
            $paths = $backup->all();
            $this->table(['Tipo', 'Archivo'], collect($paths)->map(fn ($v, $k) => [$k, $v])->all());
            if ($this->option('verify')) {
                $this->line(json_encode($verify->verify(), JSON_PRETTY_PRINT));
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('No se pudo generar el backup: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
