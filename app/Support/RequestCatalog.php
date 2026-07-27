<?php

namespace App\Support;

use App\Enums\CreativeService;

final class RequestCatalog
{
    public static function types(CreativeService|string $service): array
    {
        $key = $service instanceof CreativeService ? $service->value : $service;

        return match ($key) {
            'design' => ['digital' => 'Pieza digital', 'print' => 'Pieza impresa', 'presentation' => 'Presentación', 'catalog' => 'Catálogo', 'packaging' => 'Empaque', 'label' => 'Etiqueta', 'signage' => 'Señalización', 'adaptation' => 'Adaptación de arte', 'other' => 'Otro'],
            'video' => ['corporate' => 'Video corporativo', 'reel' => 'Reel', 'tutorial' => 'Tutorial', 'product' => 'Video de producto', 'testimonial' => 'Testimonio', 'animation' => 'Animación', 'editing' => 'Edición de material', 'coverage' => 'Cobertura', 'other' => 'Otro'],
            'render' => ['product' => 'Render de producto', 'display' => 'Render de exhibidor', 'architectural' => 'Render arquitectónico', 'mockup' => 'Mockup 3D', 'commercial' => 'Escena comercial', 'technical' => 'Visualización técnica', 'other' => 'Otro'],
            default => [],
        };
    }

    public static function services(): array
    {
        return [['value' => 'design', 'label' => 'Diseño Gráfico', 'description' => 'Materiales impresos, digitales y comunicación visual.', 'icon' => 'pen-tool'], ['value' => 'video', 'label' => 'Video', 'description' => 'Producción, edición y contenido audiovisual.', 'icon' => 'video'], ['value' => 'render', 'label' => 'Render 3D', 'description' => 'Visualización de productos, espacios y conceptos.', 'icon' => 'box']];
    }
}
