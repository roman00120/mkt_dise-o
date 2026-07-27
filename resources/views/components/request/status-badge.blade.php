@props(['status' => 'Pendiente'])
@php($map = ['Borrador' => ['neutral','file-edit'], 'Pendiente' => ['info','inbox'], 'En validación' => ['warning','scan-search'], 'Asignada' => ['design','user-check'], 'En proceso' => ['info','play-circle'], 'En espera de información' => ['warning','pause-circle'], 'En revisión interna' => ['design','search-check'], 'En revisión de Marketing' => ['info','eye'], 'Correcciones solicitadas' => ['danger','refresh-cw'], 'Aprobada' => ['success','circle-check'], 'Finalizada' => ['success','check-check'], 'Cancelada' => ['neutral','ban'], 'Rechazada' => ['danger','circle-x']])
@php([$variant, $icon] = $map[$status] ?? ['neutral', 'circle'])
<x-ui.badge :variant="$variant" :icon="$icon">{{ $status }}</x-ui.badge>
