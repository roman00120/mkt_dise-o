<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return ['report' => ['required', 'in:executive,operations,requests,deliverables,corrections,sla,workload'], 'frequency' => ['required', 'in:daily,weekly,monthly'], 'format' => ['required', 'in:csv,pdf'], 'filters' => ['nullable', 'array', 'max:30']];
    }
}
