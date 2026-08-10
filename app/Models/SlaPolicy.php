<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlaPolicy extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'name', 'service', 'request_type_id', 'metric', 'target_minutes', 'warning_threshold_percent', 'business_hours_only', 'is_active', 'priority', 'effective_from', 'effective_to', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return ['business_hours_only' => 'boolean', 'is_active' => 'boolean', 'effective_from' => 'date', 'effective_to' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(fn (self $model) => $model->uuid ??= (string) Str::uuid());
    }

    public function requestType()
    {
        return $this->belongsTo(RequestType::class);
    }
}
