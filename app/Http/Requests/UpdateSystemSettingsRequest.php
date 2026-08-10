<?php

namespace App\Http\Requests;

use App\Services\Settings\SystemSettingService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return collect(SystemSettingService::ALLOWED)->mapWithKeys(fn ($key) => [$key => ['nullable', 'string', 'max:500']])->all();
    }
}
