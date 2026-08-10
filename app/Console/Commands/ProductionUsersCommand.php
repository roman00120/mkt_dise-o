<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Department;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Database\Seeders\ProductionSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ProductionUsersCommand extends Command
{
    protected $signature = 'production:users {--update : Actualiza los usuarios objetivo existentes}';

    protected $description = 'Configura los usuarios iniciales autorizados de TG Creative Hub.';

    public function handle(AuditLogService $audit): int
    {
        if (! in_array((string) config('app.env'), ['production', 'staging'], true)) {
            $this->error('Este comando únicamente puede ejecutarse en production o staging.');

            return self::FAILURE;
        }

        app(ProductionSeeder::class)->run();
        $adminName = trim((string) (env('ADMIN_NAME') ?: $this->ask('Nombre del administrador')));
        $adminEmail = strtolower(trim((string) (env('ADMIN_EMAIL') ?: $this->ask('Correo del administrador'))));
        if ($adminName === '' || ! filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('El nombre y correo del administrador son obligatorios y válidos.');

            return self::INVALID;
        }

        $definitions = [
            ['name' => 'Ana Carolina Román', 'email' => 'ana.roman@totalground.com', 'role' => UserRole::CREATIVE, 'department' => 'design'],
            ['name' => 'Ángel Castañeda', 'email' => 'angel.castaneda@totalground.com', 'role' => UserRole::MARKETING, 'department' => 'marketing'],
            ['name' => 'Arlem Cruz', 'email' => 'arlem.cruz@totalground.com', 'role' => UserRole::MARKETING, 'department' => 'marketing'],
            ['name' => 'Gerardo', 'email' => 'gerardo.lopez@totalground.com', 'role' => UserRole::CREATIVE, 'department' => 'design'],
            ['name' => 'Jesús', 'email' => 'jesus.cabrera@totalground.com', 'role' => UserRole::CREATIVE, 'department' => 'design'],
            ['name' => $adminName, 'email' => $adminEmail, 'role' => UserRole::ADMIN, 'department' => 'administration'],
        ];

        $this->table(['Nombre', 'Correo', 'Rol', 'Departamento'], array_map(fn (array $user): array => [$user['name'], $user['email'], $user['role']->value, $user['department']], $definitions));
        if (! $this->confirm('¿Continuar con esta configuración?', false)) {
            $this->info('Operación cancelada.');

            return self::SUCCESS;
        }

        foreach ($definitions as $definition) {
            $existing = User::where('email', $definition['email'])->first();
            if ($existing && ! $this->option('update')) {
                $this->error("Ya existe {$definition['email']}. Usa --update para actualizar sin duplicar.");

                return self::FAILURE;
            }
            if ($existing && ! $this->confirm("¿Actualizar {$definition['email']}?", false)) {
                $this->error('La actualización requiere confirmación explícita.');

                return self::FAILURE;
            }
        }

        DB::transaction(function () use ($definitions, $audit): void {
            foreach ($definitions as $definition) {
                $department = Department::where('code', $definition['department'])->where('is_active', true)->firstOrFail();
                $password = $this->askForPassword($definition['email']);
                $existing = User::where('email', $definition['email'])->first();
                $user = User::updateOrCreate(['email' => $definition['email']], [
                    'uuid' => $existing?->uuid ?: (string) str()->uuid(),
                    'name' => $definition['name'],
                    'password' => Hash::make($password),
                    'role' => $definition['role'],
                    'status' => UserStatus::ACTIVE,
                    'department_id' => $department->id,
                    'password_changed_at' => null,
                    'must_change_password' => true,
                    'suspended_at' => null,
                    'deactivated_at' => null,
                ]);
                $audit->record($existing ? 'user.updated' : 'user.created', null, $user, $user, ['role' => $definition['role']->value, 'department' => $definition['department'], 'status' => 'active']);
            }
        });

        $this->info('Usuarios iniciales configurados. Las contraseñas no se han mostrado ni registrado.');

        return self::SUCCESS;
    }

    private function askForPassword(string $email): string
    {
        while (true) {
            $password = $this->secret("Contraseña para {$email}");
            $confirmation = $this->secret('Confirma la contraseña');
            $validation = Validator::make(['password' => $password, 'password_confirmation' => $confirmation], [
                'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
            ]);
            if (! $validation->fails()) {
                return $password;
            }

            $this->error($validation->errors()->first('password'));
        }
    }
}
