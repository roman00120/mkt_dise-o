<?php

namespace App\Enums;

enum DeliverableVersionStatus: string
{
    case DRAFT = 'draft';
    case INTERNAL_REVIEW = 'internal_review';
    case INTERNAL_CHANGES_REQUESTED = 'internal_changes_requested';
    case READY_FOR_MARKETING = 'ready_for_marketing';
    case MARKETING_REVIEW = 'marketing_review';
    case MARKETING_CHANGES_REQUESTED = 'marketing_changes_requested';
    case APPROVED = 'approved';
    case SUPERSEDED = 'superseded';
    case ARCHIVED = 'archived';
}
