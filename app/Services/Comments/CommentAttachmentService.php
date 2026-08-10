<?php

namespace App\Services\Comments;

use App\Models\Comment;
use App\Models\CommentAttachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommentAttachmentService
{
    public const MAX_BYTES = 15 * 1024 * 1024;

    public const ALLOWED = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip'];

    public function store(Comment $comment, User $user, UploadedFile $file): CommentAttachment
    {
        abort_unless($comment->user_id === $user->id && ! $comment->trashed(), 403);
        abort_if($comment->attachments()->count() >= 5, 422, 'Puedes adjuntar hasta 5 archivos por comentario.');
        abort_if($file->getSize() > self::MAX_BYTES, 422, 'El archivo supera el límite de 15 MB.');
        $extension = strtolower($file->getClientOriginalExtension());
        abort_unless(in_array($extension, self::ALLOWED, true), 422);
        $mime = (string) $file->getMimeType();
        abort_if($mime === '' || $mime === 'application/octet-stream', 422, 'El tipo de archivo no es reconocible.');

        return DB::transaction(function () use ($comment, $user, $file, $extension, $mime): CommentAttachment {
            $stored = $file->store('comments/'.$comment->uuid, 'local');

            return $comment->attachments()->create(['uuid' => (string) Str::uuid(), 'uploaded_by' => $user->id, 'original_name' => $file->getClientOriginalName(), 'stored_name' => basename($stored), 'disk' => 'local', 'path' => $stored, 'mime_type' => $mime, 'extension' => $extension, 'size' => $file->getSize(), 'checksum' => hash_file('sha256', $file->getRealPath())]);
        });
    }

    public function delete(CommentAttachment $attachment): void
    {
        Storage::disk($attachment->disk)->delete($attachment->path);
        $attachment->delete();
    }
}
