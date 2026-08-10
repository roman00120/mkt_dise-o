<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SlaPolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:120'], 'service' => ['nullable', 'in:design,video,render'], 'metric' => ['required', 'in:initial_response,assignment,delivery,internal_review,marketing_review,corrections'], 'target_minutes' => ['required', 'integer', 'min:1', 'max:525600'], 'warning_threshold_percent' => ['required', 'integer', 'min:1', 'max:99'], 'business_hours_only' => ['nullable', 'boolean'], 'effective_from' => ['required', 'date'], 'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'], 'priority' => ['nullable', 'in:low,medium,high,urgent']];
    }
}
