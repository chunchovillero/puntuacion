<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilot;
use App\Models\Championship;
use App\Models\Matchday;
use App\Models\User;
use App\Models\ActivityLog;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        // Estadísticas básicas
        $stats = [
            'pilots' => Pilot::count(),
            'championships' => Championship::count(),
            'matchdays' => Matchday::count(),
            'users' => User::count(),
        ];

        // Actividad reciente (últimos 10 logs)
        $recentActivity = ActivityLog::with('user')
                                   ->orderBy('created_at', 'desc')
                                   ->take(10)
                                   ->get();

        // Próximas jornadas
        $upcomingMatchdays = Matchday::where('date', '>=', now()->toDateString())
                                   ->with('championship')
                                   ->orderBy('date')
                                   ->take(5)
                                   ->get();

        return view('admin.dashboard', compact('stats', 'recentActivity', 'upcomingMatchdays'));
    }
}
