<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Acceso' }} · TG Creative Hub</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[var(--color-bg-primary)] text-white antialiased">
        <main class="grid min-h-screen lg:grid-cols-[1.1fr_.9fr]">
            <section class="relative hidden overflow-hidden border-r border-[var(--color-border-subtle)] bg-[var(--color-bg-sidebar)] p-10 lg:flex lg:flex-col lg:justify-between xl:p-16">
                <div class="relative z-10"><a href="/" class="inline-flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-[var(--radius-md)] bg-[var(--color-action-primary)] text-sm font-black">TG</span><span><span class="block text-xs font-black tracking-[.18em]">TOTAL GROUND</span><span class="block text-xs text-[var(--color-text-tertiary)]">TG Creative Hub</span></span></a><p class="mt-24 max-w-xl text-5xl font-bold leading-tight">Gestiona solicitudes creativas desde un solo lugar</p><p class="mt-6 max-w-lg text-base leading-7 text-[var(--color-text-secondary)]">Crea, consulta y da seguimiento a solicitudes de Diseño Gráfico, Video y Render 3D.</p></div>
                <div class="relative z-10 grid gap-3 xl:grid-cols-3"><div class="rounded-[var(--radius-md)] border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)] p-4"><i data-lucide="pen-tool" class="h-5 w-5 text-violet-300" aria-hidden="true"></i><p class="mt-8 text-sm font-semibold">Diseño Gráfico</p><p class="mt-1 text-xs leading-5 text-[var(--color-text-tertiary)]">Materiales impresos, digitales y comunicación visual.</p></div><div class="rounded-[var(--radius-md)] border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)] p-4"><i data-lucide="video" class="h-5 w-5 text-rose-300" aria-hidden="true"></i><p class="mt-8 text-sm font-semibold">Video</p><p class="mt-1 text-xs leading-5 text-[var(--color-text-tertiary)]">Producción, edición y contenido audiovisual.</p></div><div class="rounded-[var(--radius-md)] border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)] p-4"><i data-lucide="box" class="h-5 w-5 text-cyan-300" aria-hidden="true"></i><p class="mt-8 text-sm font-semibold">Render 3D</p><p class="mt-1 text-xs leading-5 text-[var(--color-text-tertiary)]">Visualización de productos, espacios y conceptos.</p></div></div>
            </section>
            <section class="flex min-w-0 items-center justify-center p-5 sm:p-8"><div class="w-full max-w-md"><div class="mb-10 lg:hidden"><a href="/" class="inline-flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-[var(--radius-md)] bg-[var(--color-action-primary)] text-sm font-black">TG</span><span><span class="block text-xs font-black tracking-[.18em]">TOTAL GROUND</span><span class="block text-xs text-[var(--color-text-tertiary)]">TG Creative Hub</span></span></a></div><div class="rounded-[var(--radius-card)] border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)] p-6 shadow-[var(--shadow-md)] sm:p-8">{{ $slot }}</div></div></section>
        </div>
    </body>
</html>
