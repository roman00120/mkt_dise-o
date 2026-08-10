<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliverableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->id === $this->route('creativeRequest')->assignee_id || $this->user()?->hasRole('admin', 'supervisor');
    }

    public function rules(): array
    {
        return ['title' => ['nullable', 'string', 'max:160']];
    }
}
