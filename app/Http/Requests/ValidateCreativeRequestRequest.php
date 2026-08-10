<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateCreativeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('validate', $this->route('creativeRequest')) ?? false;
    }

    public function rules(): array
    {
        return ['assignee_id' => ['required', 'integer', 'exists:users,id'], 'operational_priority' => ['nullable', 'in:low,medium,high,urgent'], 'internal_due_date' => ['nullable', 'date', 'after_or_equal:today'], 'observation' => ['nullable', 'string', 'max:1000']];
    }
}
