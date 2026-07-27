@props(['variant' => 'neutral', 'size' => 'md', 'icon' => null])
@php($colors = ['neutral' => 'bg-slate-700/40 text-slate-200', 'success' => 'bg-green-500/15 text-green-300', 'warning' => 'bg-amber-500/15 text-amber-300', 'danger' => 'bg-red-500/15 text-red-300', 'info' => 'bg-blue-500/15 text-blue-300', 'design' => 'bg-violet-500/15 text-violet-300', 'video' => 'bg-rose-500/15 text-rose-300', 'render' => 'bg-cyan-500/15 text-cyan-300'])
<span {{ $attributes->merge(['class' => 'inline-flex w-fit items-center gap-1.5 rounded-[var(--radius-pill)] font-semibold ' . ($colors[$variant] ?? $colors['neutral']) . ' ' . ($size === 'sm' ? 'px-2 py-1 text-[11px]' : 'px-2.5 py-1.5 text-xs')]) }}>
    @if($icon)<i data-lucide="{{ $icon }}" class="h-3.5 w-3.5" aria-hidden="true"></i>@endif
    {{ $slot }}
</span>
