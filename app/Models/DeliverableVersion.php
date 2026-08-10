<?php

namespace App\Models;

use App\Enums\DeliverableVersionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliverableVersion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['uuid', 'deliverable_id', 'version_number', 'created_by', 'status', 'notes', 'internal_notes', 'submitted_for_internal_review_at', 'submitted_to_marketing_at', 'reviewed_at', 'approved_at'];

    protected function casts(): array
    {
        return ['status' => DeliverableVersionStatus::class, 'submitted_for_internal_review_at' => 'datetime', 'submitted_to_marketing_at' => 'datetime', 'reviewed_at' => 'datetime', 'approved_at' => 'datetime'];
    }

    public function deliverable()
    {
        return $this->belongsTo(Deliverable::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files()
    {
        return $this->hasMany(DeliverableFile::class);
    }

    public function reviews()
    {
        return $this->hasMany(DeliverableReview::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function corrections()
    {
        return $this->hasMany(CorrectionRequest::class);
    }

    public function isEditable(): bool
    {
        return $this->status === DeliverableVersionStatus::DRAFT;
    }
}
