<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'creative_request_id', 'deliverable_id', 'deliverable_version_id', 'requested_by', 'type', 'status', 'summary', 'details', 'category', 'due_date', 'resolved_by_version_id', 'resolved_at'];

    protected function casts(): array
    {
        return ['due_date' => 'date', 'resolved_at' => 'datetime'];
    }

    public function request()
    {
        return $this->belongsTo(CreativeRequest::class, 'creative_request_id');
    }

    public function deliverable()
    {
        return $this->belongsTo(Deliverable::class);
    }

    public function version()
    {
        return $this->belongsTo(DeliverableVersion::class, 'deliverable_version_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function resolvedByVersion()
    {
        return $this->belongsTo(DeliverableVersion::class, 'resolved_by_version_id');
    }
}
