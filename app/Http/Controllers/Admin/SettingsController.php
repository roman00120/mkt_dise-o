<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSystemSettingsRequest;
use App\Services\Settings\SystemSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(SystemSettingService $settings): View
    {
        return view('admin.settings.index', ['settings' => $settings->all()]);
    }

    public function update(UpdateSystemSettingsRequest $request, SystemSettingService $settings): RedirectResponse
    {
        $settings->update($request->validated(), $request->user());

        return back()->with('status', 'Configuración actualizada.');
    }
}
