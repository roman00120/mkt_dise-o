<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MARKETING = 'marketing';
    case CREATIVE = 'creative';
    // Legacy service roles remain supported for existing automated flows.
    case DESIGN = 'design';
    case VIDEO = 'video';
    case RENDER = 'render';
    case SUPERVISOR = 'supervisor';
}
