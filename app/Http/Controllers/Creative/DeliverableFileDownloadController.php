<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Models\Deliverable;
use App\Models\DeliverableFile;
use App\Models\DeliverableVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DeliverableFileDownloadController extends Controller
{
    public function __invoke(Request $request, Deliverable $deliverable, DeliverableVersion $version, DeliverableFile $file): Response
    {
        abort_unless($version->deliverable_id === $deliverable->id, 404);
        abort_unless($file->deliverable_version_id === $version->id, 404);
        $this->authorize('download', $file);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        if ($request->boolean('inline')) {
            return Storage::disk($file->disk)->response($file->path, basename($file->original_name), [
                'Content-Type' => $file->mime_type ?? 'image/jpeg',
                'Content-Disposition' => 'inline',
            ]);
        }

        return Storage::disk($file->disk)->download($file->path, basename($file->original_name), [
            'Content-Type' => $file->mime_type,
        ]);
    }
}
