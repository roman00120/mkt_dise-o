@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard de '.match(auth()->user()->role->value) {
    'admin' => 'Administración',
    'supervisor' => 'Supervisión',
    default => 'Marketing',
})

@section('content')
@php
    $greeting = now()->hour < 12 ? 'Buenos días' : (now()->hour < 19 ? 'Buenas tardes' : 'Buenas noches');
@endphp
<div class="mx-auto max-w-[1600px] space-y-8">
    <header class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div>
            <p class="ds-kicker">Área de {{ auth()->user()->role->value === 'marketing' ? 'Marketing' : 'trabajo' }}</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">{{ $greeting }}, {{ auth()->user()->name }}</h1>
            <p class="mt-2 text-sm text-[var(--color-text-secondary)]">Aquí tienes un resumen de tus solicitudes creativas.</p>
        </div>
        @if(auth()->user()->hasRole('marketing'))
            <a href="{{ route('app.requests.create') }}" class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-[var(--radius-md)] bg-[var(--color-action-primary)] px-5 text-sm font-semibold text-white hover:bg-[var(--color-action-primary-hover)] sm:w-auto"><i data-lucide="plus" class="h-5 w-5" aria-hidden="true"></i>Nueva solicitud</a>
        @endif
    </header>

    <x-dashboard.section title="Resumen" description="Una vista rápida del trabajo activo.">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($metrics as $metric)
                <x-dashboard.metric-card :metric="$metric" />
            @endforeach
        </div>
    </x-dashboard.section>

    <x-dashboard.section title="Servicios creativos" description="Servicios disponibles para iniciar una solicitud.">
        <div class="grid gap-4 md:grid-cols-3">
            @forelse($serviceCards as $service)
                <x-dashboard.service-card :service="$service" />
            @empty
                <x-ui.empty-state title="No hay servicios disponibles" description="El catálogo no tiene servicios activos en este momento." icon="layers-3" />
            @endforelse
        </div>
    </x-dashboard.section>

    <div class="grid gap-8 xl:grid-cols-[1.25fr_.75fr]">
        <x-dashboard.section title="Requieren tu atención" description="Prioriza las solicitudes con una acción pendiente.">
            <div class="space-y-3">
                @forelse($attentionItems as $item)
                    <x-dashboard.attention-item :item="$item" />
                @empty
                    <x-ui.empty-state title="No tienes solicitudes que requieran atención." description="Te avisaremos cuando haya algo que revisar." icon="check-circle" />
                @endforelse
            </div>
        </x-dashboard.section>
        <x-dashboard.section title="Pendientes de revisión" description="Entregables listos para tu aprobación.">
            <div class="space-y-3">
                @forelse($pendingDeliverables as $deliverable)
                    <x-deliverable.review-card :deliverable="$deliverable" />
                @empty
                    <x-ui.empty-state title="No tienes entregables pendientes de revisión." description="Los entregables enviados a revisión aparecerán aquí." icon="check-circle" />
                @endforelse
            </div>
        </x-dashboard.section>
    </div>

    <x-dashboard.section title="Solicitudes recientes" description="Consulta el avance de tus solicitudes." :action="null">
        <div class="mb-4 flex gap-2 overflow-x-auto pb-1" aria-label="Filtros de solicitudes">
            @foreach(['all' => 'Todas', 'pending' => 'Pendientes', 'in-progress' => 'En proceso', 'review' => 'En revisión', 'completed' => 'Finalizadas'] as $key => $label)
                <a href="{{ route('app.dashboard', ['filter' => $key]) }}" @class(['inline-flex min-h-10 shrink-0 items-center rounded-[var(--radius-pill)] border px-3 text-xs font-semibold', 'border-[var(--color-action-primary)] bg-[var(--color-action-soft)] text-white' => $filter === $key, 'border-[var(--color-border-default)] text-[var(--color-text-secondary)] hover:bg-[var(--color-surface-interactive)]' => $filter !== $key])>{{ $label }}</a>
            @endforeach
        </div>
        <div class="overflow-hidden rounded-[var(--radius-card)] border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)]">
            <div class="hidden grid-cols-[1.2fr_1fr_1fr_1fr_auto] gap-3 border-b border-[var(--color-border-subtle)] px-3 py-3 text-[11px] font-bold uppercase tracking-wider text-[var(--color-text-tertiary)] lg:grid"><span>Solicitud</span><span>Servicio</span><span>Estado</span><span>Fecha requerida</span><span>Acción</span></div>
            @forelse($recentRequests as $request)
                <x-request.list-item :request="$request" />
            @empty
                <x-ui.empty-state title="Aún no tienes solicitudes" description="Cuando crees o recibas una solicitud aparecerá aquí." icon="inbox">
                    @if(auth()->user()->hasRole('marketing'))<a href="{{ route('app.requests.create') }}" class="inline-flex min-h-11 items-center gap-2 rounded-[var(--radius-md)] bg-[var(--color-action-primary)] px-4 text-sm font-semibold">Crear solicitud</a>@endif
                </x-ui.empty-state>
            @endforelse
        </div>
    </x-dashboard.section>

    <div class="grid gap-8 lg:grid-cols-[1fr_1fr]">
        <x-dashboard.section title="Actividad reciente">
            <x-ui.card>
                @forelse($recentActivity as $activity)<x-dashboard.activity-item :activity="$activity" />@empty<x-ui.empty-state title="Todavía no hay actividad" description="Las actualizaciones de tus solicitudes aparecerán aquí." icon="activity" />@endforelse
            </x-ui.card>
        </x-dashboard.section>
        <x-dashboard.section title="Accesos rápidos">
            <div class="grid gap-3 sm:grid-cols-2">
                @if(auth()->user()->hasRole('marketing'))<x-dashboard.quick-action :href="route('app.requests.create')" icon="plus" title="Nueva solicitud" description="Inicia un brief creativo" />@endif
                <x-dashboard.quick-action :href="route('app.requests.index')" icon="inbox" title="Ver mis solicitudes" description="Consulta tus solicitudes" />
                <x-dashboard.quick-action :href="route('app.notifications')" icon="bell" title="Notificaciones" description="Revisa tus avisos" />
            </div>
        </x-dashboard.section>
    </div>
</div>
@endsection
