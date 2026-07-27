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
}
