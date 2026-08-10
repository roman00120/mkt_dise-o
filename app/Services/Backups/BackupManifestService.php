<?php

namespace App\Services\Backups;

use Illuminate\Support\Facades\Storage;

class BackupManifestService
{
    public function write(string $relativePath, array $data): string
    {
        $disk = Storage::disk(env('BACKUP_DISK', 'backups'));
        $manifest = $relativePath.'.manifest.json';
        $disk->put($manifest, json_encode($data + ['created_at' => now()->toIso8601String(), 'sha256' => hash_file('sha256', $disk->path($relativePath))], JSON_PRETTY_PRINT));

        return $manifest;
    }
}
