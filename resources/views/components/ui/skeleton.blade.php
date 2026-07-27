@props(['class' => 'h-4 w-full'])
<div {{ $attributes->merge(['class' => 'animate-pulse rounded bg-[var(--color-surface-interactive)] ' . $class]) }} aria-hidden="true"></div>
