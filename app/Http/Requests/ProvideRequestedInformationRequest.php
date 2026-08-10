<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProvideRequestedInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->id === $this->route('creativeRequest')->requester_id;
    }

    public function rules(): array
    {
        return ['response' => ['required', 'string', 'max:2000']];
    }
}
