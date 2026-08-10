<?php

namespace App\Console\Commands;

use App\Services\Environment\EnvironmentValidationService;
use Illuminate\Console\Command;

class ValidateEnvironmentCommand extends Command
{
    protected $signature = 'app:validate-environment {--strict} {--json} {--no-db} {--no-mail}';

    protected $description = 'Valida configuración y requisitos del entorno sin mostrar secretos.';

    public function handle(EnvironmentValidationService $validator): int
    {
        $result = $validator->validate((bool) $this->option('strict'), (bool) $this->option('no-db'), (bool) $this->option('no-mail'));
        if ($this->option('json')) {
            $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->table(['Comprobación', 'Estado', 'Resultado'], collect($result['checks'])->map(fn ($row) => [$row['name'], strtoupper($row['status']), $row['message']])->all());
        }

        return $result['strict_failed'] ? self::FAILURE : self::SUCCESS;
    }
}
