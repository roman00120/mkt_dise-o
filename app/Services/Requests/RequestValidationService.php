<?php

namespace App\Services\Requests;

use App\Models\CreativeRequest;
use Illuminate\Validation\ValidationException;

class RequestValidationService
{
    public function validate(CreativeRequest $request): void
    {
        $errors = [];
        foreach (['title' => 'título', 'description' => 'descripción', 'required_date' => 'fecha requerida'] as $field => $label) {
            if (! filled($request->{$field})) {
                $errors[$field] = "La {$label} es obligatoria para validar.";
            }
        }
        if (! $request->request_type) {
            $errors['request_type'] = 'El tipo de solicitud es obligatorio.';
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }
}
