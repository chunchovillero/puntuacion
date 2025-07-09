<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Championship;
use App\Models\ChampionshipRegistration;
use App\Models\Pilot;
use Illuminate\Http\Request;

class ChampionshipRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Championship $championship, Request $request)
    {
        $query = ChampionshipRegistration::with(['pilot.club', 'category'])
                    ->where('championship_id', $championship->id);
        
        // Filtros
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('pilot', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('rut', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%");
            })->orWhere('bib_number', 'like', "%{$search}%");
        }
        
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }
        
        if ($request->has('club_id') && $request->get('club_id') !== '') {
            $query->whereHas('pilot', function($q) use ($request) {
                $q->where('club_id', $request->get('club_id'));
            });
        }

        $registrations = $query->orderBy('bib_number')->paginate(20);
        $clubs = $championship->registrations()->with('pilot.club')->get()->pluck('pilot.club')->unique('id');
        
        return view('admin.championships.registrations.index', compact('championship', 'registrations', 'clubs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Championship $championship)
    {
        // Obtener pilotos que NO están registrados en este campeonato
        $registeredPilotIds = ChampionshipRegistration::where('championship_id', $championship->id)
                                                     ->pluck('pilot_id')
                                                     ->toArray();
        
        $pilots = Pilot::with(['club'])
                      ->whereNotIn('id', $registeredPilotIds)
                      ->where('status', 'active')
                      ->orderBy('first_name')
                      ->orderBy('last_name')
                      ->get();
        
        $categories = Category::active()->orderBy('type')->orderBy('name')->get();
        
        // Obtener el próximo dorsal disponible
        $nextBibNumber = $this->getNextAvailableBibNumber($championship);
        
        return view('admin.championships.registrations.create', compact('championship', 'pilots', 'categories', 'nextBibNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Championship $championship)
    {
        $validated = $request->validate([
            'pilot_id' => 'required|exists:pilots,id',
            'category_id' => 'required|exists:categories,id',
            'bib_number' => [
                'required',
                'string',
                'max:10',
                'unique:championship_registrations,bib_number,NULL,id,championship_id,' . $championship->id
            ],
            'registration_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive'
        ]);
        
        // Verificar que el piloto no esté ya registrado
        $existingRegistration = ChampionshipRegistration::where('championship_id', $championship->id)
                                                       ->where('pilot_id', $validated['pilot_id'])
                                                       ->first();
        
        if ($existingRegistration) {
            return back()->withErrors(['pilot_id' => 'Este piloto ya está registrado en el campeonato.'])->withInput();
        }
        
        $validated['championship_id'] = $championship->id;
        
        ChampionshipRegistration::create($validated);
        
        return redirect()->route('admin.championships.registrations.index', $championship)
                        ->with('success', 'Piloto registrado exitosamente en el campeonato.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Championship $championship, ChampionshipRegistration $registration)
    {
        $registration->load(['pilot.club', 'category']);
        return view('admin.championships.registrations.show', compact('championship', 'registration'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Championship $championship, ChampionshipRegistration $registration)
    {
        return view('admin.championships.registrations.edit', compact('championship', 'registration'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Championship $championship, ChampionshipRegistration $registration)
    {
        $validated = $request->validate([
            'bib_number' => [
                'required',
                'string',
                'max:10',
                'unique:championship_registrations,bib_number,' . $registration->id . ',id,championship_id,' . $championship->id
            ],
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive'
        ]);
        
        $registration->update($validated);
        
        return redirect()->route('admin.championships.registrations.index', $championship)
                        ->with('success', 'Registro actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Championship $championship, ChampionshipRegistration $registration)
    {
        $registration->delete();
        
        return redirect()->route('admin.championships.registrations.index', $championship)
                        ->with('success', 'Registro eliminado exitosamente.');
    }

    /**
     * Obtener el próximo dorsal disponible
     */
    private function getNextAvailableBibNumber(Championship $championship)
    {
        $usedBibNumbers = ChampionshipRegistration::where('championship_id', $championship->id)
                                                 ->pluck('bib_number')
                                                 ->map(function($bib) {
                                                     return is_numeric($bib) ? (int)$bib : 0;
                                                 })
                                                 ->filter()
                                                 ->sort()
                                                 ->values()
                                                 ->toArray();
        
        // Buscar el primer número disponible empezando por 1
        $nextNumber = 1;
        foreach ($usedBibNumbers as $usedNumber) {
            if ($nextNumber < $usedNumber) {
                break;
            }
            if ($nextNumber == $usedNumber) {
                $nextNumber++;
            }
        }
        
        return (string)$nextNumber;
    }
}
