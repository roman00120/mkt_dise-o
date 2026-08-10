<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavedReportViewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isActive() ?? false;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:100'], 'report' => ['required', 'in:executive,operations,requests,deliverables,corrections,sla,workload'], 'filters' => ['nullable', 'array', 'max:30']];
    }
}
