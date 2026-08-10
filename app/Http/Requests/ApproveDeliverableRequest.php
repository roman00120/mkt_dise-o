<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveDeliverableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('approveMarketing', $this->route('version')) ?? false;
    }

    public function rules(): array
    {
        return ['confirmed' => ['accepted'], 'comments' => ['nullable', 'string', 'max:1000']];
    }
}
