<?php

namespace App\Services\Backups;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class RestoreService
{
    public function restore(string $relative, bool $dryRun = false, bool $confirm = false): array
    {
        if (app()->environment('production') && ! $confirm) {
            throw new \InvalidArgumentException('La restauración en producción requiere confirmación explícita.');
        }$disk = Storage::disk(env('BACKUP_DISK', 'backups'));
        if (! $disk->exists($relative)) {
            throw new \RuntimeException('Backup no encontrado.');
        }$driver = config('database.default');
        if ($driver !== 'sqlite') {
            return ['status' => 'manual_required', 'message' => 'Para MySQL restaura mediante el procedimiento documentado y el cliente mysql.'];
        }$target = config('database.connections.sqlite.database');
        if (! $dryRun) {
            File::copy($disk->path($relative), $target);
        }

        return ['status' => $dryRun ? 'dry_run' : 'restored', 'target' => 'sqlite'];
    }
}
