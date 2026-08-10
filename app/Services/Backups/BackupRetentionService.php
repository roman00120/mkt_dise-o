<?php

namespace App\Services\Backups;

use Illuminate\Support\Facades\Storage;

class BackupRetentionService
{
    public function prune(bool $dryRun = false): array
    {
        $disk = Storage::disk(env('BACKUP_DISK', 'backups'));
        $cutoff = now()->subDays((int) env('BACKUP_RETENTION_DAYS', 14));
        $removed = [];
        foreach ($disk->allFiles() as $file) {
            if (! str_ends_with($file, '.manifest.json') && optional($disk->lastModified($file) ? now()->setTimestamp($disk->lastModified($file)) : null)->lessThan($cutoff)) {
                $removed[] = $file;
                if (! $dryRun) {
                    $disk->delete([$file, $file.'.manifest.json']);
                }
            }
        }

return $removed;
    }
}
