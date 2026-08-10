<?php

namespace App\Http\Requests;

use App\Enums\RequestPriority;
use App\Enums\RequestStatus;
use App\Models\CreativeRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreativeRequestIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewCreativePanel', CreativeRequest::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['q' => trim((string) $this->input('q', '')), 'per_page' => (int) $this->input('per_page', 25)]);
    }

    public function rules(): array
    {
        return ['q' => ['nullable', 'string', 'max:120'], 'service' => ['nullable', 'in:design,video,render'], 'status' => ['nullable', Rule::in(array_column(RequestStatus::cases(), 'value'))], 'assignee_id' => ['nullable', 'integer', 'exists:users,id'], 'operational_priority' => ['nullable', Rule::in(array_column(RequestPriority::cases(), 'value'))], 'requested_priority' => ['nullable', Rule::in(array_column(RequestPriority::cases(), 'value'))], 'unassigned' => ['nullable', 'boolean'], 'mine' => ['nullable', 'boolean'], 'sort' => ['nullable', Rule::in(['updated_at', 'created_at', 'required_date', 'internal_due_date'])], 'direction' => ['nullable', Rule::in(['asc', 'desc'])], 'per_page' => ['required', Rule::in([10, 25, 50])]];
    }
}
