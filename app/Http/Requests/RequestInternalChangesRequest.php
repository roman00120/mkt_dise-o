<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestInternalChangesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('requestInternalChanges', $this->route('version')) ?? false;
    }

    public function rules(): array
    {
        return ['reason' => ['required', 'string', 'max:2000'], 'category' => ['nullable', 'string', 'max:80']];
    }
}
