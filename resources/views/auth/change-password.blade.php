<x-guest-layout title="Cambiar contraseña">
    <div class="mb-8"><p class="text-xs font-bold uppercase tracking-[.16em] text-red-300">Primer acceso</p><h1 class="mt-3 text-3xl font-bold tracking-tight">Cambia tu contraseña</h1><p class="mt-2 text-sm leading-6 text-[var(--color-text-secondary)]">Por seguridad debes definir una contraseña personal antes de continuar.</p></div>
    <form method="POST" action="{{ route('password.change.update') }}" class="space-y-5">@csrf
        <label class="block text-sm font-semibold" for="password">Nueva contraseña<input id="password" name="password" type="password" required autocomplete="new-password" class="mt-2 min-h-11 w-full rounded border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3"><span class="mt-2 block text-xs text-[var(--color-text-tertiary)]">Mínimo 12 caracteres, mayúsculas, minúsculas, números y símbolos.</span></label>
        <label class="block text-sm font-semibold" for="password_confirmation">Confirma la contraseña<input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-2 min-h-11 w-full rounded border border-[var(--color-border-default)] bg-[var(--color-bg-secondary)] px-3"></label>
        @if($errors->any())<p class="text-sm text-red-300">{{ $errors->first() }}</p>@endif
        <x-ui.button type="submit" class="w-full" size="lg">Guardar contraseña</x-ui.button>
    </form>
</x-guest-layout>
