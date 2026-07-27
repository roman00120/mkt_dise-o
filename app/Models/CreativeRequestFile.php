<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreativeRequestFile extends Model
{
    use SoftDeletes;

    protected $fillable = ['creative_request_id', 'uploaded_by', 'original_name', 'stored_name', 'disk', 'path', 'mime_type', 'extension', 'size', 'category', 'description'];

    public function request()
    {
        return $this->belongsTo(CreativeRequest::class, 'creative_request_id');
    }
}
