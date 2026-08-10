<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectCreativeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('reject', $this->route('creativeRequest')) ?? false;
    }

    public function rules(): array
    {
        return ['reason' => ['required', 'string', 'max:1000'], 'category' => ['required', 'in:scope,duplicate,incomplete,wrong_service,cancelled,other']];
    }
}
