<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveInternalReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('internalApprove', $this->route('version')) ?? false;
    }

    public function rules(): array
    {
        return ['comments' => ['nullable', 'string', 'max:1000']];
    }
}
