<?php

namespace App\Services\AI;

use App\Models\CreativeRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GeminiRequestReviewService
{
    public function review(CreativeRequest $request): array
    {
        $key = config('gemini.api_key');
        if (blank($key)) throw new RuntimeException('GEMINI_API_KEY no está configurada.');
        $model = config('gemini.model', 'gemini-2.5-flash');
        $payload = ['service' => $request->service?->label(), 'request_type' => $request->request_type, 'other_request_type' => $request->other_request_type, 'title' => $request->title, 'description' => $request->description, 'objective' => $request->objective, 'target_audience' => $request->target_audience, 'channel' => $request->channel, 'required_date' => $request->required_date?->toDateString(), 'requested_priority' => $request->requested_priority?->value, 'urgency_reason' => $request->urgency_reason, 'details' => $request->detail?->data ?? [], 'reference_files' => $request->files->map(fn ($file) => ['name' => Str::limit((string) $file->original_name, 120), 'category' => $file->category])->values()->all()];
        $instruction = 'Revisa una solicitud interna de Marketing para un equipo creativo. Devuelve únicamente JSON válido según el esquema. Evalúa ortografía, gramática, claridad, información faltante, ambigüedades, contradicciones, entregables, medidas, formatos, medio, fecha, textos, imágenes, logotipos y referencias. No inventes datos: si algo no está escrito, repórtalo como faltante o recomendación. No cambies estados, prioridades, responsables, fechas ni el contenido original. IMPORTANTE: corrected_text debe contener únicamente la versión corregida del campo description, como texto normal para pegar en la descripción. No incluyas etiquetas, JSON, el resto de campos, la palabra SOLICITUD ni un resumen dentro de corrected_text. ready significa sin información importante faltante; warning permite enviarla con observaciones; incomplete significa que falta información necesaria.';
        $http = Http::timeout((int) config('gemini.timeout', 30));
        if (filled(config('gemini.ca_bundle'))) $http = $http->withOptions(['verify' => config('gemini.ca_bundle')]);
        $response = $http->acceptJson()->withHeaders(['x-goog-api-key' => $key])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", ['contents' => [['parts' => [['text' => $instruction."\n\nSOLICITUD:\n".json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)]]]], 'generationConfig' => ['temperature' => 0.1, 'responseMimeType' => 'application/json', 'responseSchema' => $this->schema()]]);
        $response->throw();
        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
        if (! is_string($text) || blank($text)) throw new RuntimeException('Gemini no devolvió una revisión.');
        $result = json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        $this->validateResult($result);
        if (str_starts_with(strtolower(trim($result['corrected_text'])), 'solicitud:')) {
            $result['corrected_text'] = (string) $request->description;
        }
        return $result;
    }

    private function schema(): array
    {
        $list = ['type' => 'array', 'items' => ['type' => 'string']];
        return ['type' => 'object', 'properties' => ['status' => ['type' => 'string', 'enum' => ['ready', 'warning', 'incomplete']], 'summary' => ['type' => 'string'], 'corrected_text' => ['type' => 'string'], 'spelling_corrections' => ['type' => 'array', 'items' => ['type' => 'object', 'properties' => ['original' => ['type' => 'string'], 'corrected' => ['type' => 'string']], 'required' => ['original', 'corrected']]], 'missing_information' => $list, 'ambiguous_instructions' => $list, 'contradictions' => $list, 'recommendations' => $list], 'required' => ['status', 'summary', 'corrected_text', 'spelling_corrections', 'missing_information', 'ambiguous_instructions', 'contradictions', 'recommendations']];
    }

    private function validateResult(mixed $result): void
    {
        if (! is_array($result) || ! in_array($result['status'] ?? null, ['ready', 'warning', 'incomplete'], true)) throw new RuntimeException('La respuesta de Gemini no cumple el formato esperado.');
        foreach (['summary', 'corrected_text', 'spelling_corrections', 'missing_information', 'ambiguous_instructions', 'contradictions', 'recommendations'] as $key) if (! array_key_exists($key, $result) || (! is_string($result[$key]) && ! is_array($result[$key]))) throw new RuntimeException('La respuesta de Gemini está incompleta.');
    }
}
