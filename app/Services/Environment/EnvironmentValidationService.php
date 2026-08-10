<?php

namespace App\Services\Environment;

use Illuminate\Support\Facades\DB;

class EnvironmentValidationService
{
    public function validate(bool $strict = false, bool $noDb = false, bool $noMail = false): array
    {
        $checks = [];
        $add = static function (array &$checks, string $name, string $status, string $message): void {
            $checks[] = compact('name', 'status', 'message');
        };
        $env = (string) env('APP_ENV', 'local');
        $debug = filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOL);
        $url = (string) env('APP_URL');
        $add($checks, 'APP_KEY', config('app.key') ? 'ok' : 'error', config('app.key') ? 'Definida' : 'No está definida.');
        $add($checks, 'APP_ENV', in_array($env, ['local', 'testing', 'staging', 'production'], true) ? 'ok' : 'error', $env);
        $add($checks, 'APP_DEBUG', $env === 'production' && $debug ? 'error' : ($debug ? 'warning' : 'ok'), $debug ? 'Activado' : 'Desactivado');
        $https = filter_var($url, FILTER_VALIDATE_URL) && (str_starts_with($url, 'https://') || $env !== 'production');
        $add($checks, 'APP_URL', $https ? 'ok' : 'error', filter_var($url, FILTER_VALIDATE_URL) ? $url : 'URL inválida');
        $add($checks, 'APP_TIMEZONE', in_array((string) config('app.timezone'), timezone_identifiers_list(), true) ? 'ok' : 'error', (string) config('app.timezone'));
        $driver = (string) config('database.default');
        $add($checks, 'DB_CONNECTION', ! in_array($driver, ['sqlite', 'mysql', 'mariadb'], true) ? 'error' : ($env === 'production' && $driver === 'sqlite' ? 'error' : 'ok'), $driver);
        if (! $noDb) {
            try {
                DB::connection()->getPdo();
                $add($checks, 'database', 'ok', 'Conexión disponible.');
            } catch (\Throwable $e) {
                $add($checks, 'database', 'error', 'No se pudo conectar.');
            }
        }
        $privateDisk = env('PRIVATE_FILESYSTEM_DISK', 'private');
        $add($checks, 'PRIVATE_FILESYSTEM_DISK', $env === 'production' && $privateDisk === 'public' ? 'error' : 'ok', $privateDisk);
        $private = config('filesystems.disks.'.$privateDisk.'.root');
        $writable = $private && is_dir($private) ? is_writable($private) : (bool) @mkdir((string) $private, 0770, true);
        $add($checks, 'private_storage', $writable ? 'ok' : 'error', $writable ? 'Escritura disponible.' : 'Disco privado no escribible.');
        $secureCookie = filter_var(env('SESSION_SECURE_COOKIE', false), FILTER_VALIDATE_BOOL);
        $add($checks, 'SESSION_SECURE_COOKIE', $env === 'production' && ! $secureCookie ? 'error' : ($secureCookie ? 'ok' : 'warning'), $secureCookie ? 'Activada' : 'Desactivada');
        $add($checks, 'mail', $noMail ? 'warning' : (config('mail.default') ? 'ok' : 'warning'), $noMail ? 'Omitido.' : (string) config('mail.default'));
        $backupRoot = config('filesystems.disks.'.env('BACKUP_DISK', 'backups').'.root');
        $add($checks, 'backup_storage', $backupRoot && (is_dir($backupRoot) || @mkdir($backupRoot, 0770, true)) ? 'ok' : 'error', $backupRoot ? 'Configurado.' : 'No configurado.');
        foreach (['pdo', 'json', 'mbstring', 'openssl', 'fileinfo'] as $extension) {
            $add($checks, 'php_'.$extension, extension_loaded($extension) ? 'ok' : 'error', extension_loaded($extension) ? 'Disponible' : 'Faltante');
        }

        return ['checks' => $checks, 'ok' => ! collect($checks)->contains('status', 'error'), 'strict_failed' => $strict && collect($checks)->contains('status', 'error')];
    }
}
