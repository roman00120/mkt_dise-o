<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentRevision extends Model
{
    use HasFactory;

    protected $fillable = ['comment_id', 'edited_by', 'previous_body'];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
