<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReassignCreativeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('reassign', $this->route('creativeRequest')) ?? false;
    }

    public function rules(): array
    {
        return ['assignee_id' => ['required', 'integer', 'exists:users,id', 'different:current_assignee_id'], 'reason' => ['required', 'string', 'max:1000']];
    }
}
