<?php

namespace App\Services\Health;

use Illuminate\Support\Facades\Storage;

class BackupHealthCheck
{
    public function check(): array
    {
        try {
            $files = Storage::disk(env('BACKUP_DISK', 'backups'))->files();

            return ['status' => $files === [] ? 'warning' : 'ok', 'count' => count($files)];
        } catch (\Throwable $e) {
            return ['status' => 'error'];
        }
    }
}
