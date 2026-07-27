<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MARKETING = 'marketing';
    case DESIGN = 'design';
    case VIDEO = 'video';
    case RENDER = 'render';
    case SUPERVISOR = 'supervisor';
}
