@props(['service' => 'Diseño Gráfico'])
@php($map = ['Diseño Gráfico' => ['design','pen-tool'], 'Video' => ['video','video'], 'Render 3D' => ['render','box']])
@php([$variant, $icon] = $map[$service] ?? ['neutral', 'layers-3'])
<x-ui.badge :variant="$variant" :icon="$icon">{{ $service }}</x-ui.badge>
