@props(['service'])
<x-ui.card class="flex h-full flex-col" interactive>
    <div class="flex items-start justify-between gap-3"><span class="inline-flex h-11 w-11 items-center justify-center rounded-[var(--radius-md)] bg-[var(--color-surface-interactive)] text-[var(--color-text-secondary)]"><i data-lucide="{{ $service['icon'] }}" class="h-5 w-5" aria-hidden="true"></i></span><x-ui.badge :variant="$service['tone']">Disponible</x-ui.badge></div>
    <h3 class="mt-6 font-semibold">{{ $service['name'] }}</h3><p class="mt-2 flex-1 text-sm leading-6 text-[var(--color-text-secondary)]">{{ $service['description'] }}</p>
    <a href="{{ route('app.requests.create', ['service' => $service['key']]) }}" class="mt-5 inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-[var(--color-action-primary)] hover:text-white">Crear solicitud <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i></a>
</x-ui.card>
