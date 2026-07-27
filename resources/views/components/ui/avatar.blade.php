@props(['name' => 'Usuario', 'size' => 'md', 'status' => null, 'src' => null])
@php($initials = collect(explode(' ', trim($name)))->filter()->take(2)->map(fn($part) => mb_substr($part, 0, 1))->join(''))
<span class="relative inline-flex shrink-0" title="{{ $name }}">
    @if($src)<img src="{{ $src }}" alt="{{ $name }}" class="{{ $size === 'lg' ? 'h-12 w-12' : ($size === 'sm' ? 'h-8 w-8' : 'h-10 w-10') }} rounded-full object-cover">@else<span class="inline-flex {{ $size === 'lg' ? 'h-12 w-12 text-base' : ($size === 'sm' ? 'h-8 w-8 text-[11px]' : 'h-10 w-10 text-xs') }} items-center justify-center rounded-full bg-[var(--color-action-soft)] font-bold text-red-200">{{ strtoupper($initials) }}</span>@endif
    @if($status)<span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-[var(--color-surface-default)] {{ $status === 'online' ? 'bg-green-500' : 'bg-slate-500' }}" aria-label="{{ $status === 'online' ? 'En línea' : 'Desconectado' }}"></span>@endif
</span>
