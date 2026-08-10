<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('department')->id;

        return ['name' => ['required', 'string', 'max:120', 'unique:departments,name,'.$id], 'code' => ['required', 'string', 'max:30', 'alpha_dash', 'unique:departments,code,'.$id], 'description' => ['nullable', 'string', 'max:1000']];
    }
}
