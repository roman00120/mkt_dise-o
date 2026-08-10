<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('requestInformation', $this->route('creativeRequest')) ?? false;
    }

    public function rules(): array
    {
        return ['message' => ['required', 'string', 'max:2000'], 'category' => ['nullable', 'in:brief,files,measures,text,date,technical,other']];
    }
}
