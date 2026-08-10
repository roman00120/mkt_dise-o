<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequestIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['q' => ['nullable', 'string', 'max:120'], 'service' => ['nullable', 'in:design,video,render'], 'status' => ['nullable', 'string', 'max:50']];
    }
}
