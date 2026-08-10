<?php

namespace App\Services\Deliverables;

use App\Models\DeliverableFile;
use App\Models\DeliverableVersion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeliverableFileService
{
    public const MAX_BYTES = 104857600;

    public function store(DeliverableVersion $version, UploadedFile $file, string $category, bool $primary = false, ?string $description = null): DeliverableFile
    {
        $extension = strtolower($file->extension());
        $allowed = match ($version->deliverable->request->service->value) {
            'design' => ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'ai', 'eps', 'psd', 'svg', 'zip'], 'video' => ['mp4', 'mov', 'webm', 'jpg', 'png', 'pdf', 'zip'], default => ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'mp4', 'obj', 'fbx', 'stl', 'zip']
        };
        abort_unless(in_array($extension, $allowed, true), 422, 'Tipo de archivo no permitido.');
        abort_if($file->getSize() > self::MAX_BYTES, 422, 'El archivo supera el límite permitido.');
        $path = $file->store('deliverables/'.$version->uuid, 'local');
        if ($primary) {
            $version->files()->where('is_primary', true)->update(['is_primary' => false]);
        }
        $stored = $version->files()->create(['uuid' => (string) Str::uuid(), 'uploaded_by' => auth()->id(), 'original_name' => $file->getClientOriginalName(), 'stored_name' => basename($path), 'disk' => 'local', 'path' => $path, 'mime_type' => $file->getMimeType(), 'extension' => $extension, 'size' => $file->getSize(), 'checksum' => hash_file('sha256', $file->getRealPath()), 'category' => in_array($category, ['preview', 'source', 'final', 'supporting', 'compressed'], true) ? $category : 'supporting', 'description' => $description, 'is_primary' => $primary]);
        $version->deliverable->request->events()->create(['actor_id' => auth()->id(), 'event' => 'deliverable_file_uploaded', 'metadata' => ['version_number' => $version->version_number, 'name' => $stored->original_name]]);

        return $stored;
    }

    public function delete(DeliverableFile $file): void
    {
        Storage::disk($file->disk)->delete($file->path);
        $file->delete();
    }
}
