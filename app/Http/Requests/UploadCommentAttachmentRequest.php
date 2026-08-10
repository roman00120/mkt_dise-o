<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadCommentAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['attachment' => ['required', 'file', 'max:15360', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,ppt,pptx,zip']];
    }
}
