@extends('layouts.creative')

@section('title', $creativeRequest->folio)
@section('header', 'Detalle operativo')

@section('content')
<div class="mx-auto max-w-7xl space-y-5">
    <a class="text-sm underline" href="{{ route('creative.requests.index') }}">← Solicitudes</a>
    <h1 class="mt-3 text-2xl font-bold">{{ $creativeRequest->title ?: 'Sin título' }}</h1>
    <p class="text-sm text-slate-400">{{ $creativeRequest->folio }} · {{ $creativeRequest->service->label() }} · {{ $creativeRequest->status->label() }}</p>

    @if (auth()->user()->hasRole('admin'))
        <div class="flex justify-end">
            <form method="POST" action="{{ route('admin.requests.destroy', $creativeRequest) }}" onsubmit="return confirm('Seguro que deseas eliminar esta solicitud? Esta accion quedara registrada.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="min-h-11 rounded bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600">Eliminar solicitud</button>
            </form>
        </div>
    @endif

    <section class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @foreach ([['Prioridad solicitada', $creativeRequest->requested_priority->label()], ['Prioridad operativa', $creativeRequest->operational_priority?->label() ?? 'No definida'], ['Fecha solicitada', $creativeRequest->required_date?->isoFormat('D MMM YYYY') ?? 'Sin fecha'], ['Fecha interna', $creativeRequest->internal_due_date?->isoFormat('D MMM YYYY') ?? 'Sin fecha interna'], ['Responsable', $creativeRequest->assignee?->name ?? 'Sin asignar']] as [$label, $value])
            <div class="rounded border border-slate-700 bg-slate-900 p-4"><small class="text-slate-400">{{ $label }}</small><strong class="mt-2 block">{{ $value }}</strong></div>
        @endforeach
    </section>

    <div class="mt-5 grid gap-6 lg:grid-cols-[1.2fr_.8fr]">
        <div class="space-y-5">
            <section class="rounded border border-slate-700 bg-slate-900 p-5">
                <h2 class="text-lg font-semibold">Brief</h2>
                <dl class="mt-4 space-y-3">
                    @foreach ([['Descripción', $creativeRequest->description], ['Objetivo', $creativeRequest->objective], ['Público objetivo', $creativeRequest->target_audience], ['Canal', $creativeRequest->channel]] as [$label, $value])
                        @if (filled($value))
                            <div><dt class="text-xs text-slate-400">{{ $label }}</dt><dd class="mt-1 whitespace-pre-line text-sm">{{ $value }}</dd></div>
                        @endif
                    @endforeach
                </dl>
            </section>

            <section class="rounded border border-slate-700 bg-slate-900 p-5">
                <h2 class="text-lg font-semibold flex items-center justify-between">
                    <span>📁 Archivos de referencia / Adjuntos</span>
                    <span class="rounded bg-slate-800 px-2.5 py-0.5 text-xs text-slate-300 font-mono">{{ $creativeRequest->files->count() }}</span>
                </h2>
                <div class="mt-4 space-y-3">
                    @forelse($creativeRequest->files as $file)
                        <div class="flex flex-col justify-between gap-3 rounded border border-slate-800 bg-slate-950 p-3 sm:flex-row sm:items-center">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold truncate text-slate-200">{{ $file->original_name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ ucfirst($file->category) }} · {{ number_format($file->size / 1024, 1) }} KB · {{ $file->uploader?->name ?? 'Marketing' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button"
                                        data-lightbox-trigger
                                        data-preview-url="{{ route('app.requests.files.download', [$creativeRequest, $file]) }}?inline=1"
                                        data-download-url="{{ route('app.requests.files.download', [$creativeRequest, $file]) }}"
                                        data-file-name="{{ $file->original_name }}"
                                        data-file-mime="{{ $file->mime_type }}"
                                        data-file-meta="{{ ucfirst($file->category) }} · {{ number_format($file->size / 1024, 1) }} KB · {{ $file->uploader?->name ?? 'Marketing' }}"
                                        class="inline-flex items-center gap-1.5 rounded bg-red-600/20 border border-red-500/40 px-3 py-1.5 text-xs font-semibold text-red-300 hover:bg-red-600 hover:text-white transition">
                                    👁️ Previsualizar
                                </button>
                                <a class="inline-flex items-center gap-1.5 rounded bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700 transition shrink-0" href="{{ route('app.requests.files.download', [$creativeRequest, $file]) }}" target="_blank">
                                    ⬇️ Descargar
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No se adjuntaron archivos de referencia en esta solicitud.</p>
                    @endforelse
                </div>
            </section>
            <section class="rounded border border-slate-700 bg-slate-900 p-5">
                <h2 class="text-lg font-semibold">Historial operativo</h2>
                <ol class="mt-4 space-y-3">
                    @forelse ($creativeRequest->events->sortByDesc('created_at') as $event)
                        <li class="border-l-2 border-red-500 pl-3 text-sm">
                            <p>{{ $event->actor?->name ?? 'Sistema' }} · {{ str($event->event)->replace('_', ' ')->title() }}</p>
                            <time class="mt-1 block text-xs text-slate-400" datetime="{{ $event->created_at?->toIso8601String() }}">
                                {{ $event->created_at?->diffForHumans() }} · {{ $event->created_at?->isoFormat('D MMM YYYY HH:mm') }}
                            </time>
                        </li>
                    @empty
                        <li class="text-sm text-slate-400">Sin historial.</li>
                    @endforelse
                </ol>
            </section>
            <x-comment.thread :commentable="$creativeRequest" :comments="$creativeRequest->comments->where('visibility', 'public')" store-route="creative.requests.comments.store" reply-route="creative.requests.comments.replies.store" />
            <x-comment.thread :commentable="$creativeRequest" :comments="$creativeRequest->comments->where('visibility', 'internal')" internal store-route="creative.requests.internal-notes.store" reply-route="creative.requests.comments.replies.store" />
        </div>
        <aside class="space-y-5">
            @if (auth()->user()->hasRole('admin', 'supervisor') && $creativeRequest->status->value === 'in_validation')
                <section class="rounded border border-red-500/40 bg-slate-900 p-5">
                    <h2 class="text-lg font-semibold">Validar y asignar</h2>
                    <p class="mt-2 text-sm text-slate-400">Revisa la solicitud y asígnala a una persona creativa.</p>
                    <form method="POST" action="{{ route('creative.requests.validate', $creativeRequest) }}" class="mt-4 space-y-3">
                        @csrf
                        <label class="block text-sm">Responsable<select name="assignee_id" required class="mt-1 min-h-11 w-full rounded bg-slate-950 p-2">@foreach ($members as $member)<option value="{{ $member->id }}">{{ $member->name }}</option>@endforeach</select></label>
                        <label class="block text-sm">Prioridad operativa<select name="operational_priority" class="mt-1 min-h-11 w-full rounded bg-slate-950 p-2"><option value="">Sin definir</option>@foreach (\App\Enums\RequestPriority::cases() as $priority)<option value="{{ $priority->value }}">{{ $priority->label() }}</option>@endforeach</select></label>
                        <label class="block text-sm">Fecha interna<input type="date" name="internal_due_date" min="{{ today()->toDateString() }}" class="mt-1 min-h-11 w-full rounded bg-slate-950 p-2"></label>
                        <label class="block text-sm">Observación<textarea name="observation" rows="3" maxlength="1000" class="mt-1 w-full rounded bg-slate-950 p-2"></textarea></label>
                        <button class="min-h-11 w-full rounded bg-red-600 px-4 text-sm font-semibold">Aprobar y asignar</button>
                    </form>
                </section>
            @endif
            @if (auth()->user()->hasRole('admin', 'supervisor'))
                <section class="rounded border border-emerald-500/40 bg-slate-900 p-5">
                    <h2 class="text-lg font-semibold text-emerald-400">✅ Aprobación Inicial de Hugo (Admin)</h2>
                    <p class="mt-1 text-xs text-slate-300">Revisa la solicitud para aprobarla y permitir que el equipo creativo (Carolina) trabaje en el diseño final.</p>
                    @if (in_array($creativeRequest->status->value, ['pending', 'in_validation', 'assigned'], true))
                        <form method="POST" action="{{ route('creative.requests.transition', $creativeRequest) }}" class="mt-4 space-y-3">
                            @csrf
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="w-full rounded bg-emerald-600 hover:bg-emerald-500 min-h-11 px-4 text-sm font-semibold text-white transition flex items-center justify-center gap-2">
                                ✅ Aprobar y Continuar a Diseño
                            </button>
                        </form>
                    @else
                        <div class="mt-3 rounded bg-slate-950 p-3 text-xs text-slate-300">
                            Estado actual: <strong class="text-emerald-400">{{ $creativeRequest->status->label() }}</strong>
                        </div>
                    @endif
                </section>
            @endif

            @if (auth()->user()->id === $creativeRequest->assignee_id && !auth()->user()->hasRole('admin', 'supervisor'))
                @if (in_array($creativeRequest->status->value, ['assigned', 'in_progress', 'corrections_requested', 'waiting_for_information', 'in_validation', 'pending'], true))
                    <section class="rounded border border-emerald-500/40 bg-slate-900 p-5">
                        <h2 class="text-lg font-semibold text-emerald-400">⚡ Subir Entregable a Marketing</h2>
                        <p class="mt-1 text-xs text-slate-400">Sube una o varias imágenes / archivos finales y envíalos directamente a revisión en 1 solo clic.</p>
                        <form method="POST" action="{{ route('creative.requests.quick-deliverable', $creativeRequest) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs text-slate-300 font-medium">Imágenes / Archivos finales *</label>
                                <input type="file" name="files[]" multiple required class="mt-1 block w-full text-xs text-slate-400 border border-slate-700 bg-slate-950 rounded p-2">
                                <p class="mt-1 text-[11px] text-slate-400">💡 Puedes seleccionar una o varias imágenes manteniendo presionada la tecla Ctrl / Shift.</p>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-300 font-medium">Notas (opcional)</label>
                                <textarea name="notes" rows="2" placeholder="Ej. Propuestas de diseño final adjuntas..." class="mt-1 block w-full text-xs text-slate-200 border border-slate-700 bg-slate-950 rounded p-2"></textarea>
                            </div>
                            <button type="submit" class="w-full rounded bg-emerald-600 hover:bg-emerald-500 min-h-11 px-4 text-sm font-semibold text-white transition shadow">
                                🚀 Subir y Enviar a Marketing
                            </button>
                        </form>
                    </section>
                @else
                    <section class="rounded border border-blue-500/40 bg-slate-900 p-5">
                        <h2 class="text-lg font-semibold text-blue-400 flex items-center gap-2">
                            <span>✅ Entregable enviado a Marketing</span>
                        </h2>
                        <p class="mt-2 text-xs text-slate-300">
                            Tu entrega ya fue enviada a Marketing. No es posible enviar más archivos a menos que Marketing solicite correcciones.
                        </p>
                        <div class="mt-3 rounded bg-slate-950 p-3 text-xs text-slate-300">
                            Estado actual: <strong class="text-emerald-400">{{ $creativeRequest->status->label() }}</strong>
                        </div>
                    </section>
                @endif
            @endif

            @if ($creativeRequest->deliverables()->exists())
                <section class="rounded border border-slate-700 bg-slate-900 p-5">
                    <h2 class="text-lg font-semibold">Entregables enviados</h2>
                    <p class="mt-2 text-sm text-slate-400">Historial de versiones y archivos entregados.</p>
                    <a class="mt-3 inline-block font-semibold text-emerald-400 underline" href="{{ route('creative.requests.deliverable.show', [$creativeRequest, $creativeRequest->deliverables()->first()]) }}">📁 Ver historial de entregables</a>
                </section>
            @endif
            @if ($creativeRequest->status->value === 'pending')
                <form method="POST" action="{{ route('creative.requests.transition', $creativeRequest) }}">@csrf<input type="hidden" name="status" value="in_validation"><button class="min-h-11 rounded border border-slate-600 px-4">Iniciar validación</button></form>
            @endif
            @if (auth()->user()->hasRole('admin', 'supervisor') && $creativeRequest->status->value === 'approved')
                <form method="POST" action="{{ route('creative.requests.complete', $creativeRequest) }}">@csrf<button class="min-h-11 rounded bg-green-700 px-4 text-sm font-semibold">Marcar como finalizada</button></form>
            @endif
        </aside>
    </div>
</div>
@endsection
