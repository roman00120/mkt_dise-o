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

    protected $fillable = ['uuid', 'folio', 'requester_id', 'department_id', 'duplicated_from_id', 'assignee_id', 'assigned_by', 'assigned_at', 'validated_by', 'validated_at', 'service', 'request_type', 'other_request_type', 'title', 'description', 'objective', 'target_audience', 'channel', 'required_date', 'requested_priority', 'operational_priority', 'internal_due_date', 'urgency_reason', 'status', 'current_step', 'submitted_at', 'cancelled_at', 'cancellation_reason', 'started_at', 'completed_at', 'waiting_information_since', 'last_status_changed_at', 'last_autosaved_at'];

    protected function casts(): array
    {
        return ['service' => CreativeService::class, 'requested_priority' => RequestPriority::class, 'operational_priority' => RequestPriority::class, 'status' => RequestStatus::class, 'required_date' => 'date', 'internal_due_date' => 'date', 'submitted_at' => 'datetime', 'assigned_at' => 'datetime', 'validated_at' => 'datetime', 'cancelled_at' => 'datetime', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'waiting_information_since' => 'datetime', 'last_status_changed_at' => 'datetime', 'last_autosaved_at' => 'datetime'];
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function informationRequests()
    {
        return $this->hasMany(RequestInformationRequest::class);
    }

    public function deliverables()
    {
        return $this->hasMany(Deliverable::class);
    }

    public function duplicatedFrom()
    {
        return $this->belongsTo(self::class, 'duplicated_from_id');
    }

    public function duplicates()
    {
        return $this->hasMany(self::class, 'duplicated_from_id');
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

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function isDraft(): bool
    {
        return $this->status === RequestStatus::DRAFT;
    }

    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        return $query->where($field ?: (str_starts_with((string) $value, 'TG-') ? 'folio' : $this->getRouteKeyName()), $value);
    }
}
