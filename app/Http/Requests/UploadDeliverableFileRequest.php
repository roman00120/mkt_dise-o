<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDeliverableFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('uploadFile', $this->route('version')) ?? false;
    }

    public function rules(): array
    {
        return ['file' => ['required', 'file', 'max:102400'], 'category' => ['required', 'in:preview,source,final,supporting,compressed'], 'is_primary' => ['nullable', 'boolean'], 'description' => ['nullable', 'string', 'max:500']];
    }
}
