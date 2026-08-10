@extends('layouts.creative')
@section('title', 'Panel Creativo')
@section('header', 'Panel Creativo de Operaciones')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <!-- Hero Header Banner -->
    <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-800 bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-indigo-500/20 border border-indigo-500/40 px-2.5 py-1 text-xs font-bold text-indigo-300 uppercase tracking-wider">
                    🎨 Centro Creativo
                </span>
                <span class="text-xs text-slate-400">TG Creative Hub</span>
            </div>
            <h1 class="mt-2 text-2xl sm:text-3xl font-black text-white tracking-tight">
                Panel creativo
            </h1>
            <p class="mt-1 text-sm text-slate-400 max-w-xl">
                Bienvenida, <strong class="text-white font-extrabold">{{ auth()->user()->name }}</strong>. Control de solicitudes asignadas, entregables y cola de producción.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('creative.requests.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-500 px-4 py-2.5 text-xs font-extrabold text-white transition shadow-lg hover:shadow-red-900/30">
                <span>📥</span>
                <span>Bandeja de Entrada</span>
            </a>
            <a href="{{ route('creative.requests.kanban') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-800/90 hover:bg-slate-700 px-4 py-2.5 text-xs font-bold text-white transition shadow">
                <span>📋</span>
                <span>Tablero Kanban</span>
            </a>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Asignadas -->
        <div class="rounded-xl border border-blue-500/30 bg-gradient-to-br from-blue-950/20 to-slate-900 p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-400 uppercase tracking-wider">Solicitudes Totales</span>
                <span class="rounded bg-blue-500/20 px-2 py-0.5 text-[10px] font-extrabold text-blue-300">Asignadas</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-4xl font-black text-white leading-none">{{ $metrics['total'] ?? 0 }}</p>
                <span class="text-xs text-blue-300 font-bold">Proyectos</span>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Total de requerimientos asignados a tu cuenta</p>
        </div>

        <!-- Pendientes de Validación -->
        <div class="rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-950/20 to-slate-900 p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Por Validar</span>
                <span class="rounded bg-amber-500/20 px-2 py-0.5 text-[10px] font-extrabold text-amber-300">Supervisión</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-4xl font-black text-white leading-none">{{ $metrics['pending'] ?? 0 }}</p>
                <span class="text-xs text-amber-300 font-bold">En cola</span>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Esperando revisión inicial de requisitos</p>
        </div>

        <!-- En Proceso -->
        <div class="rounded-xl border border-indigo-500/30 bg-gradient-to-br from-indigo-950/20 to-slate-900 p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">En Desarrollo</span>
                <span class="rounded bg-indigo-500/20 px-2 py-0.5 text-[10px] font-extrabold text-indigo-300">En marcha</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-4xl font-black text-white leading-none">{{ $metrics['in_progress'] ?? 0 }}</p>
                <span class="text-xs text-indigo-300 font-bold flex items-center gap-1">
                    <span class="h-2 w-2 rounded-full bg-indigo-400 animate-pulse"></span> Activas
                </span>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Proyectos actualmente en fase de producción</p>
        </div>

        <!-- Bloqueadas / Finalizadas -->
        <div class="rounded-xl border border-emerald-500/30 bg-gradient-to-br from-emerald-950/20 to-slate-900 p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Finalizadas / Listas</span>
                <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-[10px] font-extrabold text-emerald-300">Entregadas</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-4xl font-black text-white leading-none">
                    {{ $mine->filter(fn($r) => ($r->status?->value ?? (string)$r->status) === 'completed')->count() }}
                </p>
                <span class="text-xs text-emerald-300 font-bold">🟢 Completadas</span>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Resultados entregados con visto bueno</p>
        </div>
    </div>

    <!-- Main Operational Workspace Grid -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Section: Mi Trabajo Asignado -->
        <section class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div>
                        <h2 class="font-bold text-white text-base flex items-center gap-2">
                            <span>💼</span>
                            <span>Mi Trabajo y Proyectos Asignados</span>
                        </h2>
                        <p class="text-xs text-slate-400">Solicitudes que requieren tu producción y entregables</p>
                    </div>
                    <span class="rounded-full bg-indigo-500/20 border border-indigo-500/30 px-3 py-1 text-xs font-extrabold text-indigo-300">
                        {{ $mine->count() }} Proyectos
                    </span>
                </div>

                <div class="mt-5 space-y-3.5">
                    @forelse($mine as $item)
                        @php
                            $sVal = $item->status?->value ?? (string)$item->status;
                            $srvVal = $item->service?->value ?? (string)$item->service;

                            $statusBadge = match($sVal) {
                                'completed' => ['label' => '✅ Finalizada', 'style' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40'],
                                'in_progress', 'assigned' => ['label' => '⚡ En Desarrollo', 'style' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40'],
                                'creative_review', 'marketing_review', 'in_validation' => ['label' => '⏳ En Revisión', 'style' => 'bg-amber-500/20 text-amber-300 border-amber-500/40'],
                                default => ['label' => ucfirst($sVal), 'style' => 'bg-slate-800 text-slate-300 border-slate-700']
                            };

                            $serviceName = match($srvVal) {
                                'design' => '🎨 Diseño Gráfico',
                                'video' => '🎬 Video Audiovisual',
                                'render' => '📦 Render 3D',
                                default => '🎨 Operación Creativa'
                            };
                        @endphp

                        <a href="{{ route('creative.requests.show', $item) }}" class="block rounded-xl border border-slate-800 bg-slate-950/80 p-4 hover:border-red-500/50 hover:bg-slate-950 transition group shadow">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-black text-red-400 tracking-wider">
                                    {{ $item->folio }}
                                </span>
                                <span class="rounded border px-2 py-0.5 text-[10px] font-bold {{ $statusBadge['style'] }}">
                                    {{ $statusBadge['label'] }}
                                </span>
                            </div>

                            <h3 class="mt-2 font-bold text-white text-base group-hover:text-red-300 transition">
                                {{ $item->title ?: 'Solicitud sin título' }}
                            </h3>

                            <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-slate-800/80 pt-2.5 text-xs text-slate-400">
                                <span class="font-medium text-slate-300">
                                    {{ $serviceName }}
                                </span>
                                <span class="flex items-center gap-1 font-semibold text-slate-400">
                                    <span>📅 Entrega:</span>
                                    <span class="text-slate-200">
                                        {{ $item->internal_due_date ? $item->internal_due_date->isoFormat('D MMM YYYY') : 'Sin fecha límite' }}
                                    </span>
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-800 p-8 text-center">
                            <span class="text-3xl">🎨</span>
                            <p class="mt-2 text-sm font-bold text-white">No tienes proyectos asignados actualmente</p>
                            <p class="text-xs text-slate-400 mt-1">Los nuevos proyectos asignados por el supervisor aparecerán aquí.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-800/60 text-right">
                <a href="{{ route('creative.requests.index') }}" class="text-xs font-bold text-red-400 hover:underline inline-flex items-center gap-1">
                    <span>Ver todas las solicitudes en la bandeja</span>
                    <span>→</span>
                </a>
            </div>
        </section>

        <!-- Section: Pendientes de Validación -->
        <section class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div>
                        <h2 class="font-bold text-white text-base flex items-center gap-2">
                            <span>⏳</span>
                            <span>Pendientes de Validación de Requisitos</span>
                        </h2>
                        <p class="text-xs text-slate-400">Solicitudes que requieren revisión inicial antes de iniciar producción</p>
                    </div>
                    <span class="rounded-full bg-amber-500/20 border border-amber-500/30 px-3 py-1 text-xs font-extrabold text-amber-300">
                        {{ $pendingValidation->count() }} Pendientes
                    </span>
                </div>

                <div class="mt-5 space-y-3.5">
                    @forelse($pendingValidation as $item)
                        @php
                            $srvVal = $item->service?->value ?? (string)$item->service;
                            $serviceName = match($srvVal) {
                                'design' => '🎨 Diseño Gráfico',
                                'video' => '🎬 Video Audiovisual',
                                'render' => '📦 Render 3D',
                                default => '🎨 Operación Creativa'
                            };
                        @endphp

                        <a href="{{ route('creative.requests.show', $item) }}" class="block rounded-xl border border-slate-800 bg-slate-950/80 p-4 hover:border-amber-500/50 hover:bg-slate-950 transition group shadow">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xs font-black text-amber-400 tracking-wider">
                                    {{ $item->folio }}
                                </span>
                                <span class="rounded bg-amber-500/20 border border-amber-500/30 px-2 py-0.5 text-[10px] font-bold text-amber-300">
                                    Por Validar
                                </span>
                            </div>

                            <h3 class="mt-2 font-bold text-white text-base group-hover:text-amber-300 transition">
                                {{ $item->title ?: 'Solicitud sin título' }}
                            </h3>

                            <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-slate-800/80 pt-2.5 text-xs text-slate-400">
                                <span class="font-semibold text-slate-300">
                                    {{ $serviceName }}
                                </span>
                                <span class="text-slate-400">
                                    Solicita: <strong class="text-slate-200">{{ $item->requester?->name ?? 'Marketing' }}</strong>
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-800 p-8 text-center">
                            <span class="text-3xl">✅</span>
                            <p class="mt-2 text-sm font-bold text-white">Todo al día</p>
                            <p class="text-xs text-slate-400 mt-1">No hay solicitudes pendientes de validación en este momento.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-800/60 text-right">
                <a href="{{ route('creative.requests.kanban') }}" class="text-xs font-bold text-amber-400 hover:underline inline-flex items-center gap-1">
                    <span>Ver tablero Kanban de la operación</span>
                    <span>→</span>
                </a>
            </div>
        </section>
    </div>
</div>
@endsection
