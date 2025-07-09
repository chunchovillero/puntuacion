<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matchday;
use App\Models\Pilot;
use App\Models\Category;
use App\Models\MatchdayParticipant;
use Illuminate\Http\Request;

class MatchdayParticipantController extends Controller
{
    /**
     * Mostrar formulario para inscribir piloto
     */
    public function create(Matchday $matchday)
    {
        // Obtener solo los pilotos registrados en el campeonato de esta jornada
        $championshipId = $matchday->championship_id;
        
        // Pilotos ya inscritos en esta jornada
        $inscribedPilotIds = MatchdayParticipant::where('matchday_id', $matchday->id)
                                               ->pluck('pilot_id')
                                               ->toArray();
        
        // Pilotos registrados en el campeonato pero NO inscritos en esta jornada
        $pilots = Pilot::whereHas('championshipRegistrations', function($query) use ($championshipId) {
                            $query->where('championship_id', $championshipId)
                                  ->where('status', 'active');
                        })
                        ->whereNotIn('id', $inscribedPilotIds)
                        ->with(['club', 'category', 'championshipRegistrations' => function($query) use ($championshipId) {
                            $query->where('championship_id', $championshipId);
                        }])
                        ->where('status', 'active')
                        ->orderBy('first_name')
                        ->orderBy('last_name')
                        ->get();

        $categories = Category::active()->orderBy('type')->orderBy('name')->get();
        
        return view('admin.matchdays.participants.create', compact('matchday', 'pilots', 'categories'));
    }

    /**
     * Inscribir piloto en la jornada
     */
    public function store(Request $request, Matchday $matchday)
    {
        $validated = $request->validate([
            'pilot_id' => 'required|exists:pilots,id',
            'category_id' => 'required|exists:categories,id',
            'entry_fee_paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verificar que el piloto no esté ya inscrito
        $existingParticipant = MatchdayParticipant::where('matchday_id', $matchday->id)
                                                 ->where('pilot_id', $validated['pilot_id'])
                                                 ->first();

        if ($existingParticipant) {
            return back()->withErrors(['pilot_id' => 'Este piloto ya está inscrito en esta jornada.'])->withInput();
        }

        // Verificar que la categoría del piloto coincida o sea compatible
        $pilot = Pilot::find($validated['pilot_id']);
        $category = Category::find($validated['category_id']);

        // Crear la inscripción
        MatchdayParticipant::create([
            'matchday_id' => $matchday->id,
            'pilot_id' => $validated['pilot_id'],
            'category_id' => $validated['category_id'],
            'entry_fee_paid' => $validated['entry_fee_paid'],
            'notes' => $validated['notes'],
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        return redirect()->route('admin.matchdays.participants.index', $matchday)
                        ->with('success', "Piloto {$pilot->first_name} {$pilot->last_name} inscrito exitosamente.");
    }

    /**
     * Mostrar lista de participantes de la jornada
     */
    public function index(Matchday $matchday)
    {
        $participants = $matchday->participants()
                                 ->with([
                                     'pilot.club', 
                                     'pilot.championshipRegistrations' => function($query) use ($matchday) {
                                         $query->where('championship_id', $matchday->championship_id);
                                     },
                                     'category'
                                 ])
                                 ->orderBy('created_at')
                                 ->get();

        $participantsByCategory = $participants->groupBy('category.name');

        return view('admin.matchdays.participants.index', compact('matchday', 'participants', 'participantsByCategory'));
    }

    /**
     * Mostrar formulario para editar participante
     */
    public function edit(Matchday $matchday, MatchdayParticipant $participant)
    {
        $categories = Category::active()->orderBy('type')->orderBy('name')->get();
        
        return view('admin.matchdays.participants.edit', compact('matchday', 'participant', 'categories'));
    }

    /**
     * Actualizar información del participante
     */
    public function update(Request $request, Matchday $matchday, MatchdayParticipant $participant)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'entry_fee_paid' => 'nullable|numeric|min:0',
            'status' => 'required|in:registered,confirmed,cancelled,no_show',
            'notes' => 'nullable|string|max:500',
        ]);

        $participant->update($validated);

        return redirect()->route('admin.matchdays.participants.index', $matchday)
                        ->with('success', 'Información del participante actualizada exitosamente.');
    }

    /**
     * Eliminar participante de la jornada
     */
    public function destroy(Matchday $matchday, MatchdayParticipant $participant)
    {
        $pilotName = $participant->pilot->first_name . ' ' . $participant->pilot->last_name;
        $participant->delete();

        return redirect()->route('admin.matchdays.participants.index', $matchday)
                        ->with('success', "Piloto {$pilotName} eliminado de la jornada exitosamente.");
    }

    /**
     * Cambiar estado del participante (AJAX)
     */
    public function updateStatus(Request $request, Matchday $matchday, MatchdayParticipant $participant)
    {
        $validated = $request->validate([
            'status' => 'required|in:registered,confirmed,cancelled,no_show',
        ]);

        $participant->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado exitosamente.',
            'status' => $participant->status,
            'status_label' => $participant->status_label,
            'status_color' => $participant->status_badge_color,
        ]);
    }
}
