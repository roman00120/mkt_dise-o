@extends('layouts.creative')
@section('title', 'Versión '.$version->version_number)
@section('header', 'Versión de Entregable')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    @if($errors->any())
        <div class="rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-sm font-medium text-red-300" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <a class="text-xs font-bold text-slate-400 hover:text-white transition flex items-center gap-1" href="{{ route('creative.requests.deliverable.show', [$deliverable->request, $deliverable]) }}">
            <span>← Volver al Entregable</span>
        </a>
        <span class="rounded font-mono text-xs font-bold text-red-400 bg-red-500/10 border border-red-500/30 px-2.5 py-1">
            {{ $deliverable->request->folio }}
        </span>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 shadow-xl">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div>
                <span class="rounded bg-indigo-500/20 border border-indigo-500/30 px-2.5 py-1 text-xs font-bold text-indigo-300 uppercase">
                    Versión {{ $version->version_number }}
                </span>
                <h1 class="mt-2 text-2xl font-black text-white">
                    {{ $deliverable->title }}
                </h1>
                <p class="mt-1 text-xs text-slate-400">
                    Estado: <strong class="text-slate-200 uppercase font-bold">{{ $version->status->value }}</strong> · {{ $version->isEditable() ? '🟢 Editable por Creativo' : '🔒 Bloqueada' }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('creative.deliverables.versions.update', [$deliverable, $version]) }}" class="mt-5 space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Notas de Entrega (Públicas para Marketing):</label>
                <textarea name="notes" maxlength="2000" @disabled(!$version->isEditable()) class="w-full rounded-xl border border-slate-700 bg-slate-950 p-3.5 text-xs text-white placeholder-slate-500 focus:border-red-500 focus:outline-none" rows="3">{{ $version->notes }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-amber-300 uppercase mb-1">Notas Internas (Solo Supervisión / Hugo):</label>
                <textarea name="internal_notes" maxlength="2000" @disabled(!$version->isEditable()) class="w-full rounded-xl border border-amber-500/30 bg-slate-950 p-3.5 text-xs text-amber-200 placeholder-slate-500 focus:border-amber-500 focus:outline-none" rows="3">{{ $version->internal_notes }}</textarea>
            </div>

            @if($version->isEditable())
                <div class="text-right">
                    <button type="submit" class="rounded-xl bg-red-600 hover:bg-red-500 px-4 py-2.5 text-xs font-extrabold text-white transition shadow">
                        💾 Guardar Notas de Versión
                    </button>
                </div>
            @endif
        </form>
    </div>

    <!-- Visual Media Gallery & File Manager Section -->
    <section class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>🖼️</span>
                    <span>Archivos y Galería Multimedia de la Versión</span>
                </h2>
                <p class="text-xs text-slate-400">Previsualización de imágenes, videos y descargas adjuntas</p>
            </div>
            <span class="rounded bg-slate-800 px-3 py-1 text-xs font-bold text-slate-300">
                {{ $version->files->count() }} Archivos
            </span>
        </div>

        <!-- Files & Gallery Grid -->
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($version->files as $file)
                @php
                    $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                    $isVideo = in_array($ext, ['mp4', 'webm', 'mov', 'avi']);
                    $downloadUrl = route('creative.deliverables.versions.files.download', [$deliverable, $version, $file]);
                    $inlineUrl = $downloadUrl . '?inline=1';
                @endphp

                <div class="rounded-xl border border-slate-800 bg-slate-950 p-4 shadow-lg flex flex-col justify-between group hover:border-slate-700 transition">
                    <div>
                        <!-- File Category & Badges -->
                        <div class="flex items-center justify-between text-[11px] font-bold mb-3">
                            <span class="rounded bg-slate-800 px-2 py-0.5 text-slate-300 uppercase">
                                {{ $file->category }}
                            </span>
                            @if($file->is_primary)
                                <span class="rounded bg-red-500/20 border border-red-500/40 px-2 py-0.5 text-red-300 uppercase">
                                    ⭐ Principal
                                </span>
                            @endif
                        </div>

                        <!-- Visual Media Preview -->
                        <div class="relative overflow-hidden rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center min-h-[160px] max-h-[220px]">
                            @if($isImage)
                                <img src="{{ $inlineUrl }}" alt="{{ $file->original_name }}" class="w-full h-44 object-cover object-center group-hover:scale-105 transition duration-300 cursor-pointer" onclick="window.open('{{ $inlineUrl }}', '_blank')">
                            @elseif($isVideo)
                                <video src="{{ $inlineUrl }}" controls class="w-full h-44 object-cover bg-black"></video>
                            @else
                                <div class="flex flex-col items-center justify-center p-6 text-center">
                                    <span class="text-4xl">📄</span>
                                    <span class="mt-2 text-xs font-mono font-bold text-slate-400 uppercase">.{{ $ext }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- File Information -->
                        <div class="mt-3 space-y-0.5">
                            <p class="text-xs font-bold text-white truncate" title="{{ $file->original_name }}">
                                {{ $file->original_name }}
                            </p>
                            <p class="text-[11px] text-slate-400 font-medium">
                                Adjuntado por: <strong class="text-slate-200">{{ $file->uploader?->name ?? $version->creator?->name ?? 'Ana Carolina Román' }}</strong>
                            </p>
                        </div>
                    </div>

                    <!-- Action Controls -->
                    <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between">
                        <a href="{{ $downloadUrl }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-400 hover:text-emerald-300 transition">
                            <span>📥 Descargar</span>
                        </a>

                        @if($isImage)
                            <a href="{{ $inlineUrl }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-400 hover:text-indigo-300 transition">
                                <span>🔍 Ver grande</span>
                            </a>
                        @endif

                        @if($version->isEditable())
                            <form method="POST" action="{{ route('creative.deliverables.versions.files.destroy', [$deliverable, $version, $file]) }}" onsubmit="return confirm('¿Eliminar este archivo de la entrega?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-red-400 hover:text-red-300 transition">
                                    🗑️ Eliminar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="sm:col-span-2 lg:col-span-3 rounded-xl border border-dashed border-slate-800 p-8 text-center">
                    <span class="text-3xl">📁</span>
                    <p class="mt-2 text-sm font-bold text-white">No hay archivos subidos en esta versión</p>
                    <p class="text-xs text-slate-400 mt-1">Utiliza el formulario a continuación para adjuntar imágenes, renders o diseños.</p>
                </div>
            @endforelse
        </div>

        <!-- File Upload Form (When Editable) -->
        @if($version->isEditable())
            <div class="mt-8 pt-6 border-t border-slate-800">
                <h3 class="text-sm font-extrabold text-white mb-3 flex items-center gap-2">
                    <span>📤</span>
                    <span>Subir Nuevo Archivo a la Versión {{ $version->version_number }}</span>
                </h3>
                <form method="POST" enctype="multipart/form-data" action="{{ route('creative.deliverables.versions.files.store', [$deliverable, $version]) }}" class="grid gap-4 sm:grid-cols-4 items-end rounded-xl bg-slate-950 p-4 border border-slate-800">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Seleccionar Archivo (Imagen, Video, Documento):</label>
                        <input required type="file" name="file" class="w-full text-xs text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-red-600 file:text-white hover:file:bg-red-500 cursor-pointer">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Categoría:</label>
                        <select name="category" class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-xs font-semibold text-white focus:border-red-500 focus:outline-none">
                            <option value="preview">Preview (Vista Previa)</option>
                            <option value="final">Final (Entrega Aprobada)</option>
                            <option value="source">Source (Editable PSD/AI/Blend)</option>
                            <option value="supporting">Supporting (Recursos/Anexos)</option>
                        </select>
                    </div>

                    <div>
                        <button type="submit" class="w-full rounded-lg bg-red-600 hover:bg-red-500 px-4 py-2.5 text-xs font-extrabold text-white transition shadow">
                            Subir Archivo
                        </button>
                    </div>

                    <div class="sm:col-span-4 mt-1">
                        <label class="inline-flex items-center gap-2 text-xs text-slate-300 font-medium">
                            <input type="checkbox" name="is_primary" value="1" class="rounded bg-slate-900 border-slate-700 text-red-600 focus:ring-0">
                            <span>Marcar como archivo principal de previsualización</span>
                        </label>
                    </div>
                </form>
            </div>
        @endif
    </section>

    <!-- Version Status Submission Actions -->
    @if($version->isEditable())
        <form method="POST" action="{{ route('creative.deliverables.versions.submit-marketing', [$deliverable, $version]) }}">
            @csrf
            <button class="w-full rounded-xl bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 py-3 text-sm font-extrabold text-white transition shadow-lg">
                🚀 Enviar Entregable Final a Marketing
            </button>
        </form>
    @endif

    @if(auth()->user()->hasRole('admin', 'supervisor') && $version->status->value === 'internal_review')
        <section class="rounded-2xl border border-amber-500/40 bg-slate-900 p-6 shadow-xl">
            <h2 class="text-lg font-bold text-amber-300 flex items-center gap-2">
                <span>👑</span>
                <span>Revisión Interna de Hugo (Administración)</span>
            </h2>
            <p class="mt-1 text-xs text-slate-400">Aprueba el diseño final para enviarlo a Marketing o solicita cambios a Ana.</p>
            <div class="mt-4 flex flex-wrap gap-3">
                <form method="POST" action="{{ route('creative.deliverables.versions.internal-approve', [$deliverable, $version]) }}">
                    @csrf
                    <button class="rounded-xl bg-emerald-600 hover:bg-emerald-500 px-5 py-2.5 text-xs font-extrabold text-white transition shadow">
                        ✅ Aprobar Revisión Interna
                    </button>
                </form>

                <form method="POST" action="{{ route('creative.deliverables.versions.internal-changes', [$deliverable, $version]) }}" class="flex gap-2 flex-1">
                    @csrf
                    <input name="reason" required maxlength="1000" placeholder="Motivo o correcciones requeridas..." class="flex-1 rounded-xl border border-slate-700 bg-slate-950 px-3.5 text-xs text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none">
                    <button class="rounded-xl border border-amber-500/50 bg-amber-500/10 hover:bg-amber-500/20 px-4 py-2.5 text-xs font-bold text-amber-300 transition">
                        ✏️ Solicitar Corrección
                    </button>
                </form>
            </div>
        </section>
    @endif

    @if(auth()->user()->hasRole('admin', 'supervisor') && $version->status->value === 'ready_for_marketing')
        <form method="POST" action="{{ route('creative.deliverables.versions.send-marketing', [$deliverable, $version]) }}">
            @csrf
            <button class="w-full rounded-xl bg-red-600 hover:bg-red-500 py-3 text-sm font-extrabold text-white transition shadow-lg">
                📤 Enviar Oficialmente a Marketing
            </button>
        </form>
    @endif
</div>
@endsection
