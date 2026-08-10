<?php

namespace App\Services\Health;

use Illuminate\Support\Facades\Storage;

class StorageHealthCheck
{
    public function check(): array
    {
        try {
            $disk = Storage::disk(env('PRIVATE_FILESYSTEM_DISK', 'local'));
            $path = '.health-'.bin2hex(random_bytes(4));
            $disk->put($path, 'ok');
            $disk->delete($path);

            return ['status' => 'ok'];
        } catch (\Throwable $e) {
            return ['status' => 'error'];
        }
    }
}
