<?php

namespace App\Models;

use App\Enums\DeliverableStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deliverable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['uuid', 'creative_request_id', 'created_by', 'current_version_id', 'approved_version_id', 'status', 'title', 'description', 'submitted_to_marketing_at', 'approved_at', 'completed_at'];

    protected function casts(): array
    {
        return ['status' => DeliverableStatus::class, 'submitted_to_marketing_at' => 'datetime', 'approved_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function request()
    {
        return $this->belongsTo(CreativeRequest::class, 'creative_request_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currentVersion()
    {
        return $this->belongsTo(DeliverableVersion::class, 'current_version_id');
    }

    public function approvedVersion()
    {
        return $this->belongsTo(DeliverableVersion::class, 'approved_version_id');
    }

    public function versions()
    {
        return $this->hasMany(DeliverableVersion::class);
    }

    public function correctionRequests()
    {
        return $this->hasMany(CorrectionRequest::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
