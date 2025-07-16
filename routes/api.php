<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PilotController;
use App\Http\Controllers\Admin\ClubController;
use App\Http\Controllers\Admin\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working', 'timestamp' => now()]);
});

// Pilot API Routes
Route::prefix('pilots')->group(function () {
    Route::get('/', [PilotController::class, 'apiIndex']);
    Route::get('/stats', [PilotController::class, 'apiStats']);
    Route::get('/clubs', [PilotController::class, 'apiClubs']);
    Route::get('/export', [PilotController::class, 'apiExport']);
    Route::get('/{pilot}', [PilotController::class, 'apiShow']);
    Route::post('/', [PilotController::class, 'apiStore']);
    Route::put('/{pilot}', [PilotController::class, 'apiUpdate']);
    Route::delete('/{pilot}', [PilotController::class, 'apiDestroy']);
    Route::patch('/{pilot}/reactivate', [PilotController::class, 'apiReactivate']);
});

// Club API Routes
Route::prefix('clubs')->group(function () {
    Route::get('/', [ClubController::class, 'apiIndex']);
    Route::get('/export', [ClubController::class, 'apiExport']);
    Route::get('/{club}', [ClubController::class, 'apiShow']);
    Route::get('/{club}/pilots', [PilotController::class, 'apiByClub']);
    Route::post('/', [ClubController::class, 'apiStore']);
    Route::put('/{club}', [ClubController::class, 'apiUpdate']);
    Route::delete('/{club}', [ClubController::class, 'apiDestroy']);
    Route::patch('/{club}/reactivate', [ClubController::class, 'apiReactivate']);
});

// Category API Routes
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'apiIndex']);
    Route::get('/export', [CategoryController::class, 'apiExport']);
});

// Matchday API Routes
Route::prefix('matchdays')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\MatchdayController::class, 'apiIndex']);
    Route::get('/export', [\App\Http\Controllers\Admin\MatchdayController::class, 'apiExport']);
    Route::get('/{matchday}', [\App\Http\Controllers\Admin\MatchdayController::class, 'apiShow']);
    Route::post('/', [\App\Http\Controllers\Admin\MatchdayController::class, 'apiStore']);
    Route::put('/{matchday}', [\App\Http\Controllers\Admin\MatchdayController::class, 'apiUpdate']);
    Route::delete('/{matchday}', [\App\Http\Controllers\Admin\MatchdayController::class, 'apiDestroy']);
    
    // Pilot management for matchdays
    Route::get('/{matchday}/available-pilots', [\App\Http\Controllers\Admin\MatchdayController::class, 'apiSearchPilotsForMatchday']);
    Route::post('/{matchday}/add-pilot', [\App\Http\Controllers\Admin\MatchdayController::class, 'apiAddPilotToMatchday']);
});

// Championship API Routes
Route::prefix('championships')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiIndex']);
    Route::get('/export', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiExport']);
    Route::get('/{championship}', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiShow']);
    Route::get('/{championship}/matchdays', [\App\Http\Controllers\Admin\MatchdayController::class, 'apiByChampionship']);
    Route::post('/', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiStore']);
    Route::put('/{championship}', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiUpdate']);
    Route::delete('/{championship}', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiDestroy']);
});
