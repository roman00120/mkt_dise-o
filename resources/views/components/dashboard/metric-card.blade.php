@props(['metric'])
@php($tone = ['primary' => 'bg-[var(--color-action-soft)] text-red-200', 'info' => 'bg-blue-500/15 text-blue-300', 'warning' => 'bg-amber-500/15 text-amber-300', 'danger' => 'bg-red-500/15 text-red-300'][$metric['tone']] ?? 'bg-[var(--color-surface-interactive)] text-white')
<x-ui.card class="min-w-0">
    <div class="flex items-start justify-between gap-3"><div class="min-w-0"><p class="text-sm text-[var(--color-text-secondary)]">{{ $metric['label'] }}</p><p class="mt-3 text-3xl font-bold tracking-tight">{{ $metric['value'] }}</p></div><span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-[var(--radius-md)] {{ $tone }}"><i data-lucide="{{ $metric['icon'] }}" class="h-5 w-5" aria-hidden="true"></i></span></div>
    <p class="mt-4 text-xs text-[var(--color-text-tertiary)]">{{ $metric['context'] }}</p>
</x-ui.card>
