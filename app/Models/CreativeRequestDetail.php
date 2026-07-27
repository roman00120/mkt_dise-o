<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreativeRequestDetail extends Model
{
    protected $fillable = ['creative_request_id', 'data'];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }

    public function request()
    {
        return $this->belongsTo(CreativeRequest::class, 'creative_request_id');
    }
}
