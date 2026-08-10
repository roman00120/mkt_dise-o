<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransitionCreativeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('transition', $this->route('creativeRequest')) ?? false;
    }

    public function rules(): array
    {
        return ['status' => ['required', 'in:in_validation,assigned,in_progress,waiting_for_information,internal_review,marketing_review,rejected']];
    }
}
