<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['q' => ['nullable', 'string', 'max:120'], 'role' => ['nullable', 'in:admin,marketing,creative,design,video,render,supervisor'], 'status' => ['nullable', 'in:active,inactive,suspended'], 'department_id' => ['nullable', 'integer', 'exists:departments,id'], 'access' => ['nullable', 'in:recent,never'], 'sort' => ['nullable', 'in:name,created_at,last_login_at,status'], 'direction' => ['nullable', 'in:asc,desc'], 'per_page' => ['nullable', 'in:10,25,50']];
    }
}
