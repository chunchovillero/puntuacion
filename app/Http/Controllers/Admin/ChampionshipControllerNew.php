<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Championship;
use App\Models\Club;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChampionshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Championship::withCount(['matchdays', 'completedMatchdays']);
        
        // Filtros
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('year') && $request->get('year') !== '') {
            $query->where('year', $request->get('year'));
        }
        
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }
        
        $championships = $query->orderBy('year', 'desc')->orderBy('name')->paginate(15);
        $availableYears = Championship::getAvailableYears();
        
        return view('admin.championships.index', compact('championships', 'availableYears'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.championships.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'year' => 'required|integer|min:2020|max:2030',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'entry_fee' => 'nullable|numeric|min:0',
            'prizes' => 'nullable|string',
            'active' => 'boolean'
        ], [
            'name.required' => 'El nombre del campeonato es obligatorio.',
            'year.required' => 'El año es obligatorio.',
            'year.min' => 'El año debe ser 2020 o posterior.',
            'year.max' => 'El año no puede ser mayor a 2030.',
            'start_date.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ]);

        // Verificar que no exista ya un campeonato con el mismo nombre y año
        $existing = Championship::where('name', $validated['name'])
                                ->where('year', $validated['year'])
                                ->exists();
        
        if ($existing) {
            return back()->withErrors(['name' => 'Ya existe un campeonato con este nombre para el año ' . $validated['year']])
                        ->withInput();
        }

        Championship::create($validated);

        return redirect()->route('admin.championships.index')
                        ->with('success', 'Campeonato creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Championship $championship)
    {
        $championship->load(['matchdays.organizerClub']);
        return view('admin.championships.show', compact('championship'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Championship $championship)
    {
        return view('admin.championships.edit', compact('championship'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Championship $championship)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'year' => 'required|integer|min:2020|max:2030',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'entry_fee' => 'nullable|numeric|min:0',
            'prizes' => 'nullable|string',
            'status' => 'required|in:planned,active,completed,cancelled',
            'active' => 'boolean'
        ]);

        // Verificar que no exista ya otro campeonato con el mismo nombre y año
        $existing = Championship::where('name', $validated['name'])
                                ->where('year', $validated['year'])
                                ->where('id', '!=', $championship->id)
                                ->exists();
        
        if ($existing) {
            return back()->withErrors(['name' => 'Ya existe otro campeonato con este nombre para el año ' . $validated['year']])
                        ->withInput();
        }

        $championship->update($validated);

        return redirect()->route('admin.championships.index')
                        ->with('success', 'Campeonato actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Championship $championship)
    {
        // Verificar si hay jornadas asociadas
        if ($championship->matchdays()->count() > 0) {
            return redirect()->route('admin.championships.index')
                            ->with('error', 'No se puede eliminar el campeonato porque tiene jornadas asociadas.');
        }

        $championship->delete();

        return redirect()->route('admin.championships.index')
                        ->with('success', 'Campeonato eliminado exitosamente.');
    }
}
