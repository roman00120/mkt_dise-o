<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestMarketingCorrectionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('requestMarketingCorrections', $this->route('version')) ?? false;
    }

    public function rules(): array
    {
        return ['summary' => ['required', 'string', 'max:200'], 'details' => ['required', 'string', 'max:3000'], 'category' => ['nullable', 'string', 'max:80'], 'due_date' => ['nullable', 'date', 'after_or_equal:today']];
    }
}
