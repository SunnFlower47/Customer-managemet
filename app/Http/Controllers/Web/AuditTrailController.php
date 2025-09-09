<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditTrailController extends Controller
{
    public function __construct()
    {
        // Middleware akan dihandle di routes
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AuditTrail::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('event', 'like', "%{$search}%")
                  ->orWhere('auditable_type', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // Filter by event
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by auditable type
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditTrails = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $events = AuditTrail::distinct()->pluck('event')->filter();
        $auditableTypes = AuditTrail::distinct()->pluck('auditable_type')->filter();
        $users = AuditTrail::with('user')->get()->pluck('user.name', 'user_id')->filter();

        return view('audit-trails.index', compact('auditTrails', 'events', 'auditableTypes', 'users'));
    }

    /**
     * Display the specified resource.
     */
    public function show(AuditTrail $auditTrail)
    {
        $auditTrail->load('user');
        return view('audit-trails.show', compact('auditTrail'));
    }

    /**
     * Export audit trails to Excel
     */
    public function export(Request $request)
    {
        try {
            $query = AuditTrail::query();

            // Apply same filters as index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('event', 'like', "%{$search}%")
                      ->orWhere('auditable_type', 'like', "%{$search}%")
                      ->orWhere('tags', 'like', "%{$search}%");
                });
            }

            if ($request->filled('event')) {
                $query->where('event', $request->event);
            }
            if ($request->filled('auditable_type')) {
                $query->where('auditable_type', $request->auditable_type);
            }
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $auditTrails = $query->orderBy('created_at', 'desc')->get();

            $filename = 'audit_trails_' . date('Y-m-d_H-i-s') . '.csv';

            // Create CSV content that Excel can open
            $csvContent = "ID,User ID,Event,Auditable Type,Auditable ID,Tags,IP Address,User Agent,Created At\n";
            
            foreach ($auditTrails as $audit) {
                $csvContent .= '"' . $audit->id . '",';
                $csvContent .= '"' . $audit->user_id . '",';
                $csvContent .= '"' . $audit->event . '",';
                $csvContent .= '"' . $audit->auditable_type . '",';
                $csvContent .= '"' . $audit->auditable_id . '",';
                $csvContent .= '"' . str_replace('"', '""', $audit->tags) . '",';
                $csvContent .= '"' . $audit->ip_address . '",';
                $csvContent .= '"' . str_replace('"', '""', $audit->user_agent) . '",';
                $csvContent .= '"' . $audit->created_at->format('Y-m-d H:i:s') . '"' . "\n";
            }

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ];

            return response($csvContent, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
}