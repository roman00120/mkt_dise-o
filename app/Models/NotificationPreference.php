<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory;

    public const TYPES = ['assignment', 'mention', 'comment', 'information_request', 'deliverable_review', 'corrections', 'approval', 'completion'];

    public const CRITICAL = ['assignment', 'information_request', 'corrections'];

    protected $fillable = ['user_id', 'event_type', 'in_app', 'email'];

    protected function casts(): array
    {
        return ['in_app' => 'boolean', 'email' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
