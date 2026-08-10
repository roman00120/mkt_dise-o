<?php

namespace App\Enums;

enum CreativeService: string
{
    case DESIGN = 'design';
    case VIDEO = 'video';
    case RENDER = 'render';

    public function label(): string
    {
        return match ($this) {
            self::DESIGN => 'Diseño Gráfico', self::VIDEO => 'Video', self::RENDER => 'Render 3D'
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::DESIGN => 'Materiales impresos, digitales y comunicación visual.',
            self::VIDEO => 'Producción, edición y contenido audiovisual.',
            self::RENDER => 'Visualización de productos, espacios y conceptos.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DESIGN => 'pen-tool',
            self::VIDEO => 'video',
            self::RENDER => 'box',
        };
    }
}
