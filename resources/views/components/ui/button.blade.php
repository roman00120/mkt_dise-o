@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button', 'loading' => false])
@php
    $variants = ['primary' => 'bg-[var(--color-action-primary)] text-white hover:bg-[var(--color-action-primary-hover)] active:bg-[var(--color-action-primary-active)]', 'secondary' => 'bg-[var(--color-surface-interactive)] text-white hover:bg-[var(--color-border-default)]', 'outline' => 'border border-[var(--color-border-default)] bg-transparent text-white hover:bg-[var(--color-surface-interactive)]', 'ghost' => 'bg-transparent text-[var(--color-text-secondary)] hover:bg-[var(--color-surface-interactive)] hover:text-white', 'destructive' => 'bg-[var(--color-status-danger)] text-white hover:bg-red-500', 'link' => 'bg-transparent px-0 text-[var(--color-action-primary)] hover:text-white'];
    $sizes = ['sm' => 'min-h-9 px-3 text-xs', 'md' => 'min-h-11 px-4 text-sm', 'lg' => 'min-h-12 px-5 text-sm'];
@endphp
<button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center gap-2 rounded-[var(--radius-md)] font-semibold leading-none disabled:cursor-not-allowed disabled:opacity-50 ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md'])])->merge(['aria-busy' => $loading ? 'true' : 'false']) }} @disabled($loading)>
    @if($loading)<i data-lucide="loader-circle" class="h-4 w-4 animate-spin" aria-hidden="true"></i>@endif
    {{ $slot }}
</button>
