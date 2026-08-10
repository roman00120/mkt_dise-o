<?php

namespace App\Services\Analytics;

use App\Models\BusinessCalendarHoliday;
use Carbon\CarbonInterface;

class BusinessTimeCalculator
{
    public function minutes(CarbonInterface $from, CarbonInterface $to): int
    {
        if ($to->lessThanOrEqualTo($from)) {
            return 0;
        } $holidays = BusinessCalendarHoliday::where('is_active', true)->whereBetween('date', [$from->toDateString(), $to->toDateString()])->pluck('date')->map->toDateString()->all();
        $cursor = $from->copy();
        $total = 0;
        while ($cursor->lessThan($to)) {
            if ($cursor->isWeekday() && ! in_array($cursor->toDateString(), $holidays, true)) {
                $next = $cursor->copy()->addMinute();
                $total += min($next->timestamp, $to->timestamp) - $cursor->timestamp;
            } $cursor->addMinute();
        }

        return (int) ceil($total / 60);
    }
}
