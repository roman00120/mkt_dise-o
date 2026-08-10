@extends('layouts.app')
@section('title', $request->title ?: $request->folio)
@section('header', 'Detalle de solicitud')
@section('content')
@php $data = $request->detail?->data ?? []; $cancelable = auth()->user()->can('cancel', $request); $eventLabels = ['request_submitted' => 'envió la solicitud', 'request_duplicated' => 'creó este borrador a partir de otra solicitud', 'request_cancelled' => 'canceló la solicitud', 'draft_created' => 'creó el borrador', 'draft_updated' => 'actualizó el borrador', 'file_uploaded' => 'adjuntó un archivo', 'file_removed' => 'eliminó un archivo']; @endphp
<div class="mx-auto max-w-6xl space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start"><div><a href="{{ route('app.requests.index') }}" class="text-sm underline">← Mis solicitudes</a><p class="mt-4 font-mono text-xs text-[var(--color-text-tertiary)]">{{ $request->folio }}</p><h1 class="mt-2 text-2xl font-bold">{{ $request->title ?: 'Sin título' }}</h1><p class="mt-2 text-sm text-[var(--color-text-secondary)]">{{ $request->service->label() }} · {{ $request->status->label() }} · {{ $request->requested_priority->label() }}</p></div><div class="flex flex-wrap gap-2">@if($request->isDraft())<a href="{{ route('app.requests.drafts.edit', $request) }}" class="inline-flex min-h-11 items-center rounded-[var(--radius-md)] bg-[var(--color-action-primary)] px-4 text-sm font-semibold">Retomar borrador</a><form method="POST" action="{{ route('app.requests.drafts.destroy', $request) }}" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este borrador?');">@csrf @method('DELETE')<button type="submit" class="inline-flex min-h-11 items-center rounded-[var(--radius-md)] border border-red-500/50 bg-red-600/20 px-4 text-sm font-semibold text-red-200 hover:bg-red-600/40 transition">🗑️ Eliminar borrador</button></form>@endif @if($cancelable)<button type="button" x-data @click="$dispatch('open-cancel')" class="min-h-11 rounded-[var(--radius-md)] border border-red-400/50 px-4 text-sm font-semibold text-red-200">Cancelar</button>@endif</div></div>
    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-labelledby="summary-heading"><h2 id="summary-heading" class="sr-only">Resumen</h2>@foreach([['Servicio', $request->service->label()], ['Tipo de solicitud', $request->other_request_type ?: $request->request_type], ['Solicitante', $request->requester->name], ['Fecha requerida', $request->required_date?->isoFormat('D MMM YYYY') ?? 'No especificado'], ['Salud de fecha', $dateHealth], ['Creada', $request->created_at->isoFormat('D MMM YYYY HH:mm')], ['Enviada', $request->submitted_at?->isoFormat('D MMM YYYY HH:mm') ?? 'No especificado'], ['Última actualización', $request->updated_at->isoFormat('D MMM YYYY HH:mm')]] as [$label, $value])<div class="rounded-[var(--radius-card)] border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)] p-4"><span class="block text-xs text-[var(--color-text-tertiary)]">{{ $label }}</span><strong class="mt-2 block text-sm">{{ $value }}</strong></div>@endforeach</section>
    <div class="grid gap-6 lg:grid-cols-[1.35fr_.65fr]"><div class="space-y-6"><section class="rounded-[var(--radius-card)] border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)] p-5"><h2 class="text-lg font-semibold">Brief</h2><dl class="mt-4 space-y-4">@foreach([['Título', $request->title], ['Descripción', $request->description], ['Objetivo', $request->objective], ['Público objetivo', $request->target_audience], ['Canal o medio', $request->channel], ['Justificación de urgencia', $request->urgency_reason]] as [$label, $value]) @if(filled($value))<div><dt class="text-xs font-semibold text-[var(--color-text-tertiary)]">{{ $label }}</dt><dd class="mt-1 whitespace-pre-line text-sm">{{ $value }}</dd></div>@endif @endforeach</dl></section>
        @if(count(array_filter($data, fn ($value) => filled($value))))<section class="rounded-[var(--radius-card)] border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)] p-5"><h2 class="text-lg font-semibold">Información específica</h2><dl class="mt-4 grid gap-4 sm:grid-cols-2">@foreach($data as $key => $value) @if(filled($value))<div><dt class="text-xs text-[var(--color-text-tertiary)]">{{ str($key)->replace('_', ' ')->title() }}</dt><dd class="mt-1 text-sm">{{ is_array($value) ? implode(', ', $value) : $value }}</dd></div>@endif @endforeach</dl></section>@endif
        <section class="rounded-[var(--radius-card)] border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)] p-5"><h2 class="text-lg font-semibold">Archivos de referencia</h2><div class="mt-4 space-y-3">@forelse($request->files as $file)<div class="flex flex-col justify-between gap-2 border-b border-[var(--color-border-subtle)] pb-3 sm:flex-row"><div><p class="text-sm font-semibold">{{ $file->original_name }}</p><p class="text-xs text-[var(--color-text-tertiary)]">{{ ucfirst($file->category) }} · {{ number_format($file->size / 1024, 1) }} KB · {{ $file->mime_type }} · {{ $file->uploader->name }}</p></div><a class="text-sm font-semibold underline" href="{{ route('app.requests.files.download', [$request, $file]) }}">Descargar</a></div>@empty<p class="text-sm text-[var(--color-text-secondary)]">No hay archivos asociados.</p>@endforelse</div></section>
    </div><aside class="space-y-6"><section class="rounded-[var(--radius-card)] border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)] p-5"><h2 class="text-lg font-semibold">Progreso</h2><ol class="mt-5 space-y-4">@foreach(['draft' => 'Borrador', 'pending' => 'Enviada', 'in_validation' => 'En validación', 'assigned' => 'Asignada', 'in_progress' => 'En proceso', 'marketing_review' => 'En revisión', 'approved' => 'Aprobada', 'completed' => 'Finalizada'] as $key => $label)<li class="flex items-center gap-3 text-sm {{ $request->status->value === $key ? 'font-bold text-white' : 'text-[var(--color-text-tertiary)]' }}"><span class="h-2.5 w-2.5 rounded-full {{ $request->status->value === $key ? 'bg-[var(--color-action-primary)]' : 'bg-[var(--color-border-default)]' }}"></span>{{ $label }}</li>@endforeach</ol></section><section class="rounded-[var(--radius-card)] border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)] p-5"><h2 class="text-lg font-semibold">Historial</h2><ol class="mt-4 space-y-4">@forelse($request->events->sortByDesc('created_at') as $event)<li class="border-l-2 border-[var(--color-action-primary)] pl-3 text-sm"><p>{{ $event->actor?->name ?? 'Sistema' }} {{ $eventLabels[$event->event] ?? 'actualizó la solicitud' }}</p><time class="text-xs text-[var(--color-text-tertiary)]" datetime="{{ $event->created_at?->toIso8601String() }}">{{ $event->created_at?->diffForHumans() }} · {{ $event->created_at?->isoFormat('D MMM YYYY HH:mm') }}</time></li>@empty<li class="text-sm text-[var(--color-text-secondary)]">Aún no hay actividad registrada.</li>@endforelse</ol></section></aside></div>
