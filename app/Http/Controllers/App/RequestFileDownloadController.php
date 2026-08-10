<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use App\Models\CreativeRequestFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class RequestFileDownloadController extends Controller
{
    public function __invoke(Request $request, CreativeRequest $creativeRequest, CreativeRequestFile $file): Response
    {
        $this->authorize('downloadFile', $creativeRequest);
        abort_unless($file->creative_request_id === $creativeRequest->id, 404);
        abort_unless(Storage::disk($file->disk)->exists($file->path), 404, 'El archivo ya no está disponible.');

        if ($request->boolean('inline')) {
            return Storage::disk($file->disk)->response($file->path, basename($file->original_name), [
                'Content-Type' => $file->mime_type ?? 'image/jpeg',
                'Content-Disposition' => 'inline',
            ]);
        }

        return Storage::disk($file->disk)->download($file->path, basename($file->original_name), ['Content-Type' => $file->mime_type]);
    }
}
