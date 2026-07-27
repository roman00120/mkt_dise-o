<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use App\Models\CreativeRequestFile;
use App\Services\Requests\RequestFileService;
use Illuminate\Http\Request;

class RequestFileController extends Controller
{
    public function store(Request $request, CreativeRequest $creativeRequest, RequestFileService $files)
    {
        $this->authorize('update', $creativeRequest);
        $request->validate(['file' => ['required', 'file', 'max:25600', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,zip'], 'category' => ['nullable', 'in:reference,technical,brief']]);
        $files->store($creativeRequest, $request->file('file'), $request->string('category', 'reference')->toString());

        return back()->with('status', 'Archivo agregado');
    }

    public function destroy(CreativeRequest $creativeRequest, CreativeRequestFile $file, RequestFileService $files)
    {
        abort_unless($file->creative_request_id === $creativeRequest->id, 404);
        $this->authorize('deleteFile', $creativeRequest);
        $files->delete($file);

        return back()->with('status', 'Archivo eliminado');
    }
}
