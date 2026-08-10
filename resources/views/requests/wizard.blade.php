@extends('layouts.app')
@section('title', 'Nueva solicitud')
@section('header', 'Nueva solicitud')
@section('content')
@php($isDraft = (bool) $requestModel)
@php($details = $requestModel?->detail?->data ?? [])
@php($currentService = $service ?? $requestModel?->service?->value)
@php($value = fn (string $key, mixed $fallback = '') => old($key, $requestModel?->{$key} ?? $fallback))
<div class="mx-auto max-w-5xl space-y-6" x-data="{ step: {{ $step }}, saving: false }">
    <div class="flex flex-wrap items-end justify-between gap-4"><div><p class="ds-kicker">Solicitud creativa</p><h1 class="mt-2 text-2xl font-bold">{{ $step === 7 ? 'Solicitud enviada' : 'Crea una nueva solicitud' }}</h1><p class="mt-2 text-sm text-[var(--color-text-secondary)]">Completa los pasos con la información disponible. Puedes guardar un borrador en cualquier momento.</p></div>@if($isDraft)<span class="text-xs text-[var(--color-text-tertiary)]">Folio {{ $requestModel->folio }} · Paso {{ $step }} de 6</span>@endif</div>
    <x-wizard.stepper :steps="['Servicio', 'Tipo', 'Brief', 'Archivos', 'Fecha y prioridad', 'Revisión']" :current="$step - 1" />
    @if(session('status'))<x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>@endif
    @if($isDraft)<p class="js-autosave-status text-right text-xs text-[var(--color-text-tertiary)]" role="status">Cambios guardados</p>@endif
    @if($isDraft && $requestModel->last_autosaved_at)<p class="text-right text-xs text-[var(--color-text-tertiary)]" role="status">Cambios guardados {{ $requestModel->last_autosaved_at->locale('es')->diffForHumans() }}</p>@endif
    @if($errors->any())<x-ui.alert variant="error" title="Revisa la información">{{ $errors->first() }}</x-ui.alert>@endif

    @if($step === 1)
        <x-ui.card><form method="POST" action="{{ $isDraft ? route('app.requests.drafts.update', $requestModel) : route('app.requests.store') }}">@csrf