</div>
@if($cancelable)<div x-data="{ open: false }" @open-cancel.window="open = true" x-show="open" x-cloak @keydown.escape.window="open = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4" role="dialog" aria-modal="true" aria-labelledby="cancel-heading"><div @click.outside="open = false" class="w-full max-w-lg rounded-[var(--radius-card)] border border-[var(--color-border-default)] bg-[var(--color-surface-elevated)] p-6"><h2 id="cancel-heading" class="text-lg font-semibold">Cancelar solicitud</h2><p class="mt-2 text-sm text-[var(--color-text-secondary)]">Esta acción no se puede revertir en esta fase.</p><form method="POST" action="{{ route('app.requests.cancel', $request) }}" class="mt-4 space-y-3">@csrf<label class="block text-sm font-semibold">Motivo<textarea name="reason" required maxlength="1000" rows="4" class="mt-1 w-full rounded-[var(--radius-md)] bg-[var(--color-bg-primary)] p-3"></textarea></label><div class="flex justify-end gap-2"><button type="button" @click="open = false" class="min-h-11 rounded-[var(--radius-md)] border px-4 text-sm">Volver</button><button class="min-h-11 rounded-[var(--radius-md)] bg-red-600 px-4 text-sm font-semibold">Confirmar cancelación</button></div></form></div></div>@endif
@if($request->informationRequests()->where('status', 'open')->exists())<section class="mx-auto mt-6 max-w-6xl rounded border border-amber-500/40 bg-amber-500/10 p-5"><h2 class="text-lg font-semibold">Información solicitada por el equipo creativo</h2>@foreach($request->informationRequests()->where('status', 'open')->latest('requested_at')->get() as $info)<p class="mt-2 whitespace-pre-line text-sm">{{ $info->message }}</p><form method="POST" action="{{ route('app.requests.provide-information', $request) }}" class="mt-4 space-y-3">@csrf<textarea name="response" required maxlength="2000" rows="4" class="w-full rounded bg-slate-950 p-3" placeholder="Escribe la información solicitada"></textarea><button class="min-h-11 rounded bg-red-600 px-4 text-sm font-semibold">Enviar información</button></form>@endforeach</section>@endif
<div class="mx-auto mt-6 max-w-6xl"><x-comment.thread :commentable="$request" :comments="$request->comments" store-route="app.requests.comments.store" reply-route="app.requests.comments.replies.store" /></div>
<section class="mx-auto mt-6 max-w-6xl rounded border border-slate-700 bg-slate-900 p-5">
    <h2 class="text-lg font-semibold">Entregables y Archivos Finales</h2>
    @if ($request->deliverables()->exists())
        @php
            $deliverable = $request->deliverables()->first();
            $version = $deliverable->currentVersion;
        @endphp
        <div class="mt-4 space-y-3">
            @if ($version && $version->files()->exists())
                <p class="text-xs text-slate-400 font-medium">Archivos entregados por el equipo creativo (Versión v{{ $version->version_number }}):</p>
                @foreach ($version->files as $file)
                    <div class="rounded border border-slate-800 bg-slate-950 p-3 space-y-3">
                        <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
                            <div>
                                <p class="text-sm font-semibold text-slate-200">{{ $file->original_name }}</p>
                                <p class="text-xs text-slate-400">{{ number_format($file->size / 1024, 1) }} KB · {{ $file->mime_type }} · Adjuntado por: <strong class="text-slate-300 font-bold">{{ $file->uploader?->name ?? $version->creator?->name ?? 'Ana Carolina Román' }}</strong></p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button"
                                        data-lightbox-trigger
                                        data-preview-url="{{ route('app.requests.deliverables.files.download', [$request, $deliverable, $version, $file]) }}?inline=1"
                                        data-download-url="{{ route('app.requests.deliverables.files.download', [$request, $deliverable, $version, $file]) }}"
                                        data-file-name="{{ $file->original_name }}"
                                        data-file-mime="{{ $file->mime_type }}"
                                        data-file-meta="Versión v{{ $version->version_number }} · {{ number_format($file->size / 1024, 1) }} KB · Subido por {{ $file->uploader?->name ?? 'Creativo' }}"
                                        class="inline-flex items-center gap-1.5 rounded bg-red-600/20 text-red-300 border border-red-500/40 px-3 py-1.5 text-xs font-semibold hover:bg-red-600 hover:text-white transition">
                                    👁️ Previsualizar
                                </button>
                                <a class="inline-flex items-center gap-1.5 rounded bg-emerald-600/20 text-emerald-400 border border-emerald-500/40 px-3 py-1.5 text-xs font-semibold hover:bg-emerald-600/30 transition shrink-0" href="{{ route('app.requests.deliverables.files.download', [$request, $deliverable, $version, $file]) }}" target="_blank">
                                    ⬇️ Descargar archivo
                                </a>
                            </div>
                        </div>
                        @if (str_starts_with($file->mime_type, 'image/'))
                            <div class="overflow-hidden rounded bg-slate-900 border border-slate-800 p-2 text-center cursor-pointer"
                                 data-lightbox-trigger
                                 data-preview-url="{{ route('app.requests.deliverables.files.download', [$request, $deliverable, $version, $file]) }}?inline=1"
                                 data-download-url="{{ route('app.requests.deliverables.files.download', [$request, $deliverable, $version, $file]) }}"
                                 data-file-name="{{ $file->original_name }}"
                                 data-file-mime="{{ $file->mime_type }}"
                                 data-file-meta="Versión v{{ $version->version_number }} · {{ number_format($file->size / 1024, 1) }} KB · Subido por {{ $file->uploader?->name ?? 'Creativo' }}">
                                <img src="{{ route('app.requests.deliverables.files.download', [$request, $deliverable, $version, $file]) }}?inline=1" alt="{{ $file->original_name }}" class="max-h-72 w-auto rounded object-contain mx-auto transition hover:scale-[1.02]">
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif

            @if ($version && $version->status->value === 'marketing_review')
                <div class="mt-5 rounded border border-emerald-500/40 bg-emerald-950/30 p-4">
                    <h3 class="font-semibold text-emerald-300 flex items-center gap-2">
                        <span>🎉 Entregable listo para tu aprobación</span>
                    </h3>
                    <p class="mt-1 text-xs text-slate-300">Revisa los archivos de arriba. Si todo está correcto, haz clic en el botón para finalizar la solicitud.</p>
                    <form method="POST" action="{{ route('app.requests.deliverables.approve', [$request, $deliverable, $version]) }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="confirmed" value="1">
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-500 transition shadow">
                            ✅ Aprobar Entregable y Finalizar Solicitud (Todo Cool)
                        </button>
                    </form>
                </div>
            @elseif (in_array($request->status->value, ['approved', 'completed'], true))
                <div class="mt-4 rounded bg-emerald-950/40 border border-emerald-500/40 p-3 text-xs text-emerald-300 font-semibold flex items-center gap-2">
                    <span>✅ Solicitud Aprobada y Finalizada exitosamente.</span>
                </div>
            @endif
        </div>
        <div class="mt-4">
            <a class="text-xs text-slate-400 underline" href="{{ route('app.requests.deliverables.index', $request) }}">Consultar versiones históricas</a>
        </div>
    @else
        <p class="mt-2 text-sm text-slate-400">El equipo creativo aún no ha enviado ningún entregable.</p>
    @endif
</section>
@endsection
