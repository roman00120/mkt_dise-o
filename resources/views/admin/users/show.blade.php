@extends('layouts.admin')
@section('title', 'Detalle de usuario · '.$managedUser->name)
@section('header', 'Detalle de usuario')

@section('content')
@php
    $roleVal = $managedUser->role?->value ?? (string)$managedUser->role;
    $statusVal = $managedUser->status?->value ?? (string)$managedUser->status;
    $initials = strtoupper(substr($managedUser->name, 0, 2));

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
        'admin' => ['label' => '👑 Administrador (Hugo)', 'style' => 'bg-red-500/20 text-red-300 border-red-500/40'],
        'supervisor' => ['label' => '⚡ Supervisor', 'style' => 'bg-amber-500/20 text-amber-300 border-amber-500/40'],
        'marketing' => ['label' => '📢 Marketing (Solicitante)', 'style' => 'bg-blue-500/20 text-blue-300 border-blue-500/40'],
        'creative' => ['label' => '🎨 Operación Creativa', 'style' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40'],
        'design' => ['label' => '🎨 Diseño Gráfico (Carolina)', 'style' => 'bg-purple-500/20 text-purple-300 border-purple-500/40'],
        'video' => ['label' => '🎬 Video (Gerardo)', 'style' => 'bg-pink-500/20 text-pink-300 border-pink-500/40'],
        'render' => ['label' => '📦 Render 3D (Jesús)', 'style' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40'],
        default => ['label' => ucfirst($roleVal), 'style' => 'bg-slate-800 text-slate-300 border-slate-700']
    };
@endphp

<div class="mx-auto max-w-5xl space-y-6">

    <!-- Navigation Header Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-800 bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 p-6 shadow-xl">
        <div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition mb-2">
                <span>←</span>
                <span>Volver al Directorio de Usuarios</span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-2">
                <span>👤</span>
                <span>Expediente de Usuario</span>
            </h1>
            <p class="mt-1 text-sm text-slate-400">
                Información detallada de la cuenta, permisos asignados y control de estado.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.edit', $managedUser) }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 px-5 py-2.5 text-xs font-extrabold text-white transition shadow-lg hover:shadow-red-900/30">
                <span>✏️</span>
                <span>Editar Usuario</span>
            </a>
        </div>
    </div>

    <!-- Main Profile Card -->
    <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 sm:p-8 shadow-2xl space-y-8">
        <!-- Top Identity Info -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 border-b border-slate-800 pb-6">
            <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-gradient-to-br {{ $avatarGradient }} text-2xl font-black text-white uppercase shadow-xl border border-white/10">
                {{ $initials }}
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-2xl font-black text-white tracking-tight">
                        {{ $managedUser->name }}
                    </h2>

                    <span class="inline-flex items-center gap-1 rounded-lg border px-3 py-1 text-xs font-extrabold {{ $roleBadge['style'] }}">
                        {{ $roleBadge['label'] }}
                    </span>
                </div>

                <p class="mt-1 text-sm font-mono text-slate-400">
                    {{ $managedUser->email }}
                </p>
            </div>
        </div>

        <!-- Spec Cards Grid -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Role -->
            <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Rol Operativo</div>
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-lg">🎭</span>
                    <span class="text-sm font-extrabold text-white">{{ $roleBadge['label'] }}</span>
                </div>
            </div>

            <!-- Status -->
            <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Estado de Cuenta</div>
                <div class="mt-2">
                    @if($statusVal === 'active')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 px-3 py-0.5 text-xs font-extrabold text-emerald-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>🟢 Activo</span>
                        </span>
                    @elseif($statusVal === 'suspended')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-500/15 border border-red-500/30 px-3 py-0.5 text-xs font-extrabold text-red-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>
                            <span>🔴 Suspendido</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-800 border border-slate-700 px-3 py-0.5 text-xs font-extrabold text-slate-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span>
                            <span>⚪ Inactivo</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Department -->
            <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Departamento</div>
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-lg">🏢</span>
                    <span class="text-sm font-extrabold text-white">{{ $managedUser->department?->name ?? 'Sin asignar' }}</span>
                </div>
            </div>

            <!-- Registration Date -->
            <div class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Fecha de Alta</div>
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-lg">📅</span>
                    <span class="text-sm font-extrabold text-white">{{ $managedUser->created_at?->format('d M Y · H:i') ?? '—' }}</span>
                </div>
            </div>
        </div>

        <!-- Administrative Security Actions Panel -->
        <div class="rounded-xl border border-slate-800 bg-slate-950/50 p-6 space-y-4">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-400">
                ⚙️ Acciones de Seguridad y Administración
            </h3>

            <div class="flex flex-wrap items-center gap-4">
                <!-- Reset Password Email -->
                <form method="POST" action="{{ route('admin.users.send-password-reset', $managedUser) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-800 hover:bg-slate-700 px-4 py-2.5 text-xs font-bold text-white transition shadow">
                        <span>🔑</span>
                        <span>Enviar Restablecimiento de Contraseña</span>
                    </button>
                </form>

                <!-- Toggle Suspend / Activate -->
                @if($statusVal !== 'active')
                    <form method="POST" action="{{ route('admin.users.activate', $managedUser) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 px-4 py-2.5 text-xs font-extrabold text-white transition shadow">
                            <span>🟢</span>
                            <span>Habilitar / Activar Cuenta</span>
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.suspend', $managedUser) }}">
                        @csrf
                        <input type="hidden" name="reason" value="Suspensión administrativa">
                        <input type="hidden" name="confirmed" value="1">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 hover:bg-amber-500 px-4 py-2.5 text-xs font-extrabold text-white transition shadow">
                            <span>⛔</span>
                            <span>Suspender Acceso de Usuario</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
