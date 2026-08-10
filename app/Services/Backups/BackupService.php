<?php

namespace App\Services\Backups;

use App\Models\User;
use App\Services\Audit\AuditLogService;

class BackupService
{
    public function __construct(private DatabaseBackupService $database, private FileBackupService $files, private BackupManifestService $manifest) {}

    public function all(?User $actor = null): array
    {
        $result = [];
        foreach ([['database', $this->database], ['files', $this->files]] as [$type,$service]) {
            $path = $service->backup();
            $result[$type] = $path;
            $this->manifest->write($path, ['type' => $type]);
        }if ($actor) {
            app(AuditLogService::class)->record('backup.created', $actor, null, null, ['types' => array_keys($result)]);
        }

return $result;
    }
}
