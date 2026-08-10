<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendDeliverableToMarketingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sendMarketing', $this->route('version')) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
