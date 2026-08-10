<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"><link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2"><link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2"><title>@yield('title', 'Administración') · TG Creative Hub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[var(--color-bg-primary)] text-white antialiased">
<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-red-600 focus:px-4 focus:py-3">Saltar al contenido principal</a>
<div class="flex min-h-screen">
    <aside class="hidden w-64 shrink-0 border-r border-[var(--color-border-subtle)] bg-[var(--color-bg-sidebar)] p-4 lg:block">
        <a href="{{ route('admin.dashboard') }}" class="brand-logo-link flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded bg-red-600 text-sm font-black">TG</span><span><b class="block text-xs tracking-widest">TOTAL GROUND</b><small class="text-[var(--color-text-tertiary)]">Administración</small></span></a>
        <nav class="mt-8 space-y-1" aria-label="Navegación de administración">
            <a class="block min-h-11 rounded px-3 py-3 text-sm hover:bg-white/10" href="{{ route('admin.dashboard') }}">Resumen</a>
            <a class="block min-h-11 rounded px-3 py-3 text-sm hover:bg-white/10" href="{{ route('admin.users.index') }}">Usuarios</a>
            <a class="block min-h-11 rounded px-3 py-3 text-sm hover:bg-white/10" href="{{ route('admin.departments.index') }}">Departamentos</a>
            <a class="block min-h-11 rounded px-3 py-3 text-sm hover:bg-white/10" href="{{ route('admin.catalogs.request-types.index') }}">Catálogos</a>
            <a class="block min-h-11 rounded px-3 py-3 text-sm hover:bg-white/10" href="{{ route('admin.settings.index') }}">Configuración</a>
            <a class="block min-h-11 rounded px-3 py-3 text-sm hover:bg-white/10" href="{{ route('admin.requests.index') }}">Solicitudes</a>
            <a class="block min-h-11 rounded px-3 py-3 text-sm hover:bg-white/10" href="{{ route('creative.dashboard') }}">Operación creativa</a>
            <a class="block min-h-11 rounded px-3 py-3 text-sm hover:bg-white/10" href="{{ route('admin.audit.index') }}">Auditoría</a>
            <a class="flex min-h-11 items-center justify-between rounded-xl px-3.5 py-3 text-sm font-black text-white bg-gradient-to-r from-red-600 via-red-500 to-amber-500 hover:from-red-500 hover:to-amber-400 transition shadow-lg my-1" href="{{ route('admin.kpis.index') }}">
                <span>📊 KPIs y Reportes</span>
                <span class="rounded bg-black/30 px-1.5 py-0.5 text-[10px] font-black text-white uppercase">HUB</span>
            </a>
        </nav>
        <div class="mt-8 rounded-xl border border-slate-700/60 bg-slate-900/90 p-3.5 shadow-lg"><div class="flex items-center gap-3"><div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-red-500 to-red-700 text-xs font-extrabold text-white uppercase shadow-md">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div><div class="min-w-0 flex-1"><span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Usuario activo</span><p class="truncate text-sm font-extrabold text-white leading-tight">{{ auth()->user()->name }}</p><span class="inline-block mt-0.5 rounded bg-red-500/20 border border-red-500/30 px-1.5 py-0.2 text-[10px] font-semibold text-red-300">{{ match(auth()->user()->role?->value ?? auth()->user()->role) { 'admin' => 'Administración', 'marketing' => 'Marketing', 'supervisor' => 'Supervisión', 'creative' => 'Creativo', 'design' => 'Diseño Gráfico', 'video' => 'Video', 'render' => 'Render 3D', default => 'Usuario' } }}</span></div></div><form method="POST" action="{{ route('logout') }}" class="mt-2.5 pt-2 border-t border-slate-800">@csrf<button type="submit" class="w-full text-left text-xs font-medium text-slate-400 hover:text-red-400 transition flex items-center gap-1.5"><span>🚪</span><span>Cerrar sesión</span></button></form></div>
    </aside>
    <div class="min-w-0 flex-1"><header class="flex min-h-16 items-center justify-between border-b border-[var(--color-border-subtle)] bg-[var(--color-bg-secondary)] px-4 sm:px-6"><div><p class="text-sm font-semibold">@yield('header', 'Administración')</p><p class="hidden text-xs text-[var(--color-text-tertiary)] sm:block">Control operativo de TG Creative Hub</p></div><a href="{{ route('app.dashboard') }}" class="min-h-11 rounded px-3 py-3 text-sm hover:bg-white/10">Ir a la aplicación</a></header><main id="main-content" class="px-4 py-6 sm:px-6 lg:px-8" tabindex="-1">
        @if(session('status'))<div class="mb-5 rounded border border-green-500/40 bg-green-500/10 p-3" role="status">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="mb-5 rounded border border-red-500/40 bg-red-500/10 p-3" role="alert"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @yield('content')
    </main></div>
</div>
<x-ui.lightbox-viewer />
</body>
</html>
