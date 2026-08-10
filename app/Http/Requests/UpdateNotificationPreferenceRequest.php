<?php

namespace App\Http\Requests;

use App\Models\NotificationPreference;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationPreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['preferences' => ['array'], 'preferences.*' => ['boolean']];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['preferences' => collect(NotificationPreference::TYPES)->mapWithKeys(fn ($type) => [$type => $this->boolean('preferences.'.$type)])->all()]);
    }
}
