@props(['interactive' => false, 'selected' => false])
<section {{ $attributes->merge(['class' => 'rounded-[var(--radius-card)] border border-[var(--color-border-subtle)] bg-[var(--color-surface-default)] p-5 ' . ($interactive ? 'cursor-pointer hover:border-[var(--color-border-default)] hover:bg-[var(--color-surface-interactive)]' : '') . ($selected ? ' border-[var(--color-action-primary)] ring-2 ring-[var(--color-focus-ring)]' : '')]) }}>
    {{ $slot }}
</section>
