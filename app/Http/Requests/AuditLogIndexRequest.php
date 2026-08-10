<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuditLogIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['action' => ['nullable', 'string', 'max:100'], 'actor_id' => ['nullable', 'integer', 'exists:users,id']];
    }
}
