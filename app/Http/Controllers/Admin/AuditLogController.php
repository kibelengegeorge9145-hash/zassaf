<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::query()
            ->with('actor')
            ->latest('id')
            ->limit(200)
            ->get();

        return view('admin.audit.index', compact('logs'));
    }
}
