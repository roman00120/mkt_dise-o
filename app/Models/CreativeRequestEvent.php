<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreativeRequestEvent extends Model
{
    public $timestamps = false;

    protected $fillable = ['creative_request_id', 'actor_id', 'event', 'metadata', 'created_at'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'created_at' => 'datetime'];
    }
}
