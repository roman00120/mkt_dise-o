<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2"><link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2"><title>{{ $title ?? $__env->yieldContent('title', 'Acceso') }} · TG Creative Hub</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-[var(--color-bg-primary)] p-6 text-white antialiased"><main class="mx-auto flex min-h-[80vh] max-w-xl flex-col justify-center rounded border border-white/10 bg-[var(--color-bg-secondary)] p-8"><a href="/" class="mb-8 text-xs font-black tracking-widest">TOTAL GROUND · TG CREATIVE HUB</a>{!! $slot ?? $__env->yieldContent('content') !!}</main></body>
</html>
