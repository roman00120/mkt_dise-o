<?php

namespace App\Console\Commands;

use App\Models\ReportRun;
use App\Models\ReportSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunScheduledReports extends Command
{
    protected $signature = 'reports:run-scheduled {--dry-run}';

    protected $description = 'Genera ejecuciones internas idempotentes para reportes programados.';

    public function handle(): int
    {
        $processed = 0;
        foreach (ReportSchedule::where('is_active', true)->where(function ($q) {
            $q->whereNull('next_run_at')->orWhere('next_run_at', '<=', now());
        })->cursor() as $schedule) {
            DB::transaction(function () use ($schedule, &$processed): void {
                $locked = ReportSchedule::lockForUpdate()->find($schedule->id);
                if (! $locked || ! $locked->is_active || ($locked->next_run_at && $locked->next_run_at->isFuture())) {
                    return;
                }if (! $this->option('dry-run')) {
                    ReportRun::create(['report_schedule_id' => $locked->id, 'status' => 'pending', 'format' => $locked->format, 'started_at' => now()]);
                    $locked->update(['last_run_at' => now(), 'next_run_at' => $locked->frequency === 'daily' ? now()->addDay() : ($locked->frequency === 'weekly' ? now()->addWeek() : now()->addMonth())]);
                }$processed++;
            });
        } $this->info(($this->option('dry-run') ? 'Se revisarían ' : 'Se generaron ').$processed.' ejecuciones.');

        return self::SUCCESS;
    }
}
