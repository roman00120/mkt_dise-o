<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="UI Kit local de TG Creative Hub">
    <title>{{ $title ?? 'Design System' }} · TG Creative Hub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[var(--color-bg-primary)] text-[var(--color-text-primary)] antialiased">
    @yield('content')
</body>
</html>
