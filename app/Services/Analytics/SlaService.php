<?php

namespace App\Services\Analytics;

use App\Models\CreativeRequest;
use App\Models\SlaPolicy;
use Carbon\CarbonInterface;

class SlaService
{
    public function policyFor(CreativeRequest $request, string $metric): ?SlaPolicy
    {
        return SlaPolicy::where('is_active', true)->where('metric', $metric)->where(fn ($q) => $q->whereNull('service')->orWhere('service', $request->service->value))->whereDate('effective_from', '<=', today())->where(fn ($q) => $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', today()))->orderByRaw('service is null')->orderByDesc('target_minutes')->first();
    }

    public function evaluate(?CarbonInterface $started, ?CarbonInterface $ended, ?SlaPolicy $policy): array
    {
        if (! $started || ! $policy) {
            return ['status' => 'not_applicable', 'minutes' => null, 'target_minutes' => $policy?->target_minutes];
        } $end = $ended ?? now();
        $minutes = $started->diffInMinutes($end);
        $status = $minutes > $policy->target_minutes ? 'breached' : ($minutes >= $policy->target_minutes * ($policy->warning_threshold_percent / 100) ? 'at_risk' : 'met');

        return ['status' => $status, 'minutes' => $minutes, 'target_minutes' => $policy->target_minutes];
    }
}
