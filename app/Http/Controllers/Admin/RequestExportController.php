<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Exports\RequestExportService;

class RequestExportController extends Controller
{
    public function __invoke(RequestExportService $export)
    {
        return $export->export(request()->query(), request()->user());
    }
}
