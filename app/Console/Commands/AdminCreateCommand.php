<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Department;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AdminCreateCommand extends Command
{
    protected $signature = 'admin:create {email?} {name?}';

    protected $description = 'Crea un administrador mediante un flujo interactivo seguro.';

    public function handle(AuditLogService $audit): int
    {
        $name = trim((string) ($this->argument('name') ?: env('ADMIN_NAME') ?: $this->ask('Nombre')));
        $email = strtolower(trim((string) ($this->argument('email') ?: env('ADMIN_EMAIL') ?: $this->ask('Correo electrónico'))));

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('El nombre y un correo electrónico válido son obligatorios.');

            return self::INVALID;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('Ya existe un usuario con ese correo electrónico.');

            return self::INVALID;
        }

        $password = $this->secret('Contraseña (mínimo 12 caracteres)');
        $confirmation = $this->secret('Confirma la contraseña');
        $validation = Validator::make(['password' => $password, 'password_confirmation' => $confirmation], [
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);
        if ($validation->fails()) {
            $this->error($validation->errors()->first('password'));

            return self::INVALID;
        }

        $department = Department::firstOrCreate(['code' => 'administration'], [
            'uuid' => (string) str()->uuid(),
            'name' => 'Administración',
            'is_active' => true,
        ]);
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
            'department_id' => $department->id,
            'password_changed_at' => now(),
            'must_change_password' => true,
        ]);
        $audit->record('user.created', null, $user, $user, ['role' => 'admin', 'status' => 'active']);

        $this->info('Administrador creado correctamente. La contraseña no se ha registrado ni mostrado.');

        return self::SUCCESS;
    }
}
