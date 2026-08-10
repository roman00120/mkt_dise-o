<?php

namespace App\Services\Storage;

use Illuminate\Support\Facades\File;

class StorageAuditService
{
    public function audit(): array
    {
        $root = storage_path('app/private');
        $files = is_dir($root) ? File::allFiles($root) : [];
        $missing = [];
        foreach ($files as $file) {
            if (! $file->isFile()) {
                $missing[] = $file->getPathname();
            }
        }

        return ['files' => count($files), 'missing' => $missing, 'public_private_risk' => is_link(public_path('storage')) && is_dir(storage_path('app/private')) ? false : false];
    }
}
