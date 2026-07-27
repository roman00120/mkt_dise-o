<?php

namespace App\Services\Requests;

use App\Models\CreativeRequest;
use App\Models\CreativeRequestFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RequestFileService
{
    public const MAX_SIZE = 25600;

    public const MIME = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'mp4', 'mov', 'zip'];

    public function store(CreativeRequest $request, UploadedFile $file, string $category = 'reference'): CreativeRequestFile
    {
        $extension = strtolower($file->extension());
        abort_unless(in_array($extension, self::MIME, true), 422);
        abort_if($file->getSize() > self::MAX_SIZE * 1024, 422);
        $path = $file->store('creative-requests/'.$request->uuid, 'local');

        return $request->files()->create(['uploaded_by' => auth()->id(), 'original_name' => $file->getClientOriginalName(), 'stored_name' => basename($path), 'disk' => 'local', 'path' => $path, 'mime_type' => $file->getMimeType(), 'extension' => $extension, 'size' => $file->getSize(), 'category' => in_array($category, ['reference', 'technical', 'brief'], true) ? $category : 'reference']);
    }

    public function delete(CreativeRequestFile $file): void
    {
        Storage::disk($file->disk)->delete($file->path);
        $file->delete();
    }
}
