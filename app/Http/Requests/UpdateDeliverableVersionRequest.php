<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliverableVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('version')) ?? false;
    }

    public function rules(): array
    {
        return ['notes' => ['nullable', 'string', 'max:2000'], 'internal_notes' => ['nullable', 'string', 'max:2000']];
    }
}
