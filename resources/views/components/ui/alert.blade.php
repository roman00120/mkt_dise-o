@props(['variant' => 'info', 'title' => null, 'icon' => null])
@php($styles = ['info' => 'border-blue-500/30 bg-blue-500/10 text-blue-100', 'success' => 'border-green-500/30 bg-green-500/10 text-green-100', 'warning' => 'border-amber-500/30 bg-amber-500/10 text-amber-100', 'error' => 'border-red-500/30 bg-red-500/10 text-red-100'])
<div role="status" {{ $attributes->merge(['class' => 'flex gap-3 rounded-[var(--radius-md)] border p-4 text-sm ' . ($styles[$variant] ?? $styles['info'])]) }}>
    <i data-lucide="{{ $icon ?? ($variant === 'success' ? 'check-circle' : ($variant === 'warning' ? 'triangle-alert' : ($variant === 'error' ? 'circle-x' : 'info'))) }}" class="mt-0.5 h-5 w-5 shrink-0" aria-hidden="true"></i>
    <div>@if($title)<p class="font-semibold">{{ $title }}</p>@endif<div class="text-sm opacity-80">{{ $slot }}</div></div>
</div>