@if($isDraft)
@method('PATCH')
@endif<input type="hidden" name="step" value="1"><fieldset><legend class="text-xl font-semibold">¿Qué tipo de apoyo creativo necesitas?</legend><p class="mt-2 text-sm text-[var(--color-text-secondary)]">Elige uno de los tres servicios disponibles.</p><div class="mt-6 grid gap-4 md:grid-cols-3">@foreach($services as $item)<label class="group cursor-pointer rounded-[var(--radius-card)] border p-5 transition hover:border-[var(--color-border-default)] has-[:checked]:border-[var(--color-action-primary)] has-[:checked]:bg-[var(--color-action-soft)]"><input class="sr-only" type="radio" name="service" value="{{ $item['value'] }}" @checked($currentService === $item['value'])><span class="inline-flex h-11 w-11 items-center justify-center rounded-[var(--radius-md)] bg-[var(--color-surface-interactive)]"><i data-lucide="{{ $item['icon'] }}" class="h-5 w-5" aria-hidden="true"></i></span><span class="mt-5 block font-semibold">{{ $item['label'] }}</span><span class="mt-2 block text-sm leading-6 text-[var(--color-text-secondary)]">{{ $item['description'] }}</span></label>@endforeach</div></fieldset><div class="mt-8 flex justify-end"><x-ui.button type="submit" size="lg">Continuar <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i></x-ui.button></div></form></x-ui.card>
    @elseif($step === 2)
        <x-ui.card>
            <form method="POST" action="{{ $isDraft ? route('app.requests.drafts.update', $requestModel) : route('app.requests.store') }}">
                @csrf
                @if($isDraft)
                    @method('PATCH')
                @endif
                <input type="hidden" name="step" value="2">
                <input type="hidden" name="service" value="{{ $currentService }}">
                <fieldset x-data="{ selectedType: '{{ old('request_type', $requestModel?->request_type ?? '') }}' }">
                    <legend class="text-xl font-bold text-white flex items-center gap-2">
                        <span>🎨</span>
                        <span>Selecciona el tipo de solicitud de {{ collect($services)->firstWhere('value', $currentService)['label'] ?? $currentService }}</span>
                    </legend>
                    <p class="mt-2 text-sm text-slate-400">Elige la opción que mejor describa la pieza gráfica o formato que necesitas.</p>

                    @if($currentService === 'design')
                        <!-- Sección 1: Piezas Digitales -->
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-blue-400">
                                    <span>📱</span>
                                    <span>Piezas Digitales</span>
                                </div>
                                <span class="rounded-full bg-blue-500/10 border border-blue-500/20 px-2.5 py-0.5 text-[10px] font-bold text-blue-300">
                                    Redes Sociales, Web y Pantallas
                                </span>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($catalog as $key => $label)
                                    @if(in_array($key, ['flyer_rrss', 'rrss_cover', 'carousel', 'infographic_product', 'infographic_installation', 'infographic_other', 'invitation_ccad', 'invitation_cert', 'image_editing', 'seminar', 'presentation', 'tech_sheet', 'distributor_brochure', 'distributor_rrss', 'distributor_catalog', 'digital_stationery']))
                                        <label class="flex min-h-14 cursor-pointer items-center gap-3 rounded-xl border border-slate-800 bg-slate-950/70 p-4 transition hover:border-slate-600 has-[:checked]:border-red-500 has-[:checked]:bg-red-500/10 shadow">
                                            <input type="radio" name="request_type" value="{{ $key }}" x-model="selectedType" @checked($requestModel?->request_type === $key) class="text-red-600 focus:ring-red-500">
                                            <span class="text-xs sm:text-sm font-bold text-white leading-snug">{{ $label }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Sub-selection Box for Flyer RRSS Sizes -->
                        <div class="mt-6 rounded-2xl border border-slate-800 bg-slate-950 p-5 space-y-4 shadow-xl" x-show="selectedType === 'flyer_rrss'" x-transition x-data="{ customFlyerSize: {{ (!empty($details['flyer_custom_size']) || (is_array($details['flyer_sizes'] ?? null) && in_array('Otro (Medida personalizada)', $details['flyer_sizes']))) ? 'true' : 'false' }} }">
                            <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-blue-400">
                                <span>📌 Selecciona las medidas / formatos que necesitas:</span>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                <label class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900 p-3.5 cursor-pointer hover:border-slate-600 transition">
                                    <input type="checkbox" name="details[flyer_sizes][]" value="Box 1080 x 1080 px" @checked(in_array('Box 1080 x 1080 px', (array)($details['flyer_sizes'] ?? ['Box 1080 x 1080 px']))) class="rounded border-slate-700 text-red-600 focus:ring-red-500">
                                    <div>
                                        <p class="text-xs font-bold text-white">Flyer RRSS Box</p>
                                        <p class="text-[11px] text-slate-400 font-mono">1080 x 1080 px | JPG</p>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900 p-3.5 cursor-pointer hover:border-slate-600 transition">
                                    <input type="checkbox" name="details[flyer_sizes][]" value="Storie 1080 x 1920 px" @checked(in_array('Storie 1080 x 1920 px', (array)($details['flyer_sizes'] ?? []))) class="rounded border-slate-700 text-red-600 focus:ring-red-500">
                                    <div>
                                        <p class="text-xs font-bold text-white">Flyer RRSS Storie</p>
                                        <p class="text-[11px] text-slate-400 font-mono">1080 x 1920 px | JPG</p>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900 p-3.5 cursor-pointer hover:border-slate-600 transition">
                                    <input type="checkbox" name="details[flyer_sizes][]" value="Portrait 1080 x 1350 px" @checked(in_array('Portrait 1080 x 1350 px', (array)($details['flyer_sizes'] ?? []))) class="rounded border-slate-700 text-red-600 focus:ring-red-500">
                                    <div>
                                        <p class="text-xs font-bold text-white">Flyer RRSS Portrait</p>
                                        <p class="text-[11px] text-slate-400 font-mono">1080 x 1350 px | JPG</p>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900 p-3.5 cursor-pointer hover:border-slate-600 transition">
                                    <input type="checkbox" x-model="customFlyerSize" name="details[flyer_sizes][]" value="Otro (Medida personalizada)" @checked(in_array('Otro (Medida personalizada)', (array)($details['flyer_sizes'] ?? [])) || !empty($details['flyer_custom_size'])) class="rounded border-slate-700 text-red-600 focus:ring-red-500">
                                    <div>
                                        <p class="text-xs font-bold text-white">Otro formato</p>
                                        <p class="text-[11px] text-slate-400">Medida personalizada</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Manual Custom Dimensions Input -->
                            <div class="mt-3 pt-3 border-t border-slate-800/80" x-show="customFlyerSize" x-transition>
                                <label class="block text-xs font-bold text-slate-300" for="flyer_custom_size">
                                    📐 Especificar medida personalizada manualmente
                                </label>
                                <input id="flyer_custom_size" name="details[flyer_custom_size]" value="{{ $details['flyer_custom_size'] ?? '' }}" maxlength="120" placeholder="Ej. 1200 x 628 px, 600 x 600 px, etc." class="mt-1.5 min-h-11 w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 text-sm font-semibold text-white placeholder-slate-500 focus:border-red-500 focus:outline-none">
                            </div>
                        </div>

                        <!-- Sección 2: Piezas Impresas -->
                        <div class="mt-8 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-emerald-400">
                                    <span>🖨️</span>
                                    <span>Piezas Impresas</span>
                                </div>
                                <span class="rounded-full bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 text-[10px] font-bold text-emerald-300">
                                    Lonas, Tarjetas, Stands y Papelería
                                </span>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($catalog as $key => $label)
                                    @if(in_array($key, ['tarp_spider', 'tarp_large_format', 'tarp_banner', 'vinyl', 'product_brochure', 'flyers_print', 'expo_stand', 'warehouse_labels', 'product_labels', 'silkscreen', 'business_card_paper', 'business_card_pvc', 'badges_pvc', 'letterhead_legal', 'letterhead_letter']))
                                        <label class="flex min-h-14 cursor-pointer items-center gap-3 rounded-xl border border-slate-800 bg-slate-950/70 p-4 transition hover:border-slate-600 has-[:checked]:border-red-500 has-[:checked]:bg-red-500/10 shadow">
                                            <input type="radio" name="request_type" value="{{ $key }}" x-model="selectedType" @checked($requestModel?->request_type === $key) class="text-red-600 focus:ring-red-500">
                                            <span class="text-xs sm:text-sm font-bold text-white leading-snug">{{ $label }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Sección 3: Personalizado / Otro -->
                        <div class="mt-8 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-amber-400">
                                    <span>✏️</span>
                                    <span>Personalizado / Otro</span>
                                </div>
                                <span class="rounded-full bg-amber-500/10 border border-amber-500/20 px-2.5 py-0.5 text-[10px] font-bold text-amber-300">
                                    Especificación Libre
                                </span>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($catalog as $key => $label)
                                    @if($key === 'other')
                                        <label class="flex min-h-14 cursor-pointer items-center gap-3 rounded-xl border border-slate-800 bg-slate-950/70 p-4 transition hover:border-slate-600 has-[:checked]:border-red-500 has-[:checked]:bg-red-500/10 shadow">
                                            <input type="radio" name="request_type" value="{{ $key }}" x-model="selectedType" @checked($requestModel?->request_type === $key) class="text-red-600 focus:ring-red-500">
                                            <span class="text-xs sm:text-sm font-bold text-white leading-snug">{{ $label }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- Otros servicios (Video, Render) -->
                        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($catalog as $key => $label)
                                <label class="flex min-h-14 cursor-pointer items-center gap-3 rounded-xl border border-slate-800 bg-slate-950/70 p-4 transition hover:border-slate-600 has-[:checked]:border-red-500 has-[:checked]:bg-red-500/10 shadow">
                                    <input type="radio" name="request_type" value="{{ $key }}" x-model="selectedType" @checked($requestModel?->request_type === $key) class="text-red-600 focus:ring-red-500">
                                    <span class="text-xs sm:text-sm font-bold text-white leading-snug">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <!-- Custom Specifications Box for 'Otro' -->
                    <div class="mt-6 rounded-2xl border border-slate-800 bg-slate-950 p-5 space-y-4 shadow-xl" x-show="selectedType === 'other'" x-transition>
                        <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-red-400">
                            <span>✏️ Especificaciones de Pieza (Otro)</span>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-300" for="other_request_type">
                                    Tipo de diseño / Pieza
                                </label>
                                <input id="other_request_type" name="other_request_type" value="{{ $value('other_request_type') }}" maxlength="120" placeholder="Ej. Lona exterior, Banner web" class="mt-2 min-h-11 w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 text-sm font-semibold text-white placeholder-slate-500 focus:border-red-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-300" for="detail_dimensions">
                                    Especificar sus medidas
                                </label>
                                <input id="detail_dimensions" name="details[dimensions]" value="{{ $details['dimensions'] ?? '' }}" maxlength="120" placeholder="Ej. 1080x1080 px, 2x1 mts" class="mt-2 min-h-11 w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 text-sm font-semibold text-white placeholder-slate-500 focus:border-red-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-300" for="detail_file_format">
                                    Formato (JPG, PNG, PDF, etc.)
                                </label>
                                <input id="detail_file_format" name="details[file_format]" value="{{ $details['file_format'] ?? '' }}" maxlength="60" placeholder="Ej. JPG, PNG transparente" class="mt-2 min-h-11 w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 text-sm font-semibold text-white placeholder-slate-500 focus:border-red-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="mt-8 flex justify-between gap-3 pt-4 border-t border-slate-800">
                    <a href="{{ $isDraft ? route('app.requests.drafts.edit', [$requestModel, 'step' => 1]) : route('app.requests.create', ['service' => $currentService]) }}" class="inline-flex min-h-11 items-center px-4 rounded-xl border border-slate-700 bg-slate-800 text-xs font-bold text-slate-300 hover:bg-slate-700 transition">← Atrás</a>
                    <x-ui.button type="submit" size="lg">Continuar <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i></x-ui.button>
                </div>
            </form>
        </x-ui.card>
    @elseif($step === 3)
        <x-ui.card><form method="POST" action="{{ route('app.requests.drafts.update', $requestModel) }}">@csrf @method('PATCH')<input type="hidden" name="step" value="3"><input type="hidden" name="service" value="{{ $currentService }}"><input type="hidden" name="request_type" value="{{ $requestModel->request_type }}"><h2 class="text-xl font-semibold">Cuéntanos qué necesitas</h2><p class="mt-2 text-sm text-[var(--color-text-secondary)]">Describe el contexto para que el equipo creativo pueda ayudarte.</p><div class="mt-6 grid gap-5"><div><label for="title" class="block text-sm font-semibold">Título de la solicitud</label><input id="title" name="title" maxlength="120" required value="{{ $value('title') }}" class="mt-2 min-h-11 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3 text-sm"><p class="mt-1 text-right text-xs text-[var(--color-text-tertiary)]">Máximo 120 caracteres</p></div><div><label for="description" class="block text-sm font-semibold">Descripción</label><textarea id="description" name="description" maxlength="2000" required rows="5" class="mt-2 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] p-3 text-sm">{{ $value('description') }}</textarea></div><div class="grid gap-5 md:grid-cols-2"><div><label for="objective" class="block text-sm font-semibold">Objetivo <span class="font-normal text-[var(--color-text-tertiary)]">(opcional)</span></label><textarea id="objective" name="objective" maxlength="1000" rows="3" class="mt-2 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] p-3 text-sm">{{ $value('objective') }}</textarea></div><div><label for="target_audience" class="block text-sm font-semibold">Público objetivo</label><textarea id="target_audience" name="target_audience" maxlength="500" rows="3" class="mt-2 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] p-3 text-sm">{{ $value('target_audience') }}</textarea></div></div><div><label for="channel" class="block text-sm font-semibold">Canal o medio</label><input id="channel" name="channel" maxlength="120" value="{{ $value('channel') }}" class="mt-2 min-h-11 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3 text-sm"></div><div class="border-t border-[var(--color-border-subtle)] pt-5"><h3 class="font-semibold">Información de {{ collect($services)->firstWhere('value', $currentService)['label'] ?? 'servicio' }}</h3><div class="mt-4 grid gap-5 md:grid-cols-2">@if($currentService === 'design')<div><label class="block text-sm font-semibold">Tipo de pieza</label><input name="details[piece_type]" required value="{{ $details['piece_type'] ?? '' }}" class="mt-2 min-h-11 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3 text-sm"></div><div><label class="block text-sm font-semibold">Número de propuestas</label><input type="number" min="1" max="5" name="details[proposals]" value="{{ $details['proposals'] ?? '' }}" class="mt-2 min-h-11 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3 text-sm"></div>@elseif($currentService === 'video')<div><label class="block text-sm font-semibold">Tipo de video</label><input name="details[video_type]" required value="{{ $details['video_type'] ?? '' }}" class="mt-2 min-h-11 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3 text-sm"></div><div><label class="block text-sm font-semibold">Duración estimada</label><input name="details[duration]" required value="{{ $details['duration'] ?? '' }}" placeholder="Ej. 60 segundos" class="mt-2 min-h-11 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3 text-sm"></div><label class="flex min-h-11 items-center gap-3 text-sm font-semibold"><input type="checkbox" name="details[recording_required]" value="1" @checked(!empty($details['recording_required']))> Requiere grabación</label><div><label class="block text-sm font-semibold">Locación <span class="font-normal text-[var(--color-text-tertiary)]">si requiere grabación</span></label><input name="details[location]" value="{{ $details['location'] ?? '' }}" class="mt-2 min-h-11 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3 text-sm"></div>@else<div><label class="block text-sm font-semibold">Tipo de render</label><input name="details[render_type]" required value="{{ $details['render_type'] ?? '' }}" class="mt-2 min-h-11 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3 text-sm"></div><div><label class="block text-sm font-semibold">Producto o espacio</label><input name="details[subject]" required value="{{ $details['subject'] ?? '' }}" class="mt-2 min-h-11 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3 text-sm"></div><div><label class="block text-sm font-semibold">Número de vistas</label><input type="number" min="1" max="12" name="details[views]" required value="{{ $details['views'] ?? '' }}" class="mt-2 min-h-11 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3 text-sm"></div><div><label class="block text-sm font-semibold">Nivel de detalle</label><select name="details[detail_level]" required class="mt-2 min-h-11 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3 text-sm"><option value="">Selecciona</option><option value="low" @selected(($details['detail_level'] ?? '') === 'low')>Bajo</option><option value="medium" @selected(($details['detail_level'] ?? '') === 'medium')>Medio</option><option value="high" @selected(($details['detail_level'] ?? '') === 'high')>Alto</option></select></div>@endif</div></div></div><div class="mt-8 flex justify-between gap-3"><a href="{{ route('app.requests.drafts.edit', [$requestModel, 'step' => 2]) }}" class="inline-flex min-h-11 items-center px-3 text-sm font-semibold text-[var(--color-text-secondary)]">Atrás</a><x-ui.button type="submit" size="lg">Continuar <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i></x-ui.button></div></form></x-ui.card>
    @elseif($step === 4)
        <x-ui.card><h2 class="text-xl font-semibold">Agrega referencias y archivos</h2><p class="mt-2 text-sm text-[var(--color-text-secondary)]">Puedes adjuntar archivos PDF, imágenes, documentos, video o ZIP de hasta 25 MB.</p><form class="mt-6" method="POST" action="{{ route('app.requests.drafts.files.store', $requestModel) }}" enctype="multipart/form-data">@csrf<div class="rounded-[var(--radius-card)] border border-dashed border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] p-8 text-center"><i data-lucide="upload-cloud" class="mx-auto h-8 w-8 text-[var(--color-text-tertiary)]" aria-hidden="true"></i><label for="file" class="mt-3 block text-sm font-semibold">Selecciona un archivo</label><input id="file" name="file" type="file" required class="mx-auto mt-4 block max-w-full text-sm"><select name="category" class="mt-4 min-h-11 rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-surface-default)] px-3 text-sm"><option value="reference">Referencia</option><option value="technical">Archivo técnico</option><option value="brief">Brief</option></select><div class="mt-4"><x-ui.button type="submit" variant="outline">Agregar archivo</x-ui.button></div></div></form><div class="mt-5 space-y-3">@foreach($requestModel->files as $file)<div class="flex items-center gap-3 rounded-[var(--radius-md)] border border-[var(--color-border-subtle)] p-3"><i data-lucide="file-text" class="h-5 w-5" aria-hidden="true"></i><span class="min-w-0 flex-1 truncate text-sm">{{ $file->original_name }}</span><form method="POST" action="{{ route('app.requests.drafts.files.destroy', [$requestModel, $file]) }}">@csrf @method('DELETE')<button class="min-h-11 px-3 text-sm text-red-300" type="submit">Eliminar</button></form></div>@endforeach</div><div class="mt-8 flex justify-between gap-3"><a href="{{ route('app.requests.drafts.edit', [$requestModel, 'step' => 3]) }}" class="inline-flex min-h-11 items-center px-3 text-sm font-semibold text-[var(--color-text-secondary)]">Atrás</a><a href="{{ route('app.requests.drafts.edit', [$requestModel, 'step' => 5]) }}" class="inline-flex min-h-11 items-center gap-2 rounded-[var(--radius-md)] bg-[var(--color-action-primary)] px-5 text-sm font-semibold">Continuar <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i></a></div></x-ui.card>
    @elseif($step === 5)
        <x-ui.card><form method="POST" action="{{ route('app.requests.drafts.update', $requestModel) }}">@csrf @method('PATCH')<input type="hidden" name="step" value="5"><input type="hidden" name="service" value="{{ $currentService }}"><h2 class="text-xl font-semibold">¿Cuándo lo necesitas?</h2><p class="mt-2 text-sm text-[var(--color-text-secondary)]">Las fechas son recomendaciones iniciales, no un SLA definitivo. Zona horaria: America/Mexico_City.</p><div class="mt-6 grid gap-5 md:grid-cols-2"><div><label for="required_date" class="block text-sm font-semibold">Fecha requerida</label><input id="required_date" name="required_date" type="date" min="{{ now()->toDateString() }}" required value="{{ old('required_date', $requestModel?->required_date?->format('Y-m-d')) }}" class="mt-2 min-h-11 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3 text-sm"><p class="mt-2 text-xs text-[var(--color-text-tertiary)]">Recomendación: {{ app(\App\Services\Requests\RecommendedDateService::class)->recommended($currentService)->translatedFormat('d M Y') }}</p></div><fieldset><legend class="text-sm font-semibold">Prioridad solicitada</legend><div class="mt-2 grid grid-cols-2 gap-2">@foreach(\App\Enums\RequestPriority::cases() as $priority)<label class="flex min-h-11 cursor-pointer items-center gap-2 rounded-[var(--radius-md)] border border-[var(--color-border-subtle)] px-3 text-sm has-[:checked]:border-[var(--color-action-primary)]"><input type="radio" name="requested_priority" value="{{ $priority->value }}" @checked(($requestModel->requested_priority?->value ?? 'medium') === $priority->value)>{{ $priority->label() }}</label>@endforeach</div></fieldset><div class="md:col-span-2"><label for="urgency_reason" class="block text-sm font-semibold">Justificación de urgencia <span class="font-normal text-[var(--color-text-tertiary)]">(obligatoria para Urgente o fecha corta)</span></label><textarea id="urgency_reason" name="urgency_reason" rows="3" maxlength="1000" class="mt-2 w-full rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] p-3 text-sm">{{ $value('urgency_reason') }}</textarea></div></div><div class="mt-8 flex justify-between gap-3"><a href="{{ route('app.requests.drafts.edit', [$requestModel, 'step' => 4]) }}" class="inline-flex min-h-11 items-center px-3 text-sm font-semibold text-[var(--color-text-secondary)]">Atrás</a><x-ui.button type="submit" size="lg">Continuar <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i></x-ui.button></div></form></x-ui.card>
    @else
        <x-ui.card><h2 class="text-xl font-semibold">Revisa tu solicitud</h2><p class="mt-2 text-sm text-[var(--color-text-secondary)]">Confirma que la información es correcta antes de enviarla al equipo creativo.</p><div class="mt-6 grid gap-4 sm:grid-cols-2"><div><p class="text-xs text-[var(--color-text-tertiary)]">Servicio</p><p class="mt-1 font-semibold">{{ $requestModel->service->label() }}</p></div><div><p class="text-xs text-[var(--color-text-tertiary)]">Tipo</p><p class="mt-1 font-semibold">{{ $catalog[$requestModel->request_type] ?? $requestModel->other_request_type }}</p></div><div><p class="text-xs text-[var(--color-text-tertiary)]">Título</p><p class="mt-1 font-semibold">{{ $requestModel->title }}</p></div><div><p class="text-xs text-[var(--color-text-tertiary)]">Fecha requerida</p><p class="mt-1 font-semibold">{{ $requestModel->required_date?->translatedFormat('d M Y') }}</p></div></div><div class="mt-6 rounded-[var(--radius-md)] bg-[var(--color-bg-secondary)] p-4"><p class="text-sm leading-6 text-[var(--color-text-secondary)]">Al enviar la solicitud, el equipo creativo podrá revisarla y solicitar información adicional.</p></div><form class="mt-6" method="POST" action="{{ route('app.requests.drafts.submit', $requestModel) }}">@csrf<label class="flex min-h-11 items-start gap-3 text-sm"><input type="checkbox" name="confirmed" value="1" required class="mt-1"><span>He leído dos veces la solicitud y confirmo que la información proporcionada es correcta.</span></label><div class="mt-6 flex flex-wrap justify-between gap-3"><a href="{{ route('app.requests.drafts.edit', [$requestModel, 'step' => 5]) }}" class="inline-flex min-h-11 items-center px-3 text-sm font-semibold text-[var(--color-text-secondary)]">Editar</a><div class="flex gap-3"><a href="{{ route('app.requests.drafts.edit', [$requestModel, 'step' => 1]) }}" class="inline-flex min-h-11 items-center rounded-[var(--radius-md)] border border-[var(--color-border-default)] px-4 text-sm font-semibold">Guardar borrador</a><x-ui.button type="submit" size="lg">Enviar solicitud <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i></x-ui.button></div></div></form></x-ui.card>
    @endif
</div>
@endsection

