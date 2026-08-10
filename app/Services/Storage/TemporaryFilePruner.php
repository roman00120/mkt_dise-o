<?php

namespace App\Services\Storage;

use Illuminate\Support\Facades\File;

class TemporaryFilePruner
{
    public function prune(bool $dryRun = false): array
    {
        $root = storage_path('app/private/tmp');
        $cutoff = now()->subHours((int) env('TEMP_FILE_RETENTION_HOURS', 24))->getTimestamp();
        $removed = [];
        if (! is_dir($root)) {
            return $removed;
        }foreach (File::allFiles($root) as $file) {
            if ($file->getMTime() < $cutoff) {
                $removed[] = $file->getPathname();
                if (! $dryRun) {
                    File::delete($file->getPathname());
                }
            }
        }

        return $removed;
    }
}
