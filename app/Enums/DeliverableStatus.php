<?php

namespace App\Enums;

enum DeliverableStatus: string
{
    case DRAFT = 'draft';
    case INTERNAL_REVIEW = 'internal_review';
    case CHANGES_REQUESTED_INTERNAL = 'changes_requested_internal';
    case READY_FOR_MARKETING = 'ready_for_marketing';
    case MARKETING_REVIEW = 'marketing_review';
    case CHANGES_REQUESTED_MARKETING = 'changes_requested_marketing';
    case APPROVED = 'approved';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador', self::INTERNAL_REVIEW => 'Revisión interna', self::CHANGES_REQUESTED_INTERNAL => 'Cambios internos solicitados', self::READY_FOR_MARKETING => 'Listo para Marketing', self::MARKETING_REVIEW => 'Revisión de Marketing', self::CHANGES_REQUESTED_MARKETING => 'Correcciones solicitadas', self::APPROVED => 'Aprobado', self::COMPLETED => 'Finalizado', self::CANCELLED => 'Cancelado'
        };
    }
}
