@props(['title', 'description' => null, 'action' => null])
<section {{ $attributes->merge(['class' => 'ds-section']) }} aria-labelledby="{{ str($title)->slug() }}-title">
    <div class="mb-4 flex flex-wrap items-end justify-between gap-3"><div><h2 id="{{ str($title)->slug() }}-title" class="text-lg font-semibold tracking-tight">{{ $title }}</h2>@if($description)<p class="mt-1 text-sm text-[var(--color-text-secondary)]">{{ $description }}</p>@endif</div>@if($action)<div>{{ $action }}</div>@endif</div>
    {{ $slot }}
</section>
