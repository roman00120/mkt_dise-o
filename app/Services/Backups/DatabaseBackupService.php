<?php

namespace App\Services\Backups;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class DatabaseBackupService
{
    public function backup(): string
    {
        $disk = Storage::disk(env('BACKUP_DISK', 'backups'));
        $name = 'database/'.now()->format('Y/m/d/His').'-'.bin2hex(random_bytes(4));
        $driver = config('database.default');
        if ($driver === 'sqlite') {
            $source = config('database.connections.sqlite.database');
            $relative = $name.'.sqlite';
            $disk->put($relative, File::get($source));

            return $relative;
        } if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new \RuntimeException('Driver no soportado para backup.');
        }
        $relative = $name.'.sql';
        $target = $disk->path($relative);
        File::ensureDirectoryExists(dirname($target));
        $connection = config('database.connections.'.$driver);
        $process = new Process(['mysqldump', '--single-transaction', '--routines', '--triggers', '--host='.$connection['host'], '--port='.$connection['port'], '--user='.$connection['username'], '--password='.$connection['password'], $connection['database']]);
        $process->run(function ($type, $buffer) use ($target): void {
            if ($type === Process::OUT) {
                file_put_contents($target, $buffer, FILE_APPEND);
            }
        });
        if (! $process->isSuccessful()) {
            throw new \RuntimeException('mysqldump no pudo generar el backup.');
        }

        return $relative;
    }
}
