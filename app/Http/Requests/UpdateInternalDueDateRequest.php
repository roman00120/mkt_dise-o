<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInternalDueDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('updateInternalDueDate', $this->route('creativeRequest')) ?? false;
    }

    public function rules(): array
    {
        return ['internal_due_date' => ['required', 'date', 'after_or_equal:today']];
    }
}
