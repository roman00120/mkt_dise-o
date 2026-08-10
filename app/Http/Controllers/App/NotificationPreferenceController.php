<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNotificationPreferenceRequest;
use App\Services\Notifications\NotificationPreferenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationPreferenceController extends Controller
{
    public function edit(Request $request): View
    {
        return view('notifications.preferences', ['preferences' => $request->user()->notificationPreferences()->get()->keyBy('event_type')]);
    }

    public function update(UpdateNotificationPreferenceRequest $request, NotificationPreferenceService $preferences): RedirectResponse
    {
        $preferences->save($request->user(), $request->validated('preferences', []));

        return back()->with('status', 'Preferencias guardadas.');
    }
}
