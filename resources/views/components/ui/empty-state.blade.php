@props(['title' => 'No hay datos todavía', 'description' => null, 'icon' => 'inbox'])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-[var(--radius-card)] border border-dashed border-[var(--color-border-default)] bg-[var(--color-surface-default)] px-6 py-12 text-center']) }}>
    <span class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-full bg-[var(--color-surface-interactive)] text-[var(--color-text-tertiary)]"><i data-lucide="{{ $icon }}" class="h-7 w-7" aria-hidden="true"></i></span>
    <h3 class="font-semibold">{{ $title }}</h3>
    @if($description)<p class="mt-2 max-w-sm text-sm text-[var(--color-text-secondary)]">{{ $description }}</p>@endif
    @if($slot->isNotEmpty())<div class="mt-5">{{ $slot }}</div>@endif
</div>
