<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInternalNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['body' => ['required', 'string', 'max:5000'], 'mentions' => ['array', 'max:10'], 'mentions.*' => ['integer', 'distinct', 'exists:users,id'], 'attachments' => ['array', 'max:5'], 'attachments.*' => ['file', 'max:15360']];
    }
}
