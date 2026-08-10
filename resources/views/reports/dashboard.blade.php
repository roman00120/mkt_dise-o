@extends($layout)
@section('title', $title)
@section('header', $header)

@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <div class="flex items-center gap-2">
            <p class="text-sm text-slate-400">Reportes</p>
            <span class="text-slate-600">/</span>
            <p class="text-sm font-medium text-red-400">{{ $header }}</p>
        </div>
        <h1 class="mt-1 text-2xl font-black text-white tracking-tight">{{ $title }}</h1>
        <p class="mt-1 text-xs text-slate-400 flex items-center gap-1.5">
            <span>⏱️ Calculado {{ now()->format('Y-m-d H:i') }}</span>
            <span>·</span>
            <span>Universo: solicitudes dentro del alcance y periodo seleccionado.</span>
        </p>
    </div>
    @if(auth()->user()->hasRole('admin'))
        <div class="flex items-center gap-2.5">
            <a class="inline-flex min-h-10 items-center gap-1.5 rounded-lg border border-slate-700 bg-slate-800/80 px-3.5 py-2 text-xs font-bold text-white hover:bg-slate-700 hover:border-slate-600 transition shadow" href="{{ route('admin.reports.export.csv', request()->query()) }}">
                <span>📄</span> Exportar CSV
            </a>
            <a class="inline-flex min-h-10 items-center gap-1.5 rounded-lg bg-red-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-red-500 transition shadow" href="{{ route('admin.reports.export.pdf', request()->query()) }}">
                <span>📕</span> Exportar PDF
            </a>
        </div>
    @endif
</div>

@include('reports.partials.filters')

<div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach($report['counts'] as $key => $value)
        @php
            $labelName = match(strtolower($key)) {
                'created' => 'Solicitudes Creadas',
                'sent' => 'Solicitudes Enviadas',
                'completed' => 'Solicitudes Completadas',
                'cancelled', 'canceled' => 'Solicitudes Canceladas',
                'in_progress' => 'En Proceso Creativo',
                'in_validation' => 'Pendientes de Aprobación',
                'drafts', 'draft' => 'Borradores',
                default => ucfirst(str_replace('_', ' ', $key))
            };
        @endphp
        <x-report.metric-card :label="$labelName" :value="$value" :comparison="$report['comparison'][$key] ?? null" />
    @endforeach
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="rounded-xl border border-slate-800 bg-slate-900/90 p-5 shadow-lg">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h2 class="font-bold text-white text-base flex items-center gap-2">
                <span>⏱️</span>
                <span>Tiempo de ciclo completo</span>
            </h2>
            <span class="text-[11px] font-semibold text-slate-400">completed_at − submitted_at</span>
        </div>
        <p class="mt-2 text-xs text-slate-400">Métrica basada únicamente en solicitudes válidas finalizadas.</p>
        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
            <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 text-center">
                <span class="text-[11px] font-bold text-slate-400 uppercase">Promedio</span>
                <strong class="block text-xl font-extrabold text-white mt-1">{{ $report['cycle']['average_minutes'] ?? '—' }} <span class="text-xs font-normal text-slate-400">min</span></strong>
            </div>
            <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 text-center">
                <span class="text-[11px] font-bold text-slate-400 uppercase">Mediana</span>
                <strong class="block text-xl font-extrabold text-white mt-1">{{ $report['cycle']['median_minutes'] ?? '—' }} <span class="text-xs font-normal text-slate-400">min</span></strong>
            </div>
            <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 text-center">
                <span class="text-[11px] font-bold text-slate-400 uppercase">P75</span>
                <strong class="block text-xl font-extrabold text-white mt-1">{{ $report['cycle']['p75_minutes'] ?? '—' }} <span class="text-xs font-normal text-slate-400">min</span></strong>
            </div>
            <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-3 text-center">
                <span class="text-[11px] font-bold text-slate-400 uppercase">P90</span>
                <strong class="block text-xl font-extrabold text-white mt-1">{{ $report['cycle']['p90_minutes'] ?? '—' }} <span class="text-xs font-normal text-slate-400">min</span></strong>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-slate-800 bg-slate-900/90 p-5 shadow-lg">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h2 class="font-bold text-white text-base flex items-center gap-2">
                <span>🎨</span>
                <span>Distribución por Servicio</span>
            </h2>
            <span class="text-[11px] font-semibold text-slate-400">Total por categoría</span>
        </div>
        <table class="mt-3 w-full text-sm">
            <thead>
                <tr class="text-xs font-bold text-slate-400 uppercase border-b border-slate-800">
                    <th class="py-2 text-left">Servicio</th>
                    <th class="py-2 text-right">Total solicitudes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($report['services'] as $service => $total)
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="py-2.5 font-semibold text-slate-200">
                            {{ match(strtolower($service)) {
                                'design' => '🎨 Diseño Gráfico',
                                'video' => '🎬 Video',
                                'render' => '📦 Render 3D',
                                default => ucfirst($service)
                            } }}
                        </td>
                        <td class="py-2.5 text-right font-extrabold text-white">{{ $total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="py-4 text-center text-sm text-slate-400">No hay registros para este periodo.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="rounded-xl border border-slate-800 bg-slate-900/90 p-5 shadow-lg">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h2 class="font-bold text-white text-base flex items-center gap-2">
                <span>📈</span>
                <span>Tendencia Diaria</span>
            </h2>
            <span class="text-[11px] font-semibold text-slate-400">Volumen histórico</span>
        </div>
        <table class="mt-3 w-full text-sm">
            <caption class="sr-only">Solicitudes enviadas por día</caption>
            <thead>
                <tr class="text-xs font-bold text-slate-400 uppercase border-b border-slate-800">
                    <th class="py-2 text-left">Fecha</th>
                    <th class="py-2 text-right">Solicitudes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($report['trend'] as $point)
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="py-2 text-slate-300 font-medium">{{ $point['label'] }}</td>
                        <td class="py-2 text-right font-bold text-white">{{ $point['value'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="py-4 text-center text-sm text-slate-400">No hay datos en el periodo seleccionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="rounded-xl border border-amber-500/40 bg-amber-950/20 p-5 shadow-lg">
        <div class="flex items-center gap-2 border-b border-amber-500/30 pb-3">
            <span class="text-xl">⚠️</span>
            <h2 class="font-bold text-amber-300 text-base">Calidad de Datos y Auditoría</h2>
        </div>
        <p class="mt-3 text-xs text-amber-200/80 leading-relaxed">
            Las métricas del informe se calculan con datos en tiempo real excluyendo ciclos incompletos o cancelaciones.
        </p>
        <div class="mt-4 rounded-lg border border-amber-500/30 bg-slate-950/60 p-4">
            <ul class="space-y-2 text-sm text-slate-200">
                @foreach($report['quality'] as $key => $value)
                    <li class="flex items-center justify-between py-1 border-b border-slate-800/50 last:border-0">
                        <span class="font-medium text-slate-300">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                        <strong class="font-bold text-amber-400">{{ $value }}</strong>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
</div>
@endsection
