<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:190', 'unique:users,email'], 'role' => ['required', 'in:admin,marketing,creative,design,video,render,supervisor'], 'department_id' => ['nullable', 'integer', 'exists:departments,id'], 'status' => ['required', 'in:active,inactive']];
    }
}
