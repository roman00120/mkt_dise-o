@extends('layouts.admin')
@section('title', 'Panel de administración')
@section('header', 'Panel de administración')

@section('content')
<div class="space-y-6">
    <!-- Header Hero Section -->
    <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-800 bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-red-600/20 border border-red-500/40 px-2.5 py-1 text-xs font-bold text-red-300 uppercase tracking-wider">
                    👑 Control General
                </span>
                <span class="text-xs text-slate-400">TG Creative Hub</span>
            </div>
            <h1 class="mt-2 text-2xl sm:text-3xl font-black text-white tracking-tight">
                Panel de administración
            </h1>
            <p class="mt-1 text-sm text-slate-400 max-w-xl">
                ¡Hola, {{ auth()->user()->name }}! 👋 Centro de supervisión general, aprobación de solicitudes y gestión de equipo.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('creative.requests.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 px-4 py-3 text-xs font-extrabold text-white transition shadow-lg hover:shadow-emerald-900/30">
                <span>📋</span>
                <span>Flujo de Aprobación</span>
            </a>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-500 px-4 py-3 text-xs font-extrabold text-white transition shadow-lg hover:shadow-red-900/30">
                <span>➕</span>
                <span>Crear Usuario</span>
            </a>
        </div>
    </div>

    <!-- Quick Shortcuts Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('creative.requests.index') }}" class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900/90 p-3.5 hover:border-slate-700 hover:bg-slate-800/80 transition group shadow">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-500/10 text-amber-400 group-hover:scale-110 transition">
                📋
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-white group-hover:text-amber-300 transition truncate">Validar Solicitudes</p>
                <p class="text-[11px] text-slate-400">Revisión de Hugo</p>
            </div>
        </a>

        <a href="{{ route('creative.requests.kanban') }}" class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900/90 p-3.5 hover:border-slate-700 hover:bg-slate-800/80 transition group shadow">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-400 group-hover:scale-110 transition">
                🎨
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-white group-hover:text-indigo-300 transition truncate">Operación Creativa</p>
                <p class="text-[11px] text-slate-400">Tablero Kanban</p>
            </div>
        </a>

        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900/90 p-3.5 hover:border-slate-700 hover:bg-slate-800/80 transition group shadow">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-400 group-hover:scale-110 transition">
                👥
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-white group-hover:text-emerald-300 transition truncate">Gestión de Usuarios</p>
                <p class="text-[11px] text-slate-400">Cuentas y roles</p>
            </div>
        </a>

        <a href="{{ route('admin.kpis.index') }}" class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900/90 p-3.5 hover:border-slate-700 hover:bg-slate-800/80 transition group shadow">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-500/10 text-red-400 group-hover:scale-110 transition">
                📊
            </div>
            <div class="min-w-0">
                <p class="text-xs font-bold text-white group-hover:text-red-300 transition truncate">KPIs y Métricas Hub</p>
                <p class="text-[11px] text-slate-400">Analítica corporativa</p>
            </div>
        </a>
    </div>

    <!-- MAIN OPERATIONAL KPIS -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Pendientes de Validación -->
        <div class="rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-950/20 to-slate-900 p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Por Aprobar (Hugo)</span>
                <span class="rounded bg-amber-500/20 px-2 py-0.5 text-[10px] font-extrabold text-amber-300">Acción requerida</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-4xl font-black text-white leading-none">{{ $metrics['pending_validation'] ?? 0 }}</p>
                <a href="{{ route('creative.requests.index') }}" class="text-xs font-bold text-amber-400 hover:underline flex items-center gap-1">
                    <span>Revisar</span>
                    <span>→</span>
                </a>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Solicitudes enviadas por Marketing listas para validación</p>
        </div>

        <!-- Entregables en Revisión -->
        <div class="rounded-xl border border-blue-500/30 bg-gradient-to-br from-blue-950/20 to-slate-900 p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-400 uppercase tracking-wider">Entregables de Caro</span>
                <span class="rounded bg-blue-500/20 px-2 py-0.5 text-[10px] font-extrabold text-blue-300">En revisión</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-4xl font-black text-white leading-none">{{ $metrics['pending_deliverables'] ?? 0 }}</p>
                <a href="{{ route('creative.requests.index') }}" class="text-xs font-bold text-blue-400 hover:underline flex items-center gap-1">
                    <span>Ver entregas</span>
                    <span>→</span>
                </a>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Versiones finales o borradores listas para visto bueno</p>
        </div>

        <!-- Solicitudes Activas -->
        <div class="rounded-xl border border-indigo-500/30 bg-gradient-to-br from-indigo-950/20 to-slate-900 p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Solicitudes Activas</span>
                <span class="rounded bg-indigo-500/20 px-2 py-0.5 text-[10px] font-extrabold text-indigo-300">En flujo</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-4xl font-black text-white leading-none">{{ $metrics['active_requests'] ?? 0 }}</p>
                <a href="{{ route('creative.requests.index') }}" class="text-xs font-bold text-indigo-400 hover:underline flex items-center gap-1">
                    <span>Ver todas</span>
                    <span>→</span>
                </a>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Total de proyectos en diseño, video o render</p>
        </div>

        <!-- Tasa de Finalización -->
        <div class="rounded-xl border border-emerald-500/30 bg-gradient-to-br from-emerald-950/20 to-slate-900 p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Tasa de Finalización</span>
                <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-[10px] font-extrabold text-emerald-300">KPI Eficiencia</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-4xl font-black text-white leading-none">{{ $metrics['completion_rate'] ?? 0 }}%</p>
                <span class="text-xs font-bold text-emerald-400">
                    {{ $metrics['completed_requests'] ?? 0 }} / {{ $metrics['total_requests'] ?? 0 }}
                </span>
            </div>
            <div class="mt-3.5 h-1.5 w-full rounded-full bg-slate-800 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-500" style="width: {{ $metrics['completion_rate'] ?? 0 }}%"></div>
            </div>
        </div>
    </div>

    <!-- KPIS SECTION: DESGLOSE POR SERVICIO Y ESTADO -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- KPIS POR SERVICIO: ¿CUÁNTAS FUERON DE QUÉ? -->
        <section class="lg:col-span-2 rounded-xl border border-slate-800 bg-slate-900/90 p-5 shadow-lg">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h2 class="font-bold text-white text-base flex items-center gap-2">
                        <span>📊</span>
                        <span>KPIs por Tipo de Servicio (¿Cuántas fueron de qué?)</span>
                    </h2>
                    <p class="text-xs text-slate-400">Distribución de volumen de trabajo acumulado</p>
                </div>
                <span class="rounded-lg bg-slate-800 px-2.5 py-1 text-xs font-extrabold text-slate-300">
                    Total: {{ $metrics['total_requests'] ?? 0 }}
                </span>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                @php
                    $total = max(1, $metrics['total_requests'] ?? 1);
                    $designCount = $serviceBreakdown['design'] ?? 0;
                    $videoCount = $serviceBreakdown['video'] ?? 0;
                    $renderCount = $serviceBreakdown['render'] ?? 0;

                    $designPct = round(($designCount / $total) * 100);
                    $videoPct = round(($videoCount / $total) * 100);
                    $renderPct = round(($renderCount / $total) * 100);
                @endphp

                <!-- Diseño Gráfico Card -->
                <div class="rounded-xl border border-indigo-500/20 bg-slate-950/60 p-4 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-indigo-400 uppercase">🎨 Diseño Gráfico</span>
                        <span class="text-xs font-extrabold text-slate-400">{{ $designPct }}%</span>
                    </div>
                    <p class="mt-2 text-3xl font-black text-white leading-none">{{ $designCount }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">solicitudes de diseño</p>
                    <div class="mt-3 h-1.5 w-full rounded-full bg-slate-800 overflow-hidden">
                        <div class="h-full bg-indigo-500 transition-all duration-500" style="width: {{ $designPct }}%"></div>
                    </div>
                </div>

                <!-- Video Card -->
                <div class="rounded-xl border border-purple-500/20 bg-slate-950/60 p-4 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-purple-400 uppercase">🎬 Video</span>
                        <span class="text-xs font-extrabold text-slate-400">{{ $videoPct }}%</span>
                    </div>
                    <p class="mt-2 text-3xl font-black text-white leading-none">{{ $videoCount }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">solicitudes de audiovisual</p>
                    <div class="mt-3 h-1.5 w-full rounded-full bg-slate-800 overflow-hidden">
                        <div class="h-full bg-purple-500 transition-all duration-500" style="width: {{ $videoPct }}%"></div>
                    </div>
                </div>

                <!-- Render 3D Card -->
                <div class="rounded-xl border border-pink-500/20 bg-slate-950/60 p-4 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-pink-400 uppercase">📦 Render 3D</span>
                        <span class="text-xs font-extrabold text-slate-400">{{ $renderPct }}%</span>
                    </div>
                    <p class="mt-2 text-3xl font-black text-white leading-none">{{ $renderCount }}</p>
                    <p class="mt-1 text-[11px] text-slate-400">solicitudes de renderizado</p>
                    <div class="mt-3 h-1.5 w-full rounded-full bg-slate-800 overflow-hidden">
                        <div class="h-full bg-pink-500 transition-all duration-500" style="width: {{ $renderPct }}%"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- KPI SUMMARY SIDE CARD -->
        <section class="rounded-xl border border-slate-800 bg-slate-900/90 p-5 shadow-lg flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h2 class="font-bold text-white text-base flex items-center gap-2">
                        <span>⚡</span>
                        <span>Métricas Clave de Salud</span>
                    </h2>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between p-2.5 rounded-lg border border-slate-800 bg-slate-950/60">
                        <span class="text-xs font-medium text-slate-300">🔥 Urgentes / Alta Prioridad</span>
                        <span class="text-sm font-black text-amber-400">{{ $metrics['urgent_requests'] ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-lg border border-slate-800 bg-slate-950/60">
                        <span class="text-xs font-medium text-slate-300">⏳ Solicitudes Vencidas</span>
                        <span class="text-sm font-black {{ ($metrics['overdue_requests'] ?? 0) > 0 ? 'text-red-400' : 'text-slate-400' }}">
                            {{ $metrics['overdue_requests'] ?? 0 }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-lg border border-slate-800 bg-slate-950/60">
                        <span class="text-xs font-medium text-slate-300">✅ Completadas con Éxito</span>
                        <span class="text-sm font-black text-emerald-400">{{ $metrics['completed_requests'] ?? 0 }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-lg border border-slate-800 bg-slate-950/60">
                        <span class="text-xs font-medium text-slate-300">🚫 Canceladas / Rechazadas</span>
                        <span class="text-sm font-black text-slate-400">{{ $statusBreakdown['cancelled'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.reports.executive') }}" class="mt-4 flex items-center justify-center gap-1.5 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 p-2.5 text-xs font-bold text-white transition shadow">
                <span>📈 Ver Reporte Ejecutivo Completo</span>
            </a>
        </section>
    </div>

    <!-- Alert Banner (If Any) -->
    @if($alerts)
        <section class="rounded-xl border border-amber-500/50 bg-amber-950/30 p-4 shadow-lg">
            <div class="flex items-center gap-2">
                <span class="text-xl">⚠️</span>
                <h2 class="font-bold text-amber-300 text-sm">Alertas del Sistema</h2>
            </div>
            <ul class="mt-2 space-y-1 text-xs text-amber-200/90 pl-6 list-disc">
                @foreach($alerts as $alert)
                    <li>{{ $alert }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <!-- Tables Section -->
    <div class="grid gap-6 xl:grid-cols-2">
        <!-- Recent Requests Section -->
        <section class="rounded-xl border border-slate-800 bg-slate-900/90 p-5 shadow-lg flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h2 class="font-bold text-white text-base">Últimas Solicitudes</h2>
                        <p class="text-xs text-slate-400">Actividad reciente en el hub</p>
                    </div>
                    <a href="{{ route('creative.requests.index') }}" class="text-xs font-bold text-emerald-400 hover:underline">
                        Ver panel completo →
                    </a>
                </div>

                <div class="mt-4 space-y-2.5">
                    @forelse($recentRequests as $request)
                        @php
                            $statusVal = $request->status instanceof \App\Enums\RequestStatus ? $request->status->value : (string) ($request->status?->value ?? $request->status);
                            $statusBadge = match($statusVal) {
                                'in_validation' => ['text' => 'Por validar', 'class' => 'bg-amber-500/20 text-amber-300 border-amber-500/30'],
                                'marketing_review' => ['text' => 'En revisión', 'class' => 'bg-blue-500/20 text-blue-300 border-blue-500/30'],
                                'assigned', 'in_progress', 'creative_review' => ['text' => 'En proceso', 'class' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30'],
                                'completed' => ['text' => 'Completada', 'class' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30'],
                                'cancelled', 'rejected' => ['text' => 'Cancelada', 'class' => 'bg-slate-700/50 text-slate-400 border-slate-600'],
                                default => ['text' => ucfirst($statusVal), 'class' => 'bg-slate-800 text-slate-300 border-slate-700']
                            };
                        @endphp
                        <a href="{{ route('creative.requests.show', $request->id) }}" class="flex items-center justify-between gap-3 rounded-lg border border-slate-800 bg-slate-950/60 p-3 hover:border-slate-700 hover:bg-slate-800/50 transition">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-red-400">#{{ $request->folio ?? $request->id }}</span>
                                    <h3 class="truncate text-xs font-bold text-white">{{ $request->title ?: 'Sin título' }}</h3>
                                </div>
                                <p class="mt-0.5 text-[11px] text-slate-400">
                                    De: <span class="text-slate-300 font-medium">{{ $request->requester?->name ?? 'Marketing' }}</span>
                                    @if($request->assignee)
                                        · Asignado a: <span class="text-slate-300 font-medium">{{ $request->assignee->name }}</span>
                                    @endif
                                </p>
                            </div>
                            <span class="shrink-0 rounded-md border px-2 py-1 text-[11px] font-bold {{ $statusBadge['class'] }}">
                                {{ $statusBadge['text'] }}
                            </span>
                        </a>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            No hay solicitudes registradas aún.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Recent Users Section -->
        <section class="rounded-xl border border-slate-800 bg-slate-900/90 p-5 shadow-lg flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h2 class="font-bold text-white text-base">Equipo y Usuarios</h2>
                        <p class="text-xs text-slate-400">Cuentas registradas recientemente</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-emerald-400 hover:underline">
                        Gestionar usuarios →
                    </a>
                </div>

                <div class="mt-4 space-y-2.5">
                    @forelse($recentUsers as $user)
                        <a href="{{ route('admin.users.show', $user) }}" class="flex items-center justify-between gap-3 rounded-lg border border-slate-800 bg-slate-950/60 p-3 hover:border-slate-700 hover:bg-slate-800/50 transition">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-600/20 text-xs font-bold text-red-300 border border-red-500/30">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-xs font-bold text-white">{{ $user->name }}</h3>
                                    <p class="text-[11px] text-slate-400">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block rounded bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-slate-300 uppercase">
                                    {{ match($user->role?->value ?? $user->role) {
                                        'admin' => 'Admin',
                                        'marketing' => 'Marketing',
                                        'creative' => 'Creativo',
                                        'supervisor' => 'Supervisor',
                                        default => ucfirst($user->role?->value ?? 'Usuario')
                                    } }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            No hay usuarios registrados.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
