@php
    $roleVal = old('role', $managedUser?->role?->value ?? (string)($managedUser?->role ?? 'marketing'));
    $statusVal = old('status', $managedUser?->status?->value ?? (string)($managedUser?->status ?? 'active'));
    $initials = $managedUser ? strtoupper(substr($managedUser->name, 0, 2)) : '👤';
@endphp

<div class="space-y-6">
    <!-- User Avatar & Profile Summary Banner -->
    <div class="flex items-center gap-4 rounded-xl border border-slate-800 bg-slate-950/70 p-4">
        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-red-600 to-red-800 text-lg font-black text-white uppercase shadow-lg border border-white/10">
            {{ $initials }}
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="text-base font-extrabold text-white leading-tight">
                {{ $managedUser?->name ?: 'Nuevo Perfil de Usuario' }}
            </h3>
            <p class="text-xs text-slate-400 font-mono mt-0.5">
                {{ $managedUser?->email ?: 'Asigna las credenciales y el nivel de acceso operativo.' }}
            </p>
        </div>
    </div>

    <!-- Form Inputs Grid -->
    <div class="grid gap-6 md:grid-cols-2">
        <!-- Full Name -->
        <div class="md:col-span-2">
            <label for="name" class="block text-xs font-extrabold uppercase tracking-wider text-slate-300">
                Nombre completo <span class="text-red-500">*</span>
            </label>
            <div class="relative mt-2">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">👤</span>
                <input id="name" name="name" type="text" required
                       value="{{ old('name', $managedUser?->name) }}"
                       placeholder="Ej. Gerardo López"
                       class="w-full rounded-xl border border-slate-800 bg-slate-950 pl-10 pr-4 py-3 text-sm font-semibold text-white placeholder-slate-500 transition focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500/50">
            </div>
        </div>

        <!-- Email Address -->
        <div class="md:col-span-2">
            <label for="email" class="block text-xs font-extrabold uppercase tracking-wider text-slate-300">
                Correo electrónico corporativo <span class="text-red-500">*</span>
            </label>
            <div class="relative mt-2">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">✉️</span>
                <input id="email" name="email" type="email" required
                       value="{{ old('email', $managedUser?->email) }}"
                       placeholder="usuario@totalground.com"
                       class="w-full rounded-xl border border-slate-800 bg-slate-950 pl-10 pr-4 py-3 text-sm font-mono font-semibold text-white placeholder-slate-500 transition focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500/50">
            </div>
        </div>

        <!-- Role Selector -->
        <div>
            <label for="role" class="block text-xs font-extrabold uppercase tracking-wider text-slate-300">
                Rol operativo <span class="text-red-500">*</span>
            </label>
            <select id="role" name="role" required class="mt-2 w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500/50">
                <option value="marketing" @selected($roleVal === 'marketing')>📢 Marketing (Solicitante)</option>
                <option value="design" @selected($roleVal === 'design')>🎨 Diseño Gráfico (Carolina)</option>
                <option value="render" @selected($roleVal === 'render')>📦 Render 3D (Jesús)</option>
                <option value="video" @selected($roleVal === 'video')>🎬 Video (Gerardo)</option>
                <option value="creative" @selected($roleVal === 'creative')>🎨 Operación Creativa General</option>
                <option value="supervisor" @selected($roleVal === 'supervisor')>⚡ Supervisor</option>
                <option value="admin" @selected($roleVal === 'admin')>👑 Administrador (Hugo)</option>
            </select>
            <p class="mt-1.5 text-[11px] text-slate-500">Determina el área de trabajo y las solicitudes que recibirá.</p>
        </div>

        <!-- Account Status Selector -->
        <div>
            <label for="status" class="block text-xs font-extrabold uppercase tracking-wider text-slate-300">
                Estado de la cuenta <span class="text-red-500">*</span>
            </label>
            <select id="status" name="status" required class="mt-2 w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500/50">
                <option value="active" @selected($statusVal === 'active')>🟢 Activo (Acceso habilitado)</option>
                <option value="inactive" @selected($statusVal === 'inactive')>⚪ Inactivo (Sin acceso temporal)</option>
                <option value="suspended" @selected($statusVal === 'suspended')>🔴 Suspendido</option>
            </select>
            <p class="mt-1.5 text-[11px] text-slate-500">Un usuario inactivo no podrá iniciar sesión en la plataforma.</p>
        </div>

        <!-- Department Selector -->
        <div class="md:col-span-2">
            <label for="department_id" class="block text-xs font-extrabold uppercase tracking-wider text-slate-300">
                Departamento asignado
            </label>
            <select id="department_id" name="department_id" class="mt-2 w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500/50">
                <option value="">🏢 Sin departamento asignado</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @selected(old('department_id', $managedUser?->department_id) == $department->id)>
                        🏢 {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
