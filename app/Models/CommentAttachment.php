<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['uuid', 'comment_id', 'uploaded_by', 'original_name', 'stored_name', 'disk', 'path', 'mime_type', 'extension', 'size', 'checksum'];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
