<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestStatusPeriod extends Model
{
    use HasFactory;

    protected $fillable = ['creative_request_id', 'status', 'started_at', 'ended_at', 'duration_seconds', 'changed_by'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'ended_at' => 'datetime'];
    }

    public function request()
    {
        return $this->belongsTo(CreativeRequest::class, 'creative_request_id');
    }
}
