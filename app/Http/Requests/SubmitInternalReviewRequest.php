<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitInternalReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('submitInternal', $this->route('version')) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
