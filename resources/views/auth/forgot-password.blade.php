<x-guest-layout title="Recuperar contraseña">
    <div class="mb-8"><p class="text-xs font-bold uppercase tracking-[.16em] text-red-300">Recuperación segura</p><h1 class="mt-3 text-3xl font-bold tracking-tight">Recupera tu contraseña</h1><p class="mt-2 text-sm leading-6 text-[var(--color-text-secondary)]">Ingresa tu correo corporativo y te enviaremos un enlace para restablecerla.</p></div>
    @if(session('status'))<x-ui.alert variant="success" title="Solicitud recibida" class="mb-5">{{ session('status') }}</x-ui.alert>@endif
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">@csrf<x-ui.input id="email" name="email" type="email" label="Correo corporativo" placeholder="nombre@totalground.com" autocomplete="email" :value="old('email')" :error="$errors->first('email')" required icon="mail" /><x-ui.button type="submit" class="w-full" size="lg">Enviar enlace</x-ui.button></form>
    <a href="{{ route('login') }}" class="mt-6 inline-flex min-h-11 items-center text-sm font-semibold text-[var(--color-text-secondary)] hover:text-white"><i data-lucide="arrow-left" class="mr-2 h-4 w-4" aria-hidden="true"></i>Volver al inicio de sesión</a>
</x-guest-layout>
