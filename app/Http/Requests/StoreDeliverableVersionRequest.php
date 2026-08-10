<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliverableVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', $this->route('deliverable')) ?? false;
    }

    public function rules(): array
    {
        return ['notes' => ['nullable', 'string', 'max:2000'], 'internal_notes' => ['nullable', 'string', 'max:2000']];
    }
}
