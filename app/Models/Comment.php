<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['uuid', 'commentable_type', 'commentable_id', 'user_id', 'parent_id', 'visibility', 'body', 'edited_at', 'deleted_by'];

    protected function casts(): array
    {
        return ['edited_at' => 'datetime'];
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')->oldest();
    }

    public function mentions()
    {
        return $this->hasMany(CommentMention::class);
    }

    public function attachments()
    {
        return $this->hasMany(CommentAttachment::class);
    }

    public function revisions()
    {
        return $this->hasMany(CommentRevision::class);
    }

    public function isInternal(): bool
    {
        return $this->visibility === 'internal';
    }
}
