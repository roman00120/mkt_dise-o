<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\CreativeRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DraftController extends Controller
{
    public function index(Request $request): View
    {
        $drafts = CreativeRequest::where('requester_id', $request->user()->id)->where('status', 'draft')->latest('updated_at')->get();

        return view('requests.drafts', compact('drafts'));
    }
}
