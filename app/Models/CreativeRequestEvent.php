<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreativeRequestEvent extends Model
{
    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (self $event): void {
            $event->created_at ??= now();
        });
    }

    protected $fillable = ['creative_request_id', 'actor_id', 'event', 'metadata', 'created_at'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'created_at' => 'datetime'];
    }

    public function request()
    {
        return $this->belongsTo(CreativeRequest::class, 'creative_request_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
