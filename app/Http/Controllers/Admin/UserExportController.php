<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Exports\UserExportService;

class UserExportController extends Controller
{
    public function __invoke(UserExportService $export)
    {
        return $export->export(request()->query(), request()->user());
    }
}
