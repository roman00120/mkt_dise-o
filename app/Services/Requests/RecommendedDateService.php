<?php

namespace App\Services\Requests;

use App\Enums\CreativeService;
use Carbon\Carbon;

class RecommendedDateService
{
    public function days(CreativeService|string $service): int
    {
        $key = $service instanceof CreativeService ? $service->value : $service;

        return ['design' => 3, 'video' => 7, 'render' => 5][$key] ?? 3;
    }

    public function recommended(CreativeService|string $service, ?Carbon $from = null): Carbon
    {
        $date = ($from ?? now())->copy()->startOfDay();
        $remaining = $this->days($service);
        while ($remaining > 0) {
            $date->addDay();
            if ($date->isWeekday()) {
                $remaining--;
            }
        }

        return $date;
    }

    public function isShort(CreativeService|string $service, Carbon|string $date): bool
    {
        return Carbon::parse($date)->startOfDay()->lessThan($this->recommended($service));
    }
}
