<?php

namespace App\Enums;

enum RequestStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case IN_VALIDATION = 'in_validation';
    case ASSIGNED = 'assigned';
    case IN_PROGRESS = 'in_progress';
    case WAITING_FOR_INFORMATION = 'waiting_for_information';
    case INTERNAL_REVIEW = 'internal_review';
    case MARKETING_REVIEW = 'marketing_review';
    case CORRECTIONS_REQUESTED = 'corrections_requested';
    case APPROVED = 'approved';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador', self::PENDING => 'Pendiente', self::IN_VALIDATION => 'En validación', self::ASSIGNED => 'Asignada', self::IN_PROGRESS => 'En proceso', self::WAITING_FOR_INFORMATION => 'En espera de información', self::INTERNAL_REVIEW => 'En revisión interna', self::MARKETING_REVIEW => 'En revisión de Marketing', self::CORRECTIONS_REQUESTED => 'Correcciones solicitadas', self::APPROVED => 'Aprobada', self::COMPLETED => 'Finalizada', self::CANCELLED => 'Cancelada', self::REJECTED => 'Rechazada'
        };
    }
}
