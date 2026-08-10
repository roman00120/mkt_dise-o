@extends('layouts.admin')
@section('title', 'KPIs y Métricas Corporativas')
@section('header', 'KPIs y Métricas Corporativas')

@section('content')
<div class="space-y-6">
    <!-- Header Hero Section -->
    <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-800 bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 p-6 shadow-xl">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-amber-500/20 border border-amber-500/40 px-2.5 py-1 text-xs font-bold text-amber-300 uppercase tracking-wider">
                    📊 Analítica Estratégica
                </span>
                <span class="text-xs text-slate-400">TG Creative Hub</span>
            </div>
            <h1 class="mt-2 text-2xl sm:text-3xl font-black text-white tracking-tight">
                Centro de KPIs y Métricas Corporativas
            </h1>
            <p class="mt-1 text-sm text-slate-400 max-w-2xl">
                Supervisión del rendimiento operativo, cumplimiento de tiempos de respuesta (SLA), eficiencia por servicio y tasa de aprobación del equipo.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.reports.export.csv', request()->query()) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-800/90 hover:bg-slate-700 px-4 py-2.5 text-xs font-bold text-white transition shadow">
                <span>📄</span>
                <span>Exportar CSV</span>
            </a>
            <a href="{{ route('admin.reports.export.pdf', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-500 px-4 py-2.5 text-xs font-bold text-white transition shadow-lg hover:shadow-red-900/30">
                <span>📕</span>
                <span>Descargar PDF</span>
            </a>
        </div>
    </div>

    <!-- Filter Control Bar -->
    <form method="GET" action="{{ route('admin.kpis.index') }}" class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-slate-800 bg-slate-900/90 p-4 shadow-lg">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <label for="period" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Periodo:</label>
                <select id="period" name="period" onchange="this.form.submit()" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs font-semibold text-white focus:border-red-500 focus:outline-none">
                    <option value="7" {{ $period === '7' ? 'selected' : '' }}>Últimos 7 días</option>
                    <option value="30" {{ $period === '30' ? 'selected' : '' }}>Últimos 30 días</option>
                    <option value="90" {{ $period === '90' ? 'selected' : '' }}>Últimos 90 días</option>
                    <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Todo el historial</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label for="service" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Servicio:</label>
                <select id="service" name="service" onchange="this.form.submit()" class="rounded-lg border border-slate-700 bg-slate-950 px-3 py-1.5 text-xs font-semibold text-white focus:border-red-500 focus:outline-none">
                    <option value="all" {{ $service === 'all' ? 'selected' : '' }}>Todos los servicios</option>
                    <option value="design" {{ $service === 'design' ? 'selected' : '' }}>🎨 Diseño Gráfico</option>
                    <option value="video" {{ $service === 'video' ? 'selected' : '' }}>🎬 Video Audiovisual</option>
                    <option value="render" {{ $service === 'render' ? 'selected' : '' }}>📦 Render 3D</option>
                </select>
            </div>
        </div>

        <div class="text-xs text-slate-400 font-medium">
            <span>Mostrando métricas calculadas en tiempo real</span>
        </div>
    </form>

    <!-- SECTION 1: TOP EXECUTIVE KPIS -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- SLA On-Time Delivery -->
        <div class="rounded-xl border border-emerald-500/30 bg-gradient-to-br from-emerald-950/30 to-slate-900 p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Cumplimiento de SLA</span>
                <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-[10px] font-extrabold text-emerald-300">Entrega a Tiempo</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-4xl font-black text-white leading-none">{{ $kpis['on_time_rate'] }}%</p>
                <span class="text-xs font-bold text-emerald-400 flex items-center gap-0.5">
                    <span>🟢</span> <span>Óptimo</span>
                </span>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Proyectos finalizados dentro del plazo requerido</p>
        </div>

        <!-- First Pass Yield -->
        <div class="rounded-xl border border-indigo-500/30 bg-gradient-to-br from-indigo-950/30 to-slate-900 p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Aprobación 1ra Entrega</span>
                <span class="rounded bg-indigo-500/20 px-2 py-0.5 text-[10px] font-extrabold text-indigo-300">Calidad Directa</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-4xl font-black text-white leading-none">{{ $kpis['first_pass_yield'] }}%</p>
                <span class="text-xs font-bold text-indigo-400">Visto Bueno</span>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Entregables aprobados sin requerir correcciones</p>
        </div>

        <!-- Tasa de Finalización -->
        <div class="rounded-xl border border-blue-500/30 bg-gradient-to-br from-blue-950/30 to-slate-900 p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-blue-400 uppercase tracking-wider">Efectividad Global</span>
                <span class="rounded bg-blue-500/20 px-2 py-0.5 text-[10px] font-extrabold text-blue-300">Completadas</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-4xl font-black text-white leading-none">{{ $kpis['completion_rate'] }}%</p>
                <span class="text-xs font-bold text-blue-300">{{ $kpis['completed_requests'] }} de {{ $kpis['total_requests'] }}</span>
            </div>
            <div class="mt-3.5 h-1.5 w-full rounded-full bg-slate-800 overflow-hidden">
                <div class="h-full bg-blue-500 transition-all duration-500" style="width: {{ $kpis['completion_rate'] }}%"></div>
            </div>
        </div>

        <!-- Carga Urgente -->
        <div class="rounded-xl border border-amber-500/30 bg-gradient-to-br from-amber-950/30 to-slate-900 p-5 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Solicitudes Urgentes</span>
                <span class="rounded bg-amber-500/20 px-2 py-0.5 text-[10px] font-extrabold text-amber-300">Prioridad Alta</span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <p class="text-4xl font-black text-white leading-none">{{ $kpis['urgent_requests'] }}</p>
                <span class="text-xs font-bold text-amber-400">{{ $kpis['urgent_rate'] }}% del total</span>
            </div>
            <p class="mt-2 text-[11px] text-slate-400">Peticiones clasificadas con carácter de urgencia</p>
        </div>
    </div>

    <!-- SECTION 2: SERVICE PERFORMANCE MATRIX -->
    <section class="rounded-xl border border-slate-800 bg-slate-900/90 p-6 shadow-lg">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h2 class="font-bold text-white text-base flex items-center gap-2">
                    <span>🎨</span>
                    <span>Matriz de Rendimiento por Área Creativa</span>
                </h2>
                <p class="text-xs text-slate-400">Desglose de productividad, tiempos de atención y entregas por disciplina</p>
            </div>
            <span class="rounded-lg bg-slate-800 px-3 py-1 text-xs font-extrabold text-slate-300">
                3 Áreas Activas
            </span>
        </div>

        <div class="mt-6 grid gap-6 md:grid-cols-3">
            @foreach($servicesKpis as $key => $sData)
                @php
                    $sTotal = max(1, $sData['total']);
                    $sCompPct = round(($sData['completed'] / $sTotal) * 100);
                @endphp
                <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5 shadow-inner flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                            <h3 class="font-extrabold text-white text-sm">{{ $sData['name'] }}</h3>
                            <span class="rounded bg-slate-800 px-2 py-0.5 text-[11px] font-bold text-slate-300">
                                {{ $sData['total'] }} Peticiones
                            </span>
                        </div>

                        <div class="mt-4 space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Completadas con éxito:</span>
                                <strong class="text-emerald-400 font-bold">{{ $sData['completed'] }} ({{ $sCompPct }}%)</strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">En desarrollo actualmente:</span>
                                <strong class="text-indigo-400 font-bold">{{ $sData['in_progress'] }}</strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Tiempo de entrega estimado:</span>
                                <strong class="text-slate-200 font-bold">{{ $sData['avg_days'] }}</strong>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase mb-1">
                                <span>Avance de entregas</span>
                                <span>{{ $sCompPct }}%</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-slate-800 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-red-500 to-emerald-400 transition-all duration-500" style="width: {{ $sCompPct }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-t border-slate-800/60 text-right">
                        <a href="{{ route('admin.reports.executive', ['service' => $key]) }}" class="text-xs font-bold text-red-400 hover:underline inline-flex items-center gap-1">
                            <span>Ver analítica de {{ str_replace(['🎨 ', '🎬 ', '📦 '], '', $sData['name']) }}</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- SECTION 3: OPERATIONAL FUNNEL CONVERSION -->
    <section class="rounded-xl border border-slate-800 bg-slate-900/90 p-6 shadow-lg">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h2 class="font-bold text-white text-base flex items-center gap-2">
                    <span>🔄</span>
                    <span>Embudo de Conversión Operativa (Workflow Funnel)</span>
                </h2>
                <p class="text-xs text-slate-400">Trayectoria de avance desde que Marketing crea la solicitud hasta que se entrega finalizada</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Funnel Step 1 -->
            <div class="rounded-xl border border-slate-800 bg-slate-950/80 p-4 relative">
                <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase">
                    <span>1. Solicitadas</span>
                    <span class="rounded bg-slate-800 px-2 py-0.5 text-slate-300">100%</span>
                </div>
                <p class="mt-3 text-3xl font-black text-white leading-none">{{ $kpis['total_requests'] }}</p>
                <p class="mt-1 text-[11px] text-slate-400">Ingresadas por Marketing</p>
            </div>

            <!-- Funnel Step 2 -->
            <div class="rounded-xl border border-amber-500/30 bg-amber-950/20 p-4 relative">
                <div class="flex items-center justify-between text-xs font-bold text-amber-400 uppercase">
                    <span>2. Por Validar</span>
                    <span class="rounded bg-amber-500/20 px-2 py-0.5 text-amber-300">Hugo</span>
                </div>
                <p class="mt-3 text-3xl font-black text-white leading-none">{{ $kpis['in_validation'] }}</p>
                <p class="mt-1 text-[11px] text-slate-400">En revisión inicial de Hugo</p>
            </div>

            <!-- Funnel Step 3 -->
            <div class="rounded-xl border border-indigo-500/30 bg-indigo-950/20 p-4 relative">
                <div class="flex items-center justify-between text-xs font-bold text-indigo-400 uppercase">
                    <span>3. En Desarrollo</span>
                    <span class="rounded bg-indigo-500/20 px-2 py-0.5 text-indigo-300">Creativos</span>
                </div>
                <p class="mt-3 text-3xl font-black text-white leading-none">{{ $kpis['in_progress'] }}</p>
                <p class="mt-1 text-[11px] text-slate-400">Diseñando / Editando</p>
            </div>

            <!-- Funnel Step 4 -->
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-950/20 p-4 relative">
                <div class="flex items-center justify-between text-xs font-bold text-emerald-400 uppercase">
                    <span>4. Finalizadas</span>
                    <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-emerald-300">Éxito</span>
                </div>
                <p class="mt-3 text-3xl font-black text-white leading-none">{{ $kpis['completed_requests'] }}</p>
                <p class="mt-1 text-[11px] text-slate-400">Aprobadas y entregadas</p>
            </div>
        </div>
    </section>

    <!-- SECTION 4: CREATIVE TEAM PERFORMANCE TABLE -->
    <section class="rounded-xl border border-slate-800 bg-slate-900/90 p-6 shadow-lg">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h2 class="font-bold text-white text-base flex items-center gap-2">
                    <span>👥</span>
                    <span>Rendimiento del Equipo Creativo</span>
                </h2>
                <p class="text-xs text-slate-400">Asignación de carga de trabajo por diseñador / editor</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-emerald-400 hover:underline">
                Gestionar miembros →
            </a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs font-bold text-slate-400 uppercase border-b border-slate-800">
                        <th class="py-3 px-3 text-left">Miembro del Equipo</th>
                        <th class="py-3 px-3 text-left">Rol</th>
                        <th class="py-3 px-3 text-center">En Desarrollo</th>
                        <th class="py-3 px-3 text-center">Completadas</th>
                        <th class="py-3 px-3 text-right">Estatus de Carga</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($creatives as $creative)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 px-3 font-semibold text-white flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-600/20 text-xs font-bold text-red-300 border border-red-500/30">
                                    {{ strtoupper(substr($creative->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-white leading-tight">{{ $creative->name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $creative->email }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-xs text-slate-300">
                                <span class="rounded bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-slate-300 uppercase">
                                    {{ match($creative->role?->value ?? $creative->role) {
                                        'creative' => 'Creativo',
                                        'design' => 'Diseño Gráfico',
                                        'video' => 'Video',
                                        'render' => 'Render 3D',
                                        default => ucfirst($creative->role?->value ?? 'Usuario')
                                    } }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center font-extrabold text-indigo-400">
                                {{ $creative->active_count }}
                            </td>
                            <td class="py-3 px-3 text-center font-extrabold text-emerald-400">
                                {{ $creative->completed_count }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                @if($creative->active_count > 3)
                                    <span class="rounded bg-red-500/20 border border-red-500/30 px-2 py-0.5 text-[10px] font-bold text-red-300">Carga Alta</span>
                                @elseif($creative->active_count > 0)
                                    <span class="rounded bg-emerald-500/20 border border-emerald-500/30 px-2 py-0.5 text-[10px] font-bold text-emerald-300">Carga Óptima</span>
                                @else
                                    <span class="rounded bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-slate-400">Disponible</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-xs text-slate-400">
                                No se registraron creativos asignables aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
