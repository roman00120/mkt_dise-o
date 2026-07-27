@props(['priority' => 'Media'])
@php($map = ['Baja' => ['neutral','arrow-down'], 'Media' => ['info','minus'], 'Alta' => ['warning','arrow-up'], 'Urgente' => ['danger','alarm-clock']])
@php([$variant, $icon] = $map[$priority] ?? ['neutral', 'minus'])
<x-ui.badge :variant="$variant" :icon="$icon">{{ $priority }}</x-ui.badge>
