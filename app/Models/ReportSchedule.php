<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReportSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'user_id', 'report', 'frequency', 'filters', 'format', 'is_active', 'next_run_at', 'last_run_at'];

    protected function casts(): array
    {
        return ['filters' => 'array', 'is_active' => 'boolean', 'next_run_at' => 'datetime', 'last_run_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $model) => $model->uuid ??= (string) Str::uuid());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function runs()
    {
        return $this->hasMany(ReportRun::class);
    }
}
