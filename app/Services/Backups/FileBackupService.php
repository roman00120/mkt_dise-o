<?php

namespace App\Services\Backups;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileBackupService
{
    public function backup(): string
    {
        if (! class_exists(\PharData::class)) {
            throw new \RuntimeException('La extensión PharData es requerida.');
        }
        $disk = Storage::disk(env('BACKUP_DISK', 'backups'));
        $relative = 'files/'.now()->format('Y/m/d/His').'-'.bin2hex(random_bytes(4)).'.tar';
        $target = $disk->path($relative);
        File::ensureDirectoryExists(dirname($target));
        if (file_exists($target)) {
            File::delete($target);
        }
        $archive = new \PharData($target);
        $root = storage_path('app/private');
        if (is_dir($root)) {
            foreach (File::allFiles($root) as $file) {
                $archive->addFile($file->getPathname(), str_replace($root.'\\', '', $file->getPathname()));
            }
        }
        if ($archive->count() === 0) {
            $archive->addFromString('.backup-empty', 'No private files existed at backup time.');
        }

        return $relative;
    }
}
