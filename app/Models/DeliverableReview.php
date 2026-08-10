<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverableReview extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'deliverable_version_id', 'reviewer_id', 'review_type', 'decision', 'comments'];

    public function version()
    {
        return $this->belongsTo(DeliverableVersion::class, 'deliverable_version_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
