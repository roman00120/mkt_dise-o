<x-guest-layout title="Iniciar sesión">
    <div class="mb-8"><p class="text-xs font-bold uppercase tracking-[.16em] text-red-300">Acceso corporativo</p><h1 class="mt-3 text-3xl font-bold tracking-tight">Bienvenido</h1><p class="mt-2 text-sm text-[var(--color-text-secondary)]">Inicia sesión con tu cuenta corporativa.</p></div>
    @if(session('status'))<x-ui.alert variant="success" class="mb-5">{{ session('status') }}</x-ui.alert>@endif
    @if($errors->any())<x-ui.alert variant="error" title="No fue posible iniciar sesión" class="mb-5">No fue posible iniciar sesión con los datos proporcionados.</x-ui.alert>@endif
    <form method="POST" action="{{ route('login.store') }}" class="space-y-5" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf
        <x-ui.input id="email" name="email" type="email" label="Correo corporativo" placeholder="nombre@totalground.com" autocomplete="email" :value="old('email')" :error="$errors->first('email')" required icon="mail" autofocus />
        <x-auth.password-field :error="$errors->first('password')" />
        <div class="flex flex-wrap items-center justify-between gap-3"><label class="inline-flex min-h-11 items-center gap-2 text-sm text-[var(--color-text-secondary)]"><input name="remember" type="checkbox" class="h-4 w-4 rounded border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] accent-red-600"> Mantener sesión iniciada</label><a href="{{ route('password.request') }}" class="text-sm font-semibold text-red-300 hover:text-white">¿Olvidaste tu contraseña?</a></div>
        <x-ui.button type="submit" class="w-full" size="lg" x-bind:disabled="submitting" x-bind:aria-busy="submitting"><span x-show="!submitting">Iniciar sesión</span><span x-show="submitting" x-cloak class="inline-flex items-center gap-2"><x-ui.spinner size="sm" /> Iniciando sesión…</span></x-ui.button>
    </form>
</x-guest-layout>
