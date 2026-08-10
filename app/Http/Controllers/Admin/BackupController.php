<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Backups\BackupService;
use App\Services\Backups\BackupVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BackupController extends Controller
{
    public function index(): View
    {
        return view('admin.system.backups', ['files' => Storage::disk(env('BACKUP_DISK', 'backups'))->allFiles()]);
    }

    public function store(BackupService $service): RedirectResponse
    {
        $service->all(request()->user());

        return back()->with('status', 'Backup generado en almacenamiento privado.');
    }

    public function verify(string $backup, BackupVerificationService $service): RedirectResponse
    {
        $result = $service->verify($backup);

        return back()->with('status', ($result[$backup]['valid'] ?? false) ? 'Backup verificado.' : 'Backup no válido.');
    }
}
