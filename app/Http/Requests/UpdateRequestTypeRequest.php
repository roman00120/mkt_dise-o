<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequestTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['label' => ['required', 'string', 'max:120'], 'description' => ['nullable', 'string', 'max:1000'], 'sort_order' => ['nullable', 'integer', 'min:0', 'max:999']];
    }
}
