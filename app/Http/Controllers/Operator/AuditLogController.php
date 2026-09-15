<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\OperatorAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    /**
     * Display the audit log for the authenticated operator.
     */
    public function index(Request $request)
    {
        $operator = $this->getOperator();

        $query = OperatorAuditLog::with('operator')
            ->where('operator_id', $operator->id);

        // Filter by event type
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Date range filter
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Search in description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('description', 'like', "%{$search}%");
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        // Get distinct event types for the filter dropdown
        $eventTypes = OperatorAuditLog::where('operator_id', $operator->id)
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        // Summary stats
        $totalActions = OperatorAuditLog::where('operator_id', $operator->id)->count();
        $actionsToday = OperatorAuditLog::where('operator_id', $operator->id)
            ->whereDate('created_at', today())->count();
        $uniqueEvents = OperatorAuditLog::where('operator_id', $operator->id)
            ->select('event')->distinct()->count();
        $lastActivity = OperatorAuditLog::where('operator_id', $operator->id)
            ->latest()->first();

        return view('operator.audit_log', compact(
            'operator', 'logs', 'eventTypes',
            'totalActions', 'actionsToday', 'uniqueEvents', 'lastActivity'
        ));
    }

    /**
     * Display details of a single audit log entry.
     */
    public function show($id)
    {
        $operator = $this->getOperator();
        $log = OperatorAuditLog::with('operator')
            ->where('operator_id', $operator->id)
            ->findOrFail($id);

        return view('operator.audit_log_detail', compact('operator', 'log'));
    }

    private function getOperator()
    {
        return Auth::guard('operator')->user();
    }
}