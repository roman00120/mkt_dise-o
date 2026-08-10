<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStatusActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['reason' => ['required', 'string', 'max:1000'], 'confirmed' => ['accepted']];
    }
}
