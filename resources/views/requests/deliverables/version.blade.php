@extends('layouts.app')
@section('title', 'Versión '.$version->version_number)
@section('header', 'Versión de Entregable')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex items-center justify-between">
        <a class="text-xs font-bold text-slate-400 hover:text-white transition flex items-center gap-1" href="{{ route('app.requests.deliverables.show', [$creativeRequest, $deliverable]) }}">
            <span>← Volver al Entregable</span>
        </a>
        <span class="rounded font-mono text-xs font-bold text-red-400 bg-red-500/10 border border-red-500/30 px-2.5 py-1">
            {{ $creativeRequest->folio }}
        </span>
    </div>

    <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 shadow-xl">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div>
                <span class="rounded bg-emerald-500/20 border border-emerald-500/30 px-2.5 py-1 text-xs font-bold text-emerald-300 uppercase">
                    Versión {{ $version->version_number }}
                </span>
                <h1 class="mt-2 text-2xl font-black text-white">
                    {{ $deliverable->title }}
                </h1>
                <p class="mt-1 text-xs text-slate-400">
                    Estado: <strong class="text-slate-200 uppercase font-bold">{{ match($version->status->value) { 'approved' => '✅ Aprobada', 'marketing_changes_requested' => '✏️ Correcciones Solicitadas', default => ucfirst($version->status->value) } }}</strong>
                </p>
            </div>
        </div>

        @if($version->notes)
            <div class="mt-4 rounded-xl border border-slate-800 bg-slate-950 p-4">
                <p class="text-xs font-bold text-slate-400 uppercase">Notas de la Entrega:</p>
                <p class="mt-1 text-sm text-slate-200 leading-relaxed">{{ $version->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Visual Media Gallery Grid -->
    <section class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>🖼️</span>
                    <span>Archivos de la Entrega</span>
                </h2>
                <p class="text-xs text-slate-400">Previsualización de imágenes, renders y resultados</p>
            </div>
            <span class="rounded bg-slate-800 px-3 py-1 text-xs font-bold text-slate-300">
                {{ $version->files->count() }} Archivos
            </span>
        </div>

        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($version->files as $file)
                @php
                    $ext = strtolower(pathinfo($file->original_name, PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                    $isVideo = in_array($ext, ['mp4', 'webm', 'mov', 'avi']);
                    $downloadUrl = route('app.requests.deliverables.files.download', [$creativeRequest, $deliverable, $version, $file]);
                    $inlineUrl = $downloadUrl . '?inline=1';
                @endphp

                <div class="rounded-xl border border-slate-800 bg-slate-950 p-4 shadow-lg flex flex-col justify-between group hover:border-slate-700 transition">
                    <div>
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

                        <!-- Visual Preview -->
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

                        <div class="mt-3 space-y-0.5">
                            <p class="text-xs font-bold text-white truncate" title="{{ $file->original_name }}">
                                {{ $file->original_name }}
                            </p>
                            <p class="text-[11px] text-slate-400 font-medium">
                                Adjuntado por: <strong class="text-slate-200">{{ $file->uploader?->name ?? $version->creator?->name ?? 'Ana Carolina Román' }}</strong>
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between">
                        <a href="{{ $downloadUrl }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-400 hover:text-emerald-300 transition">
                            <span>📥 Descargar</span>
                        </a>

                        @if($isImage)
                            <a href="{{ $inlineUrl }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-400 hover:text-indigo-300 transition">
                                <span>🔍 Ver grande</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="sm:col-span-2 lg:col-span-3 rounded-xl border border-dashed border-slate-800 p-8 text-center">
                    <span class="text-3xl">📁</span>
                    <p class="mt-2 text-sm font-bold text-white">No hay archivos adjuntos en esta versión</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
