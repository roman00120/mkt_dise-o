<?php

namespace App\Models;

use App\Enums\CreativeService;
use App\Enums\RequestPriority;
use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreativeRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['uuid', 'folio', 'requester_id', 'department_id', 'service', 'request_type', 'other_request_type', 'title', 'description', 'objective', 'target_audience', 'channel', 'required_date', 'requested_priority', 'urgency_reason', 'status', 'current_step', 'submitted_at', 'last_autosaved_at'];

    protected function casts(): array
    {
        return ['service' => CreativeService::class, 'requested_priority' => RequestPriority::class, 'status' => RequestStatus::class, 'required_date' => 'date', 'submitted_at' => 'datetime', 'last_autosaved_at' => 'datetime'];
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function detail()
    {
        return $this->hasOne(CreativeRequestDetail::class);
    }

    public function files()
    {
        return $this->hasMany(CreativeRequestFile::class);
    }

    public function events()
    {
        return $this->hasMany(CreativeRequestEvent::class);
    }

    public function isDraft(): bool
    {
        return $this->status === RequestStatus::DRAFT;
    }
}
