<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    /**
     * Tampilkan log untuk suatu entitas tertentu.
     */
    public function index(Request $request)
{
    $query = \App\Models\AuditLog::with('user');

    // Optional filter by model_type, user, action
    if ($request->filled('model_type')) {
        $query->where('model_type', $request->model_type);
    }
    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }
    if ($request->filled('aksi')) {
        $query->where('aksi', $request->aksi);
    }

    $logs = $query->orderByDesc('created_at')->paginate(20);

    // Untuk dropdown filter
    $modelTypes = \App\Models\AuditLog::select('model_type')->distinct()->pluck('model_type');
    $users = \App\Models\User::pluck('name', 'id');
    $aksiTypes = \App\Models\AuditLog::select('aksi')->distinct()->pluck('aksi');

    return view('backend.auditlog.index', compact('logs', 'modelTypes', 'users', 'aksiTypes'));
}
}
