<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matchday;
use App\Models\Championship;
use App\Models\Club;
use Illuminate\Http\Request;

class MatchdayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Matchday::with(['championship', 'organizerClub']);
        
        // Filtros
        if ($request->has('championship') && $request->get('championship') !== '') {
            $query->where('championship_id', $request->get('championship'));
        }
        
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }
        
        if ($request->has('date_from') && $request->get('date_from') !== '') {
            $query->whereDate('date', '>=', $request->get('date_from'));
        }
        
        if ($request->has('date_to') && $request->get('date_to') !== '') {
            $query->whereDate('date', '<=', $request->get('date_to'));
        }
        
        $matchdays = $query->orderBy('date', 'desc')->orderBy('number', 'asc')->get();
        
        return view('admin.matchdays.index', compact('matchdays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $championships = Championship::orderBy('year', 'desc')->orderBy('name')->get();
        $clubs = Club::orderBy('name')->get();
        
        return view('admin.matchdays.create', compact('championships', 'clubs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'championship_id' => 'required|exists:championships,id',
            'number' => 'required|integer|min:1',
            'name' => 'nullable|string|max:100',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'required|string|max:200',
            'address' => 'nullable|string|max:300',
            'organizer_club_id' => 'nullable|exists:clubs,id',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
            'description' => 'nullable|string',
        ]);

        // Validar que el número de jornada sea único en el campeonato
        $exists = Matchday::where('championship_id', $validated['championship_id'])
                         ->where('number', $validated['number'])
                         ->exists();
        
        if ($exists) {
            return back()->withErrors(['number' => 'Ya existe una jornada con este número en el campeonato seleccionado.'])->withInput();
        }

        // Si se seleccionó organizador AMBMX, limpiar el club organizador
        if ($request->input('organizer_type') === 'ambmx') {
            $validated['organizer_club_id'] = null;
        }

        Matchday::create($validated);

        return redirect()->route('admin.championships.show', $validated['championship_id'])
                        ->with('success', 'Jornada creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Matchday $matchday)
    {
        $matchday->load(['championship', 'organizerClub']);
        return view('admin.matchdays.show', compact('matchday'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Matchday $matchday)
    {
        $championships = Championship::orderBy('year', 'desc')->orderBy('name')->get();
        $clubs = Club::orderBy('name')->get();
        
        return view('admin.matchdays.edit', compact('matchday', 'championships', 'clubs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Matchday $matchday)
    {
        $validated = $request->validate([
            'championship_id' => 'required|exists:championships,id',
            'number' => 'required|integer|min:1',
            'name' => 'nullable|string|max:100',
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'venue' => 'required|string|max:200',
            'address' => 'nullable|string|max:300',
            'organizer_club_id' => 'nullable|exists:clubs,id',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
            'description' => 'nullable|string',
        ]);

        // Verificar que no exista otra jornada con el mismo número en el campeonato
        $exists = Matchday::where('championship_id', $validated['championship_id'])
                         ->where('number', $validated['number'])
                         ->where('id', '!=', $matchday->id)
                         ->exists();
        
        if ($exists) {
            return back()->withErrors(['number' => 'Ya existe otra jornada con este número en el campeonato seleccionado.'])
                        ->withInput();
        }

        // Si se seleccionó organizador AMBMX, limpiar el club organizador
        if ($request->input('organizer_type') === 'ambmx') {
            $validated['organizer_club_id'] = null;
        }

        $matchday->update($validated);

        return redirect()->route('admin.matchdays.show', $matchday)
                        ->with('success', 'Jornada actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Matchday $matchday)
    {
        $championshipId = $matchday->championship_id;
        
        $matchday->delete();

        return redirect()->route('admin.championships.show', $championshipId)
                        ->with('success', 'Jornada eliminada exitosamente.');
    }
}
