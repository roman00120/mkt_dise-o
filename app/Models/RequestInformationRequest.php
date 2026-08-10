<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestInformationRequest extends Model
{
    protected $fillable = ['creative_request_id', 'requested_by', 'message', 'category', 'previous_status', 'responded_by', 'response', 'requested_at', 'responded_at', 'status'];

    protected function casts(): array
    {
        return ['requested_at' => 'datetime', 'responded_at' => 'datetime'];
    }

    public function request()
    {
        return $this->belongsTo(CreativeRequest::class, 'creative_request_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}
