@extends('layouts.admin')
@section('title', 'Gestión de Usuarios')
@section('header', 'Gestión de Usuarios')

@section('content')
<div class="space-y-6">
    <!-- Header Hero Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-800 bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-red-500/20 border border-red-500/40 px-2.5 py-1 text-xs font-bold text-red-300 uppercase tracking-wider">
                    👥 Control de Usuarios
                </span>
                <span class="text-xs text-slate-400">TG Creative Hub</span>
            </div>
            <h1 class="mt-2 text-2xl sm:text-3xl font-black text-white tracking-tight">
                Directorio de Usuarios y Roles
            </h1>
            <p class="mt-1 text-sm text-slate-400 max-w-xl">
                Administra los perfiles de acceso, asignación de departamentos y permisos del personal activo.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.exports.users') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-800/90 hover:bg-slate-700 px-4 py-2.5 text-xs font-bold text-white transition shadow">
                <span>📥</span>
                <span>Exportar CSV</span>
            </a>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 px-4 py-2.5 text-xs font-extrabold text-white transition shadow-lg hover:shadow-red-900/30">
                <span>➕</span>
                <span>Nuevo Usuario</span>
            </a>
        </div>
    </div>

    <!-- Quick Metrics Summary Bar -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-800 bg-slate-900/80 p-4 shadow">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase">
                <span>Total Cuentas</span>
                <span class="text-emerald-400">🟢 Registrados</span>
            </div>
            <p class="mt-2 text-3xl font-black text-white leading-none">{{ $users->total() }}</p>
            <p class="mt-1 text-[11px] text-slate-400">Usuarios en la plataforma</p>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-900/80 p-4 shadow">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase">
                <span>Administración</span>
                <span class="text-red-400">👑 Hugo / Admin</span>
            </div>
            <p class="mt-2 text-3xl font-black text-white leading-none">
                {{ $users->filter(fn($u) => ($u->role?->value ?? $u->role) === 'admin')->count() }}
            </p>
            <p class="mt-1 text-[11px] text-slate-400">Acceso total al sistema</p>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-900/80 p-4 shadow">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase">
                <span>Equipo Creativo</span>
                <span class="text-indigo-400">🎨 Producción</span>
            </div>
            <p class="mt-2 text-3xl font-black text-white leading-none">
                {{ $users->filter(fn($u) => in_array(($u->role?->value ?? $u->role), ['creative', 'design', 'video', 'render', 'supervisor']))->count() }}
            </p>
            <p class="mt-1 text-[11px] text-slate-400">Diseño, Video y Render</p>
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-900/80 p-4 shadow">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase">
                <span>Marketing</span>
                <span class="text-blue-400">📢 Solicitantes</span>
            </div>
            <p class="mt-2 text-3xl font-black text-white leading-none">
                {{ $users->filter(fn($u) => ($u->role?->value ?? $u->role) === 'marketing')->count() }}
            </p>
            <p class="mt-1 text-[11px] text-slate-400">Ingreso de peticiones</p>
        </div>
    </div>

    <!-- Filter Control Card -->
    <form method="GET" class="rounded-xl border border-slate-800 bg-slate-900/90 p-4 shadow-lg">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <div class="relative">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="🔍 Buscar por nombre o email..." class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:border-red-500 focus:outline-none">
            </div>

            <div>
                <select name="role" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3.5 py-2 text-xs font-semibold text-white focus:border-red-500 focus:outline-none">
                    <option value="">Todos los Roles</option>
                    <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>👑 Administrador</option>
                    <option value="supervisor" @selected(($filters['role'] ?? '') === 'supervisor')>⚡ Supervisor</option>
                    <option value="marketing" @selected(($filters['role'] ?? '') === 'marketing')>📢 Marketing</option>
                    <option value="creative" @selected(($filters['role'] ?? '') === 'creative')>🎨 Operación Creativa</option>
                    <option value="design" @selected(($filters['role'] ?? '') === 'design')>🎨 Diseño Gráfico</option>
                    <option value="video" @selected(($filters['role'] ?? '') === 'video')>🎬 Video</option>
                    <option value="render" @selected(($filters['role'] ?? '') === 'render')>📦 Render 3D</option>
                </select>
            </div>

            <div>
                <select name="status" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3.5 py-2 text-xs font-semibold text-white focus:border-red-500 focus:outline-none">
                    <option value="">Todos los Estados</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>🟢 Activo</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>⚪ Inactivo</option>
                    <option value="suspended" @selected(($filters['status'] ?? '') === 'suspended')>🔴 Suspendido</option>
                </select>
            </div>

            <div>
                <select name="department_id" class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3.5 py-2 text-xs font-semibold text-white focus:border-red-500 focus:outline-none">
                    <option value="">Todos los Departamentos</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? '') == $department->id)>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-full rounded-lg bg-red-600 hover:bg-red-500 px-4 py-2 text-xs font-extrabold text-white transition shadow">
                    Filtrar
                </button>
                @if(request()->anyFilled(['q', 'role', 'status', 'department_id']))
                    <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 px-3 py-2 text-xs font-bold text-slate-300 transition flex items-center justify-center">
                        ✕
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Main Users Table Card -->
    <div class="rounded-xl border border-slate-800 bg-slate-900/90 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/60 text-xs font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Usuario</th>
                        <th class="py-3.5 px-4">Rol & Permisos</th>
                        <th class="py-3.5 px-4">Departamento</th>
                        <th class="py-3.5 px-4 text-center">Estado de Cuenta</th>
                        <th class="py-3.5 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/70">
                    @forelse($users as $user)
                        @php
                            $roleVal = $user->role?->value ?? (string)$user->role;
                            $statusVal = $user->status?->value ?? (string)$user->status;
                            $initials = strtoupper(substr($user->name, 0, 2));

                            $avatarGradient = match($roleVal) {
                                'admin' => 'from-red-600 to-red-800',
                                'supervisor' => 'from-amber-600 to-amber-800',
                                'marketing' => 'from-blue-600 to-blue-800',
                                'design', 'creative' => 'from-indigo-600 to-purple-800',
                                'video' => 'from-pink-600 to-rose-800',
                                'render' => 'from-emerald-600 to-teal-800',
                                default => 'from-slate-600 to-slate-800'
                            };

                            $roleBadge = match($roleVal) {
                                'admin' => ['label' => '👑 Administrador', 'style' => 'bg-red-500/20 text-red-300 border-red-500/40'],
                                'supervisor' => ['label' => '⚡ Supervisor', 'style' => 'bg-amber-500/20 text-amber-300 border-amber-500/40'],
                                'marketing' => ['label' => '📢 Marketing', 'style' => 'bg-blue-500/20 text-blue-300 border-blue-500/40'],
                                'creative' => ['label' => '🎨 Operación Creativa', 'style' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40'],
                                'design' => ['label' => '🎨 Diseño Gráfico', 'style' => 'bg-purple-500/20 text-purple-300 border-purple-500/40'],
                                'video' => ['label' => '🎬 Video', 'style' => 'bg-pink-500/20 text-pink-300 border-pink-500/40'],
                                'render' => ['label' => '📦 Render 3D', 'style' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40'],
                                default => ['label' => ucfirst($roleVal), 'style' => 'bg-slate-800 text-slate-300 border-slate-700']
                            };
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition">
                            <!-- User Column -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br {{ $avatarGradient }} text-xs font-black text-white uppercase shadow-md border border-white/10">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.users.show', $user) }}" class="font-extrabold text-white hover:text-red-400 transition leading-tight block">
                                            {{ $user->name }}
                                        </a>
                                        <span class="text-xs text-slate-400 block font-mono mt-0.5">
                                            {{ $user->email }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Column -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 rounded-md border px-2.5 py-1 text-xs font-bold {{ $roleBadge['style'] }}">
                                    {{ $roleBadge['label'] }}
                                </span>
                            </td>

                            <!-- Department Column -->
                            <td class="py-3.5 px-4 text-xs font-medium text-slate-300">
                                @if($user->department)
                                    <span class="inline-flex items-center gap-1 text-slate-200 font-semibold">
                                        <span>🏢</span>
                                        <span>{{ $user->department->name }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-500">—</span>
                                @endif
                            </td>

                            <!-- Status Column -->
                            <td class="py-3.5 px-4 text-center">
                                @if($statusVal === 'active')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 px-3 py-0.5 text-xs font-bold text-emerald-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <span>Activo</span>
                                    </span>
                                @elseif($statusVal === 'suspended')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-500/15 border border-red-500/30 px-3 py-0.5 text-xs font-bold text-red-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>
                                        <span>Suspendido</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-800 border border-slate-700 px-3 py-0.5 text-xs font-bold text-slate-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span>
                                        <span>Inactivo</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Actions Column -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}" class="rounded-lg border border-slate-700 bg-slate-800/90 hover:bg-slate-700 px-2.5 py-1.5 text-xs font-bold text-slate-200 transition" title="Ver Detalle">
                                        👁️ Ver
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg border border-red-500/30 bg-red-500/10 hover:bg-red-500/20 px-2.5 py-1.5 text-xs font-bold text-red-300 transition" title="Editar Usuario">
                                        ✏️ Editar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-xs text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="text-3xl">🔍</span>
                                    <p class="font-bold text-white text-sm">No se encontraron usuarios</p>
                                    <p class="text-slate-400">Intenta ajustar los criterios de búsqueda o filtros.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-slate-800 p-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
