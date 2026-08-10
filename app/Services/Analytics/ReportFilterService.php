<?php

namespace App\Services\Analytics;

use Carbon\CarbonImmutable;

class ReportFilterService
{
    public function normalize(array $filters): array
    {
        $to = isset($filters['to']) ? CarbonImmutable::parse($filters['to'])->endOfDay() : CarbonImmutable::now()->endOfDay();
        $from = isset($filters['from']) ? CarbonImmutable::parse($filters['from'])->startOfDay() : $this->periodFrom($filters['period'] ?? '30', $to);

        return array_merge($filters, ['from_date' => $from, 'to_date' => $to]);
    }

    private function periodFrom(string $period, CarbonImmutable $to): CarbonImmutable
    {
        return match ($period) {
            'today' => $to->startOfDay(), '7' => $to->subDays(6)->startOfDay(), 'current_month' => $to->startOfMonth(), 'previous_month' => $to->subMonth()->startOfMonth(), 'current_quarter' => $to->startOfQuarter(), 'previous_quarter' => $to->subQuarter()->startOfQuarter(), 'current_year' => $to->startOfYear(), 'previous_year' => $to->subYear()->startOfYear(), default => $to->subDays(29)->startOfDay()
        };
    }

    public function previous(array $filters): array
    {
        $current = $this->normalize($filters);
        $days = $current['from_date']->diffInDays($current['to_date']) + 1;

        return array_merge($filters, ['from_date' => $current['from_date']->subDays($days), 'to_date' => $current['from_date']->subSecond()]);
    }
}
