<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        // Ambil data log terbaru beserta relasi usernya, dengan paginasi 50 data per halaman
        $logs = AuditLog::with('user')->latest()->paginate(50);
        return view('audit_logs.index', compact('logs'));
    }
}
