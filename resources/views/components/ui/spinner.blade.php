@props(['size' => 'md'])
<i data-lucide="loader-circle" class="{{ $size === 'sm' ? 'h-4 w-4' : ($size === 'lg' ? 'h-7 w-7' : 'h-5 w-5') }} animate-spin" aria-label="Cargando"></i>
