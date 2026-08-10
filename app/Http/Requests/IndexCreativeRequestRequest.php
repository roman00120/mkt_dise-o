<?php

namespace App\Http\Requests;

use App\Enums\CreativeService;
use App\Enums\RequestPriority;
use App\Enums\RequestStatus;
use App\Models\CreativeRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexCreativeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', CreativeRequest::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'q' => trim((string) $this->input('q', '')),
            'per_page' => (int) $this->input('per_page', 10),
        ]);
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', Rule::in(array_column(RequestStatus::cases(), 'value'))],
            'service' => ['nullable', Rule::in(array_column(CreativeService::cases(), 'value'))],
            'priority' => ['nullable', Rule::in(array_column(RequestPriority::cases(), 'value'))],
            'required_from' => ['nullable', 'date'],
            'required_to' => ['nullable', 'date', 'after_or_equal:required_from'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date', 'after_or_equal:created_from'],
            'attention' => ['nullable', 'boolean'],
            'drafts' => ['nullable', 'boolean'],
            'scope' => ['nullable', 'in:active,completed,review'],
            'sort' => ['nullable', Rule::in(['created_at', 'required_date', 'requested_priority', 'updated_at'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['required', Rule::in([10, 25, 50])],
        ];
    }
}
