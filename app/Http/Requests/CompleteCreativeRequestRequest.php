<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteCreativeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('complete', $this->route('creativeRequest')->deliverables()->first()) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
