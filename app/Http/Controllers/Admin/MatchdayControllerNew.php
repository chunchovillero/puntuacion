<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matchday;
use App\Models\Championship;
use App\Models\Club;
use App\Models\Category;
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
        if ($request->has('championship_id') && $request->get('championship_id') !== '') {
            $query->where('championship_id', $request->get('championship_id'));
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
        
        $matchdays = $query->orderBy('date', 'desc')->paginate(15);
        $championships = Championship::active()->orderBy('year', 'desc')->orderBy('name')->get();
        
        return view('admin.matchdays.index', compact('matchdays', 'championships'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $championshipId = $request->get('championship_id');
        $championships = Championship::active()->orderBy('year', 'desc')->orderBy('name')->get();
        $clubs = Club::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('type')->orderBy('name')->get();
        
        return view('admin.matchdays.create', compact('championships', 'clubs', 'categories', 'championshipId'));
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
            'organizer_name' => 'nullable|string|max:100',
            'organizer_contact' => 'nullable|string|max:100',
            'organizer_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'entry_fee' => 'nullable|numeric|min:0',
            'requirements' => 'nullable|string',
        ]);

        // Verificar que no exista otra jornada con el mismo número en el campeonato
        $existing = Matchday::where('championship_id', $validated['championship_id'])
                           ->where('number', $validated['number'])
                           ->exists();
        
        if ($existing) {
            return back()->withErrors(['number' => 'Ya existe una jornada con este número en el campeonato seleccionado.'])
                        ->withInput();
        }

        // Si no se especifica organizador, usar AMBMX por defecto
        if (empty($validated['organizer_club_id']) && empty($validated['organizer_name'])) {
            $validated['organizer_name'] = 'AMBMX';
        }

        // Convertir categorías a JSON si existen
        if (isset($validated['categories'])) {
            $validated['categories'] = $validated['categories'];
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
        $championships = Championship::active()->orderBy('year', 'desc')->orderBy('name')->get();
        $clubs = Club::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('type')->orderBy('name')->get();
        
        return view('admin.matchdays.edit', compact('matchday', 'championships', 'clubs', 'categories'));
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
            'organizer_name' => 'nullable|string|max:100',
            'organizer_contact' => 'nullable|string|max:100',
            'organizer_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'entry_fee' => 'nullable|numeric|min:0',
            'requirements' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
            'results' => 'nullable|string',
        ]);

        // Verificar que no exista otra jornada con el mismo número en el campeonato
        $existing = Matchday::where('championship_id', $validated['championship_id'])
                           ->where('number', $validated['number'])
                           ->where('id', '!=', $matchday->id)
                           ->exists();
        
        if ($existing) {
            return back()->withErrors(['number' => 'Ya existe otra jornada con este número en el campeonato seleccionado.'])
                        ->withInput();
        }

        // Si no se especifica organizador, usar AMBMX por defecto
        if (empty($validated['organizer_club_id']) && empty($validated['organizer_name'])) {
            $validated['organizer_name'] = 'AMBMX';
        }

        $matchday->update($validated);

        return redirect()->route('admin.championships.show', $matchday->championship_id)
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
