<?php

namespace App\Enums;

enum RequestPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Baja', self::MEDIUM => 'Media', self::HIGH => 'Alta', self::URGENT => 'Urgente'
        };
    }
}
