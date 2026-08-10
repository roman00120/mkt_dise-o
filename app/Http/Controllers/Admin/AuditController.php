<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuditLogIndexRequest;
use App\Models\User;
use App\Queries\AuditLogQuery;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(AuditLogIndexRequest $request, AuditLogQuery $query): View
    {
        return view('admin.audit.index', ['logs' => $query->paginate($request->validated()), 'filters' => $request->validated(), 'actors' => User::where('role', 'admin')->orderBy('name')->get()]);
    }
}
