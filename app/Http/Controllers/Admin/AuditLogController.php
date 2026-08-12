<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display the platform-wide admin audit log.
     */
    public function index(Request $request)
    {
        $query = AdminAuditLog::with('admin');

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('event', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        $eventTypes    = AdminAuditLog::select('event')->distinct()->orderBy('event')->pluck('event');
        $totalActions  = AdminAuditLog::count();
        $actionsToday  = AdminAuditLog::whereDate('created_at', today())->count();
        $uniqueEvents  = AdminAuditLog::select('event')->distinct()->count();
        $lastActivity  = AdminAuditLog::latest()->first();

        return view('admin.audit_log', compact(
            'logs',
            'eventTypes',
            'totalActions',
            'actionsToday',
            'uniqueEvents',
            'lastActivity'
        ));
    }

    /**
     * Show a single audit log entry with recorded state changes.
     */
    public function show($id)
    {
        $log = AdminAuditLog::with('admin')->findOrFail($id);

        return view('admin.audit_log_detail', compact('log'));
    }
}