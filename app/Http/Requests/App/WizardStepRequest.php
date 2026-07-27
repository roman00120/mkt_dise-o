<?php

namespace App\Http\Requests\App;

use App\Enums\CreativeService;
use App\Enums\RequestPriority;
use App\Support\RequestCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WizardStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $step = (int) $this->input('step', 1);
        $service = $this->input('service');
        $rules = ['step' => ['required', 'integer', 'between:1,6']];
        if ($step === 1) {
            $rules['service'] = ['required', Rule::enum(CreativeService::class)];
        } if ($step === 2) {
            $rules['request_type'] = ['required', Rule::in(array_keys(RequestCatalog::types($service)))];
            $rules['other_request_type'] = ['required_if:request_type,other', 'nullable', 'string', 'max:120'];
        } if ($step === 3) {
            $rules += ['title' => ['required', 'string', 'max:120'], 'description' => ['required', 'string', 'max:2000'], 'objective' => ['nullable', 'string', 'max:1000'], 'target_audience' => ['nullable', 'string', 'max:500'], 'channel' => ['nullable', 'string', 'max:120']];
            $rules += $this->specificRules($service);
        } if ($step === 5) {
            $rules += ['required_date' => ['required', 'date', 'after_or_equal:today'], 'requested_priority' => ['required', Rule::enum(RequestPriority::class)], 'urgency_reason' => ['required_if:requested_priority,urgent', 'nullable', 'string', 'max:1000']];
        }

return $rules;
    }

    private function specificRules(?string $service): array
    {
        return match ($service) {
            'design' => ['details.piece_type' => ['required', 'string', 'max:120'], 'details.proposals' => ['nullable', 'integer', 'between:1,5']], 'video' => ['details.video_type' => ['required', 'string', 'max:120'], 'details.duration' => ['required', 'string', 'max:80'], 'details.recording_required' => ['nullable', 'boolean'], 'details.location' => ['required_if:details.recording_required,1', 'nullable', 'string', 'max:200']], 'render' => ['details.render_type' => ['required', 'string', 'max:120'], 'details.subject' => ['required', 'string', 'max:500'], 'details.views' => ['required', 'integer', 'between:1,12'], 'details.detail_level' => ['required', Rule::in(['low', 'medium', 'high'])]], default => []
        };
    }

    public function messages(): array
    {
        return ['required' => 'Este campo es obligatorio.', 'after_or_equal' => 'La fecha no puede ser anterior a hoy.', 'required_if' => 'Este campo es obligatorio para esta selección.', 'details.*.required' => 'Completa la información específica del servicio.'];
    }
}
