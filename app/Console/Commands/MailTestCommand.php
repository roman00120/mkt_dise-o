<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MailTestCommand extends Command
{
    protected $signature = 'mail:test {address}';

    protected $description = 'Valida configuración de correo sin enviar mensajes reales.';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('No se permite correo de prueba en producción.');

            return self::FAILURE;
        }$this->info('Mailer configurado: '.config('mail.default').'. Envío no ejecutado; usa sandbox controlado.');

        return self::SUCCESS;
    }
}
