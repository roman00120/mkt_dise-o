<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequestTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['service' => ['required', 'in:design,video,render'], 'key' => ['required', 'string', 'max:80', 'alpha_dash', Rule::unique('request_types', 'key')->where(fn ($query) => $query->where('service', $this->input('service')))], 'label' => ['required', 'string', 'max:120'], 'description' => ['nullable', 'string', 'max:1000'], 'sort_order' => ['nullable', 'integer', 'min:0', 'max:999']];
    }
}
