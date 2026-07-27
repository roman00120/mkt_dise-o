@props(['value' => 0, 'label' => null])
@php($widths = [0 => 'w-0', 25 => 'w-1/4', 50 => 'w-1/2', 75 => 'w-3/4', 100 => 'w-full'])
<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    @if($label)<div class="flex justify-between text-xs text-[var(--color-text-secondary)]"><span>{{ $label }}</span><span>{{ $value }}%</span></div>@endif
    <div class="h-2 overflow-hidden rounded-full bg-[var(--color-surface-interactive)]" role="progressbar" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="100"><div class="h-full rounded-full bg-[var(--color-action-primary)] transition-all {{ $widths[$value] ?? 'w-3/4' }}"></div></div>
</div>
