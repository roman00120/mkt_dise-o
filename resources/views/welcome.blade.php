<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TG Creative Hub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white antialiased">
    <main class="flex min-h-screen items-center justify-center px-6">
        <section class="w-full max-w-xl rounded-2xl border border-slate-800 bg-slate-900 p-10 text-center shadow-2xl">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-orange-400">TOTAL GROUND</p>
            <h1 class="mt-5 text-4xl font-bold tracking-tight">TG Creative Hub</h1>
            <p class="mt-4 text-lg text-slate-300">Entorno instalado correctamente</p>
            @if (app()->environment('local'))
                <p class="mt-8 text-xs text-slate-500">Laravel {{ Illuminate\Foundation\Application::VERSION }}</p>
            @endif
        </section>
    </main>
</body>
</html>
