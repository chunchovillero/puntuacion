<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', [AdminController::class, 'dashboard'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Public dashboard - visible to everyone
Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

// Public read-only routes (anyone can view) - URLs en español
Route::name('admin.')->group(function () {
    // View-only routes for clubs
    Route::get('clubes', [\App\Http\Controllers\Admin\ClubController::class, 'index'])->name('clubs.index');
    Route::get('clubes/{club}', [\App\Http\Controllers\Admin\ClubController::class, 'show'])->name('clubs.show');
    
    // View-only routes for categories
    Route::get('categorias', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::get('categorias/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'show'])->name('categories.show');
    
    // View-only routes for pilots
    Route::get('pilotos', [\App\Http\Controllers\Admin\PilotController::class, 'index'])->name('pilots.index');
    Route::get('pilotos/{pilot}', [\App\Http\Controllers\Admin\PilotController::class, 'show'])->name('pilots.show');
    Route::get('clubes/{club}/pilotos', [\App\Http\Controllers\Admin\PilotController::class, 'byClub'])->name('pilots.by-club');
    
    // View-only routes for championships
    Route::get('campeonatos', [\App\Http\Controllers\Admin\ChampionshipController::class, 'index'])->name('championships.index');
    Route::get('campeonatos/{championship}', [\App\Http\Controllers\Admin\ChampionshipController::class, 'show'])->name('championships.show');
    
    // View-only routes for matchdays
    Route::get('jornadas', [\App\Http\Controllers\Admin\MatchdayController::class, 'index'])->name('matchdays.index');
    Route::get('jornadas/{matchday}', [\App\Http\Controllers\Admin\MatchdayController::class, 'show'])->name('matchdays.show');
});

// Authentication routes (only login, no registration)
Auth::routes(['register' => false]);

// Admin-only routes (create, edit, update, delete) - URLs en español
Route::middleware(['auth'])->prefix('gestionar')->name('admin.')->group(function () {
    // User management routes (only for authenticated admins)
    Route::resource('usuarios', \App\Http\Controllers\Admin\UserController::class)->parameters(['usuarios' => 'user']);
    
    // Club admin routes
    Route::get('clubes/crear', [\App\Http\Controllers\Admin\ClubController::class, 'create'])->name('clubs.create');
    Route::post('clubes', [\App\Http\Controllers\Admin\ClubController::class, 'store'])->name('clubs.store');
    Route::get('clubes/{club}/editar', [\App\Http\Controllers\Admin\ClubController::class, 'edit'])->name('clubs.edit');
    Route::put('clubes/{club}', [\App\Http\Controllers\Admin\ClubController::class, 'update'])->name('clubs.update');
    Route::delete('clubes/{club}', [\App\Http\Controllers\Admin\ClubController::class, 'destroy'])->name('clubs.destroy');
    
    // Category admin routes
    Route::get('categorias/crear', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create');
    Route::post('categorias', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::get('categorias/{category}/editar', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categorias/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categorias/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('categorias/{category}/cambiar-estado', [\App\Http\Controllers\Admin\CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    
    // Pilot admin routes
    Route::get('pilotos/crear', [\App\Http\Controllers\Admin\PilotController::class, 'create'])->name('pilots.create');
    Route::post('pilotos', [\App\Http\Controllers\Admin\PilotController::class, 'store'])->name('pilots.store');
    Route::get('pilotos/{pilot}/editar', [\App\Http\Controllers\Admin\PilotController::class, 'edit'])->name('pilots.edit');
    Route::put('pilotos/{pilot}', [\App\Http\Controllers\Admin\PilotController::class, 'update'])->name('pilots.update');
    Route::delete('pilotos/{pilot}', [\App\Http\Controllers\Admin\PilotController::class, 'destroy'])->name('pilots.destroy');
    
    // Championship admin routes
    Route::get('campeonatos/crear', [\App\Http\Controllers\Admin\ChampionshipController::class, 'create'])->name('championships.create');
    Route::post('campeonatos', [\App\Http\Controllers\Admin\ChampionshipController::class, 'store'])->name('championships.store');
    Route::get('campeonatos/{championship}/editar', [\App\Http\Controllers\Admin\ChampionshipController::class, 'edit'])->name('championships.edit');
    Route::put('campeonatos/{championship}', [\App\Http\Controllers\Admin\ChampionshipController::class, 'update'])->name('championships.update');
    Route::delete('campeonatos/{championship}', [\App\Http\Controllers\Admin\ChampionshipController::class, 'destroy'])->name('championships.destroy');
    
    // Matchday admin routes
    Route::get('jornadas/crear', [\App\Http\Controllers\Admin\MatchdayController::class, 'create'])->name('matchdays.create');
    Route::post('jornadas', [\App\Http\Controllers\Admin\MatchdayController::class, 'store'])->name('matchdays.store');
    Route::get('jornadas/{matchday}/editar', [\App\Http\Controllers\Admin\MatchdayController::class, 'edit'])->name('matchdays.edit');
    Route::put('jornadas/{matchday}', [\App\Http\Controllers\Admin\MatchdayController::class, 'update'])->name('matchdays.update');
    Route::delete('jornadas/{matchday}', [\App\Http\Controllers\Admin\MatchdayController::class, 'destroy'])->name('matchdays.destroy');
});
