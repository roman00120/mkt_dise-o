<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isActive() ?? false;
    }

    public function rules(): array
    {
        return ['from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from'], 'period' => ['nullable', 'in:today,7,30,current_month,previous_month,current_quarter,previous_quarter,current_year,previous_year'], 'compare' => ['nullable', 'in:previous,equal_last_year'], 'service' => ['nullable', 'in:design,video,render'], 'department_id' => ['nullable', 'integer', 'exists:departments,id'], 'requester_id' => ['nullable', 'integer', 'exists:users,id'], 'assignee_id' => ['nullable', 'integer', 'exists:users,id'], 'status' => ['nullable', 'in:draft,pending,in_validation,assigned,in_progress,waiting_for_information,internal_review,marketing_review,corrections_requested,approved,completed,cancelled,rejected'], 'requested_priority' => ['nullable', 'in:low,medium,high,urgent'], 'operational_priority' => ['nullable', 'in:low,medium,high,urgent'], 'has_corrections' => ['nullable', 'boolean'], 'has_deliverables' => ['nullable', 'boolean'], 'sla' => ['nullable', 'in:met,at_risk,breached,not_applicable'], 'active' => ['nullable', 'boolean'], 'finalized' => ['nullable', 'boolean']];
    }

    public function filters(): array
    {
        return $this->validated();
    }
}
