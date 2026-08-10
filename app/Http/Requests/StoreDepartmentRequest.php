<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:120', 'unique:departments,name'], 'code' => ['required', 'string', 'max:30', 'alpha_dash', 'unique:departments,code'], 'description' => ['nullable', 'string', 'max:1000']];
    }
}
