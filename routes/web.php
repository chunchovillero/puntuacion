<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes - Vue.js Only
|--------------------------------------------------------------------------
|
| Simplified routes for Vue.js SPA application
|
*/

// Authentication routes (only login, no registration)
Auth::routes(['register' => false]);

// API Routes must come BEFORE the catch-all route
Route::prefix('api')->group(function () {
    // Test route
    Route::get('test', function () {
        return response()->json(['message' => 'API is working', 'timestamp' => now()]);
    });
    
    // Pilot API Routes
    Route::get('pilots', [\App\Http\Controllers\Admin\PilotController::class, 'apiIndex']);
    Route::get('pilots/{pilot}', [\App\Http\Controllers\Admin\PilotController::class, 'apiShow']);
    Route::get('pilots/stats', [\App\Http\Controllers\Admin\PilotController::class, 'apiStats']);
    Route::get('pilots/clubs', [\App\Http\Controllers\Admin\PilotController::class, 'apiClubs']);
    Route::get('pilots/export', [\App\Http\Controllers\Admin\PilotController::class, 'apiExport']);
    
    // Pilot API Routes (Protected - require authentication)
    Route::middleware('auth')->group(function () {
        Route::post('pilots', [\App\Http\Controllers\Admin\PilotController::class, 'apiStore']);
        Route::put('pilots/{pilot}', [\App\Http\Controllers\Admin\PilotController::class, 'apiUpdate']);
        Route::delete('pilots/{pilot}', [\App\Http\Controllers\Admin\PilotController::class, 'apiDestroy']);
    });
    
    // Club API Routes
    Route::get('clubs', [\App\Http\Controllers\Admin\ClubController::class, 'apiIndex']);
    Route::get('clubs/{club}', [\App\Http\Controllers\Admin\ClubController::class, 'apiShow']);
    
    // Category API Routes
    Route::get('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'apiIndex']);
    
    // Championship API Routes
    Route::get('championships', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiIndex']);
    
    // Matchday API Routes
    Route::get('matchdays', [\App\Http\Controllers\Admin\MatchdayController::class, 'apiIndex']);
});

// Public routes for displaying information (before catch-all) - serve SPA with initial data
Route::get('/clubes', function () {
    $clubs = \App\Models\Club::where('status', 'active')->get();
    
    return view('app')->with('initialData', [
        'clubs' => $clubs,
        'page' => 'clubs-list'
    ]);
})->name('public.clubs.index');

Route::get('/clubes/{club}', [\App\Http\Controllers\Admin\ClubController::class, 'show'])->name('public.clubs.show');
Route::get('/clubes/{club}/pilotos', [\App\Http\Controllers\Admin\PilotController::class, 'byClub'])->name('public.clubs.pilots');

Route::get('/pilotos', function () {
    // Solo mostrar pilotos activos en vista pública y que pertenezcan a clubes activos
    $pilots = \App\Models\Pilot::with(['club'])
        ->where('status', 'active')
        ->whereHas('club', function($query) {
            $query->where('status', 'active');
        })
        ->paginate(15);
    $clubs = \App\Models\Club::where('status', 'active')->orderBy('name')->get();
    
    return view('app')->with('initialData', [
        'pilots' => $pilots,
        'clubs' => $clubs,
        'page' => 'pilots-list'
    ]);
})->name('public.pilots.index');

Route::get('/pilotos/{pilot}', function (\App\Models\Pilot $pilot) {
    // Verificar que el piloto esté activo y su club también
    if ($pilot->status !== 'active') {
        abort(404);
    }
    
    if ($pilot->club && $pilot->club->status !== 'active') {
        abort(404);
    }
    
    $pilot->load(['club']);
    
    return view('app')->with('initialData', [
        'pilot' => $pilot,
        'page' => 'pilot-detail'
    ]);
})->name('public.pilots.show');

Route::get('/categorias', function () {
    $categories = \App\Models\Category::orderBy('name')->get();
    
    return view('app')->with('initialData', [
        'categories' => $categories,
        'page' => 'categories-list'
    ]);
})->name('public.categories.index');
Route::get('/categorias/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'show'])->name('public.categories.show');

Route::get('/campeonatos', function () {
    $championships = \App\Models\Championship::orderBy('created_at', 'desc')->get();
    
    return view('app')->with('initialData', [
        'championships' => $championships,
        'page' => 'championships-list'
    ]);
})->name('public.championships.index');
Route::get('/campeonatos/{championship}', [\App\Http\Controllers\Admin\ChampionshipController::class, 'show'])->name('public.championships.show');

Route::get('/jornadas', function () {
    $matchdays = \App\Models\Matchday::with(['championship'])
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('app')->with('initialData', [
        'matchdays' => $matchdays,
        'page' => 'matchdays-list'
    ]);
})->name('public.matchdays.index');
Route::get('/jornadas/{matchday}', [\App\Http\Controllers\Admin\MatchdayController::class, 'show'])->name('public.matchdays.show');

// Main Vue.js application route - catch all other routes and let Vue handle routing
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*')->name('vue.app');
