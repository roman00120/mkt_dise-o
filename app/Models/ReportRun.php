<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportRun extends Model
{
    use HasFactory;

    protected $fillable = ['report_schedule_id', 'status', 'format', 'path', 'started_at', 'finished_at', 'error'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'finished_at' => 'datetime'];
    }

    public function schedule()
    {
        return $this->belongsTo(ReportSchedule::class, 'report_schedule_id');
    }
}
