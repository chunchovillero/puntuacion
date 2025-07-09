<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');
        
        // Filtros
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->paginate(50);
        
        // Para los filtros
        $users = User::orderBy('name')->get();
        $actions = ActivityLog::distinct()->pluck('action');
        $modelTypes = ActivityLog::distinct()->pluck('model_type')->map(function($type) {
            return [
                'value' => $type,
                'label' => class_basename($type)
            ];
        });
        
        return view('admin.activity-logs.index', compact('logs', 'users', 'actions', 'modelTypes'));
    }

    /**
     * Vue.js index view
     */
    public function vueIndex()
    {
        return view('admin.activity-logs.vue-index');
    }

    /**
     * API: Get activity logs with pagination and filters
     */
    public function apiIndex(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->paginate(50);
        
        return response()->json($logs);
    }

    /**
     * API: Get filter data for dropdowns
     */
    public function apiFilterData()
    {
        $users = User::orderBy('name')->get(['id', 'name']);
        $actions = ActivityLog::distinct()->pluck('action');
        $modelTypes = ActivityLog::distinct()->pluck('model_type')->map(function($type) {
            return [
                'value' => $type,
                'label' => class_basename($type)
            ];
        })->values();
        
        return response()->json([
            'users' => $users,
            'actions' => $actions,
            'modelTypes' => $modelTypes
        ]);
    }

    /**
     * API: Export activity logs with filters
     */
    public function apiExport(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');
        
        // Apply the same filters as in index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->limit(1000)->get();
        
        $csvData = [];
        $csvData[] = ['Fecha', 'Usuario', 'Acción', 'Modelo', 'Descripción', 'IP'];
        
        foreach ($logs as $log) {
            $csvData[] = [
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user ? $log->user->name : 'Sistema',
                $log->action,
                class_basename($log->model_type),
                $log->description,
                $log->ip_address,
            ];
        }
        
        $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
