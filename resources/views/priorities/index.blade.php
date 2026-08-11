@extends(auth()->user()->hasRole('marketing') ? 'layouts.app' : 'layouts.creative')

@section('title', 'Prioridades')
@section('header', 'Prioridades')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <header>
        <p class="text-xs font-bold uppercase tracking-[.18em] text-[var(--color-action-primary)]">Orden operativo</p>
        <h1 class="mt-2 text-2xl font-black">Prioridades de solicitudes</h1>
        <p class="mt-2 max-w-2xl text-sm text-[var(--color-text-secondary)]">Carolina puede ordenar las solicitudes para indicar qué debe atenderse primero. El orden es visible para todo el equipo.</p>
    </header>
    @if(! $canManage)<div class="rounded-xl border border-blue-500/30 bg-blue-500/10 p-4 text-sm text-blue-100">Vista informativa: puedes consultar el orden definido, pero solo Carolina, Supervisión y Administración pueden modificarlo.</div>@endif
    <section class="space-y-3" aria-label="Lista de prioridades">
        @forelse($requests as $position => $requestModel)
            @php
                $requestUrl = auth()->user()->hasRole('marketing') ? route('app.requests.show', $requestModel) : route('creative.requests.show', $requestModel);
                $priority = $requestModel->operational_priority?->label() ?? $requestModel->requested_priority?->label() ?? 'Sin prioridad';
                $priorityValue = $requestModel->operational_priority?->value ?? $requestModel->requested_priority?->value ?? 'medium';
            @endphp
            <article class="flex flex-col gap-4 rounded-2xl border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)] p-4 shadow-sm sm:flex-row sm:items-center">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[var(--color-action-soft)] text-lg font-black text-[var(--color-action-primary)]">{{ $position + 1 }}</div>
                @php
                    $isOverdue = $requestModel->required_date?->isBefore(now()->startOfDay()) && ! in_array($requestModel->status->value, ['completed', 'cancelled', 'rejected'], true);
                    $daysUntilDue = $requestModel->required_date ? now()->startOfDay()->diffInDays($requestModel->required_date, false) : null;
                @endphp
                <div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-2 text-xs text-[var(--color-text-tertiary)]"><span class="font-mono font-bold">{{ $requestModel->folio }}</span><span>·</span><span>{{ $requestModel->service?->label() ?? ucfirst($requestModel->service) }}</span><span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase {{ $priorityValue === 'urgent' ? 'bg-red-500/20 text-red-300' : ($priorityValue === 'high' ? 'bg-amber-500/20 text-amber-200' : 'bg-slate-700 text-slate-300') }}">{{ $priority }}</span></div><h2 class="mt-1 truncate font-bold text-white">{{ $requestModel->title ?: 'Solicitud sin título' }}</h2><div class="mt-2 grid gap-x-5 gap-y-1 text-xs text-[var(--color-text-secondary)] sm:grid-cols-2"><span><strong class="text-slate-400">Solicita:</strong> {{ $requestModel->requester?->name ?? '—' }}</span><span><strong class="text-slate-400">Responsable:</strong> {{ $requestModel->assignee?->name ?? 'Sin asignar' }}</span><span><strong class="text-slate-400">Creada:</strong> {{ $requestModel->created_at?->locale('es')->isoFormat('D MMM YYYY, HH:mm') }}</span><span class="{{ $isOverdue ? 'font-bold text-red-300' : ($daysUntilDue !== null && $daysUntilDue <= 2 ? 'font-semibold text-amber-200' : '') }}"><strong class="text-slate-400">Entrega:</strong> {{ $requestModel->required_date?->locale('es')->isoFormat('D MMM YYYY') ?? 'Sin fecha' }} @if($isOverdue) · Vencida @elseif($daysUntilDue !== null && $daysUntilDue >= 0 && $daysUntilDue <= 2) · En {{ $daysUntilDue }} {{ $daysUntilDue === 1 ? 'día' : 'días' }} @endif</span></div><p class="mt-1 text-xs text-[var(--color-text-secondary)]">Estado: {{ ucfirst(str_replace('_', ' ', $requestModel->status->value)) }}</p></div>
                <div class="flex shrink-0 items-center gap-2"><a href="{{ $requestUrl }}" class="min-h-10 rounded-lg border border-[var(--color-border-default)] px-3 py-2 text-xs font-semibold hover:bg-white/10">Ver solicitud</a>@if($canManage)<form method="POST" action="{{ route('priorities.move', $requestModel) }}">@csrf<input type="hidden" name="direction" value="up"><button class="min-h-10 min-w-10 rounded-lg border border-[var(--color-border-default)] px-2 text-lg hover:bg-white/10" aria-label="Subir prioridad" @disabled($position === 0)>↑</button></form><form method="POST" action="{{ route('priorities.move', $requestModel) }}">@csrf<input type="hidden" name="direction" value="down"><button class="min-h-10 min-w-10 rounded-lg border border-[var(--color-border-default)] px-2 text-lg hover:bg-white/10" aria-label="Bajar prioridad" @disabled($position === $requests->count() - 1)>↓</button></form>@endif</div>
            </article>
        @empty<div class="rounded-2xl border border-dashed border-[var(--color-border-default)] p-10 text-center text-sm text-[var(--color-text-secondary)]">No hay solicitudes activas para ordenar.</div>@endforelse
    </section>
</div>
@endsection
