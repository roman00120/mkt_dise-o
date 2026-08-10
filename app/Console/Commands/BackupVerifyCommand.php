<?php

namespace App\Console\Commands;

use App\Services\Backups\BackupVerificationService;
use Illuminate\Console\Command;

class BackupVerifyCommand extends Command
{
    protected $signature = 'app:backup-verify {--latest}';

    protected $description = 'Verifica checksums de backups privados.';

    public function handle(BackupVerificationService $service): int
    {
        $result = $service->verify();
        $this->line(json_encode($result, JSON_PRETTY_PRINT));

        return collect($result)->contains(fn ($row) => ! $row['valid']) ? self::FAILURE : self::SUCCESS;
    }
}
