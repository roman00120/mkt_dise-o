<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelCreativeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('cancel', $this->route('creativeRequest')) ?? false;
    }

    public function rules(): array
    {
        return ['reason' => ['required', 'string', 'max:1000']];
    }

    public function messages(): array
    {
        return ['reason.required' => 'Indica el motivo de la cancelación.', 'reason.max' => 'El motivo no puede superar 1,000 caracteres.'];
    }
}
