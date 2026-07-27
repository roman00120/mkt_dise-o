@props(['label', 'icon' => 'more-horizontal', 'size' => 'md', 'variant' => 'ghost'])
<button type="button" aria-label="{{ $label }}" title="{{ $label }}" {{ $attributes->merge(['class' => 'inline-flex min-h-11 min-w-11 items-center justify-center rounded-[var(--radius-md)] text-[var(--color-text-secondary)] hover:bg-[var(--color-surface-interactive)] hover:text-white ' . ($variant === 'outline' ? 'border border-[var(--color-border-default)]' : '')]) }}>
    <i data-lucide="{{ $icon }}" class="{{ $size === 'sm' ? 'h-4 w-4' : 'h-5 w-5' }}" aria-hidden="true"></i>
</button>
