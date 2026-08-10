<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DraftController extends Controller
{
    public function index(Request $request): View
    {
        $drafts = CreativeRequest::where('requester_id', $request->user()->id)->where('status', 'draft')->latest('updated_at')->get();

        return view('requests.drafts', compact('drafts'));
    }

    public function destroy(CreativeRequest $creativeRequest, Request $request): RedirectResponse
    {
        abort_unless($creativeRequest->requester_id === $request->user()->id && $creativeRequest->isDraft(), 403);
        $creativeRequest->delete();

        return redirect()->route('app.requests.index')->with('status', 'El borrador fue eliminado exitosamente.');
    }
}
