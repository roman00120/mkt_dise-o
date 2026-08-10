<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\CommentAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CommentAttachmentFactory extends Factory
{
    protected $model = CommentAttachment::class;

    public function definition(): array
    {
        return ['uuid' => (string) Str::uuid(), 'comment_id' => Comment::factory(), 'uploaded_by' => User::factory(), 'original_name' => 'reference.pdf', 'stored_name' => 'reference.pdf', 'disk' => 'local', 'path' => 'comments/reference.pdf', 'mime_type' => 'application/pdf', 'extension' => 'pdf', 'size' => 1024, 'checksum' => hash('sha256', 'reference')];
    }
}
