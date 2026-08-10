<?php

namespace App\Services\Backups;

use Illuminate\Support\Facades\Storage;

class BackupVerificationService
{
    public function verify(?string $relative = null): array
    {
        $disk = Storage::disk(env('BACKUP_DISK', 'backups'));
        $files = $relative ? [$relative] : collect($disk->allFiles())->filter(fn ($f) => str_ends_with($f, '.manifest.json'))->map(fn ($f) => substr($f, 0, -14))->all();
        $results = [];
        foreach ($files as $file) {
            $manifest = $file.'.manifest.json';
            $expected = data_get(json_decode($disk->get($manifest), true), 'sha256');
            $actual = $disk->exists($file) ? hash_file(strlen((string) $expected) === 32 ? 'md5' : 'sha256', $disk->path($file)) : null;
            $results[$file] = ['valid' => $actual !== null && hash_equals((string) $expected, $actual)];
        }

        return $results;
    }
}
