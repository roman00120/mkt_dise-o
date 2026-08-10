<?php

namespace App\Http\Controllers\Creative;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadDeliverableFileRequest;
use App\Models\Deliverable;
use App\Models\DeliverableFile;
use App\Models\DeliverableVersion;
use App\Services\Deliverables\DeliverableFileService;
use Illuminate\Http\RedirectResponse;

class DeliverableFileController extends Controller
{
    public function store(UploadDeliverableFileRequest $request, Deliverable $deliverable, DeliverableVersion $version, DeliverableFileService $files): RedirectResponse
    {
        abort_unless($version->deliverable_id === $deliverable->id, 404);
        $files->store($version, $request->file('file'), $request->validated('category'), (bool) $request->validated('is_primary'), $request->validated('description'));

        return back()->with('status', 'Archivo agregado a la versión.');
    }

    public function destroy(Deliverable $deliverable, DeliverableVersion $version, DeliverableFile $file, DeliverableFileService $files): RedirectResponse
    {
        abort_unless($version->deliverable_id === $deliverable->id, 404);
        abort_unless($file->deliverable_version_id === $version->id, 404);
        $this->authorize('deleteFile', $version);
        $files->delete($file);

        return back()->with('status', 'Archivo eliminado.');
    }
}
