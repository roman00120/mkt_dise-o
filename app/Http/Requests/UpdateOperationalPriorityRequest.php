<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOperationalPriorityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('updateOperationalPriority', $this->route('creativeRequest')) ?? false;
    }

    public function rules(): array
    {
        return ['operational_priority' => ['required', 'in:low,medium,high,urgent']];
    }
}
