<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matchday;
use App\Models\RaceSeries;
use App\Models\RaceHeat;
use App\Models\RaceLineup;
use App\Models\Category;
use App\Models\Pilot;
use App\Models\MatchdayParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RaceSheetController extends Controller
{
    /**
     * Mostrar la planilla de carreras de una jornada
     */
    public function index(Matchday $matchday)
    {
        $matchday->load(['championship', 'organizerClub']);
        
        // Obtener las rondas organizadas por categoría
        $seriesByCategory = RaceSeries::where('matchday_id', $matchday->id)
            ->with([
                'category', 
                'heats.lineups.pilot.club',
                'heats.lineups.pilot.championshipRegistrations' => function($query) use ($matchday) {
                    $query->where('championship_id', $matchday->championship_id);
                }
            ])
            ->orderBy('category_id')
            ->orderBy('series_number')
            ->get()
            ->groupBy('category.name');

        // Obtener participantes agrupados por categoría para mostrar pilotos disponibles
        $participantsByCategory = $matchday->participants()
            ->with([
                'pilot.club', 
                'category',
                'pilot.championshipRegistrations' => function($query) use ($matchday) {
                    $query->where('championship_id', $matchday->championship_id);
                }
            ])
            ->where('status', '!=', 'cancelled')
            ->get()
            ->groupBy('category.name');

        return view('admin.race-sheets.index', compact('matchday', 'seriesByCategory', 'participantsByCategory'));
    }

    /**
     * Crear una nueva ronda para una categoría
     */
    public function createSeries(Request $request, Matchday $matchday)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'nullable|string|max:100',
            'max_pilots' => 'integer|min:1|max:8',
            'transfer_to_final' => 'integer|min:0|max:8',
            'transfer_to_semifinal' => 'integer|min:0|max:8',
            'transfer_to_quarterfinal' => 'integer|min:0|max:8',
        ]);

        // Obtener el siguiente número de ronda para esta categoría
        $nextSeriesNumber = RaceSeries::where('matchday_id', $matchday->id)
            ->where('category_id', $validated['category_id'])
            ->max('series_number') + 1;

        $category = Category::find($validated['category_id']);
        
        // Generar nombre automático si no se proporciona
        if (empty($validated['name'])) {
            $validated['name'] = "Ronda {$nextSeriesNumber}";
        }

        $validated['matchday_id'] = $matchday->id;
        $validated['series_number'] = $nextSeriesNumber;
        $validated['max_pilots'] = $validated['max_pilots'] ?? 8;

        DB::transaction(function () use ($validated) {
            // Crear la ronda
            $series = RaceSeries::create($validated);

            // Crear automáticamente las 3 mangas
            for ($i = 1; $i <= 3; $i++) {
                RaceHeat::create([
                    'race_series_id' => $series->id,
                    'heat_number' => $i,
                    'name' => "Manga {$i}",
                    'status' => 'scheduled'
                ]);
            }
        });

        return redirect()->route('admin.race-sheets.index', $matchday)
            ->with('success', 'Ronda creada exitosamente con 3 mangas.');
    }

    /**
     * Asignar pilotos a una ronda automáticamente
     */
    public function assignPilots(Request $request, Matchday $matchday, RaceSeries $series)
    {
        $validated = $request->validate([
            'pilot_ids' => 'required|array|max:8',
            'pilot_ids.*' => 'exists:pilots,id',
        ]);

        // Verificar que los pilotos estén inscritos en la jornada y categoría correcta
        $participants = MatchdayParticipant::where('matchday_id', $matchday->id)
            ->where('category_id', $series->category_id)
            ->whereIn('pilot_id', $validated['pilot_ids'])
            ->where('status', '!=', 'cancelled')
            ->pluck('pilot_id')
            ->toArray();

        if (count($participants) !== count($validated['pilot_ids'])) {
            return back()->withErrors(['pilot_ids' => 'Algunos pilotos seleccionados no están inscritos en esta categoría.']);
        }

        DB::transaction(function () use ($series, $participants) {
            // Limpiar lineups existentes
            foreach ($series->heats as $heat) {
                $heat->lineups()->delete();
            }

            // Asignar pilotos a cada manga en posiciones aleatorias
            foreach ($series->heats as $heat) {
                $shuffledPilots = collect($participants)->shuffle();
                
                foreach ($shuffledPilots as $index => $pilotId) {
                    RaceLineup::create([
                        'race_heat_id' => $heat->id,
                        'pilot_id' => $pilotId,
                        'gate_position' => $index + 1,
                    ]);
                }
            }
        });

        return redirect()->route('admin.race-sheets.index', $matchday)
            ->with('success', 'Pilotos asignados exitosamente a las 3 mangas.');
    }

    /**
     * Mostrar formulario para editar una ronda específica
     */
    public function editSeries(Matchday $matchday, RaceSeries $series)
    {
        $series->load([
            'category', 
            'heats.lineups.pilot.club',
            'heats.lineups.pilot.championshipRegistrations' => function($query) use ($matchday) {
                $query->where('championship_id', $matchday->championship_id);
            }
        ]);
        
        // Obtener pilotos disponibles de esta categoría
        $availablePilots = $matchday->participants()
            ->with([
                'pilot.club',
                'pilot.championshipRegistrations' => function($query) use ($matchday) {
                    $query->where('championship_id', $matchday->championship_id);
                }
            ])
            ->where('category_id', $series->category_id)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->pluck('pilot');

        return view('admin.race-sheets.edit-series', compact('matchday', 'series', 'availablePilots'));
    }

    /**
     * Actualizar una ronda
     */
    public function updateSeries(Request $request, Matchday $matchday, RaceSeries $series)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'transfer_to_final' => 'integer|min:0|max:8',
            'transfer_to_semifinal' => 'integer|min:0|max:8',
            'transfer_to_quarterfinal' => 'integer|min:0|max:8',
            'notes' => 'nullable|string',
        ]);

        $series->update($validated);

        return redirect()->route('admin.race-sheets.index', $matchday)
            ->with('success', 'Ronda actualizada exitosamente.');
    }

    /**
     * Eliminar una ronda
     */
    public function destroySeries(Matchday $matchday, RaceSeries $series)
    {
        $series->delete();

        return redirect()->route('admin.race-sheets.index', $matchday)
            ->with('success', 'Ronda eliminada exitosamente.');
    }

    /**
     * Actualizar las posiciones de partidor de una manga específica
     */
    public function updateHeatLineup(Request $request, Matchday $matchday, RaceHeat $heat)
    {
        $validated = $request->validate([
            'lineups' => 'required|array',
            'lineups.*.pilot_id' => 'required|exists:pilots,id',
            'lineups.*.gate_position' => 'required|integer|min:1|max:8',
            'lineups.*.finish_position' => 'nullable|integer|min:1|max:8',
            'lineups.*.lap_time' => 'nullable|numeric|min:0',
            'lineups.*.dnf' => 'boolean',
            'lineups.*.dsq' => 'boolean',
            'lineups.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($heat, $validated) {
            // Limpiar lineups existentes
            $heat->lineups()->delete();

            // Crear nuevos lineups
            foreach ($validated['lineups'] as $lineupData) {
                RaceLineup::create([
                    'race_heat_id' => $heat->id,
                    'pilot_id' => $lineupData['pilot_id'],
                    'gate_position' => $lineupData['gate_position'],
                    'finish_position' => $lineupData['finish_position'] ?? null,
                    'lap_time' => $lineupData['lap_time'] ?? null,
                    'dnf' => $lineupData['dnf'] ?? false,
                    'dsq' => $lineupData['dsq'] ?? false,
                    'notes' => $lineupData['notes'] ?? null,
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Posiciones actualizadas exitosamente.']);
    }

    /**
     * Generar series automáticamente para todas las categorías
     */
    public function generateAllSeries(Request $request, Matchday $matchday)
    {
        $validated = $request->validate([
            'pilots_per_series' => 'integer|min:1|max:8',
        ]);

        $pilotsPerSeries = $validated['pilots_per_series'] ?? 8;

        DB::transaction(function () use ($matchday, $pilotsPerSeries) {
            // Limpiar series existentes
            RaceSeries::where('matchday_id', $matchday->id)->delete();

            // Obtener participantes agrupados por categoría
            $participantsByCategory = $matchday->participants()
                ->with(['pilot', 'category'])
                ->where('status', '!=', 'cancelled')
                ->get()
                ->groupBy('category_id');

            foreach ($participantsByCategory as $categoryId => $participants) {
                $category = $participants->first()->category;
                $pilots = $participants->pluck('pilot_id')->toArray();
                
                // Dividir pilotos en series
                $series = array_chunk($pilots, $pilotsPerSeries);
                
                foreach ($series as $index => $pilotGroup) {
                    $seriesNumber = $index + 1; // 1, 2, 3, etc.
                    
                    $raceSeries = RaceSeries::create([
                        'matchday_id' => $matchday->id,
                        'category_id' => $categoryId,
                        'name' => "Ronda {$seriesNumber}",
                        'series_number' => $seriesNumber,
                        'max_pilots' => $pilotsPerSeries,
                        'transfer_to_final' => min(4, count($pilotGroup)), // Máximo 4 a final
                    ]);

                    // Crear las 3 mangas para esta serie
                    for ($heatNumber = 1; $heatNumber <= 3; $heatNumber++) {
                        $heat = RaceHeat::create([
                            'race_series_id' => $raceSeries->id,
                            'heat_number' => $heatNumber,
                            'name' => "Manga {$heatNumber}",
                            'status' => 'scheduled'
                        ]);

                        // Asignar pilotos con posiciones aleatorias
                        $shuffledPilots = collect($pilotGroup)->shuffle();
                        foreach ($shuffledPilots as $gateIndex => $pilotId) {
                            RaceLineup::create([
                                'race_heat_id' => $heat->id,
                                'pilot_id' => $pilotId,
                                'gate_position' => $gateIndex + 1,
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('admin.race-sheets.index', $matchday)
            ->with('success', 'Rondas generadas automáticamente para todas las categorías.');
    }

    /**
     * API endpoint for Vue.js - Get race sheets data
     */
    public function apiIndex(Matchday $matchday)
    {
        $matchday->load(['championship', 'organizerClub']);
        
        // Obtener las rondas organizadas por categoría
        $seriesByCategory = RaceSeries::where('matchday_id', $matchday->id)
            ->with([
                'category', 
                'heats.lineups.pilot.club',
                'heats.lineups.pilot.championshipRegistrations' => function($query) use ($matchday) {
                    $query->where('championship_id', $matchday->championship_id);
                }
            ])
            ->orderBy('category_id')
            ->orderBy('series_number')
            ->get()
            ->groupBy('category.name');

        // Obtener participantes agrupados por categoría
        $participantsByCategory = $matchday->participants()
            ->with([
                'pilot.club', 
                'category',
                'pilot.championshipRegistrations' => function($query) use ($matchday) {
                    $query->where('championship_id', $matchday->championship_id);
                }
            ])
            ->where('status', '!=', 'cancelled')
            ->get()
            ->groupBy('category.name');

        return response()->json([
            'success' => true,
            'data' => [
                'matchday' => $matchday,
                'seriesByCategory' => $seriesByCategory,
                'participantsByCategory' => $participantsByCategory
            ]
        ]);
    }

    /**
     * API endpoint for Vue.js - Create new series
     */
    public function apiCreateSeries(Request $request, Matchday $matchday)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'nullable|string|max:100',
            'max_pilots' => 'integer|min:1|max:8',
            'transfer_to_final' => 'integer|min:0|max:8',
            'transfer_to_semifinal' => 'integer|min:0|max:8',
            'transfer_to_quarterfinal' => 'integer|min:0|max:8',
        ]);

        // Obtener el siguiente número de ronda para esta categoría
        $nextSeriesNumber = RaceSeries::where('matchday_id', $matchday->id)
            ->where('category_id', $validated['category_id'])
            ->max('series_number') + 1;

        // Generar nombre automático si no se proporciona
        if (empty($validated['name'])) {
            $validated['name'] = "Ronda {$nextSeriesNumber}";
        }

        $validated['matchday_id'] = $matchday->id;
        $validated['series_number'] = $nextSeriesNumber;
        $validated['max_pilots'] = $validated['max_pilots'] ?? 8;

        try {
            DB::transaction(function () use ($validated, &$series) {
                // Crear la ronda
                $series = RaceSeries::create($validated);

                // Crear automáticamente las 3 mangas
                for ($i = 1; $i <= 3; $i++) {
                    RaceHeat::create([
                        'race_series_id' => $series->id,
                        'heat_number' => $i,
                        'name' => "Manga {$i}",
                        'status' => 'scheduled'
                    ]);
                }
            });

            // Cargar relaciones para la respuesta
            $series->load(['category', 'heats']);

            return response()->json([
                'success' => true,
                'message' => 'Ronda creada exitosamente con 3 mangas.',
                'data' => $series
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la ronda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint for Vue.js - Assign pilots to series
     */
    public function apiAssignPilots(Request $request, Matchday $matchday, RaceSeries $series)
    {
        $validated = $request->validate([
            'pilot_ids' => 'required|array|max:8',
            'pilot_ids.*' => 'exists:pilots,id',
        ]);

        // Verificar que los pilotos estén inscritos en la jornada y categoría correcta
        $participants = MatchdayParticipant::where('matchday_id', $matchday->id)
            ->where('category_id', $series->category_id)
            ->whereIn('pilot_id', $validated['pilot_ids'])
            ->where('status', '!=', 'cancelled')
            ->pluck('pilot_id')
            ->toArray();

        if (count($participants) !== count($validated['pilot_ids'])) {
            return response()->json([
                'success' => false,
                'message' => 'Algunos pilotos seleccionados no están inscritos en esta categoría.'
            ], 400);
        }

        try {
            DB::transaction(function () use ($series, $participants) {
                // Limpiar lineups existentes
                foreach ($series->heats as $heat) {
                    $heat->lineups()->delete();
                }

                // Asignar pilotos a cada manga en posiciones aleatorias
                foreach ($series->heats as $heat) {
                    $shuffledPilots = collect($participants)->shuffle();
                    
                    foreach ($shuffledPilots as $index => $pilotId) {
                        RaceLineup::create([
                            'race_heat_id' => $heat->id,
                            'pilot_id' => $pilotId,
                            'gate_position' => $index + 1,
                        ]);
                    }
                }
            });

            // Recargar series con las relaciones actualizadas
            $series->load([
                'category', 
                'heats.lineups.pilot.club',
                'heats.lineups.pilot.championshipRegistrations' => function($query) use ($matchday) {
                    $query->where('championship_id', $matchday->championship_id);
                }
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pilotos asignados exitosamente a las 3 mangas.',
                'data' => $series
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar pilotos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint for Vue.js - Update heat lineup
     */
    public function apiUpdateHeatLineup(Request $request, Matchday $matchday, RaceHeat $heat)
    {
        $validated = $request->validate([
            'lineups' => 'required|array',
            'lineups.*.pilot_id' => 'required|exists:pilots,id',
            'lineups.*.gate_position' => 'required|integer|min:1|max:8',
            'lineups.*.finish_position' => 'nullable|integer|min:1|max:8',
            'lineups.*.lap_time' => 'nullable|numeric|min:0',
            'lineups.*.dnf' => 'boolean',
            'lineups.*.dsq' => 'boolean',
            'lineups.*.notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($heat, $validated) {
                // Limpiar lineups existentes
                $heat->lineups()->delete();

                // Crear nuevos lineups
                foreach ($validated['lineups'] as $lineupData) {
                    RaceLineup::create([
                        'race_heat_id' => $heat->id,
                        'pilot_id' => $lineupData['pilot_id'],
                        'gate_position' => $lineupData['gate_position'],
                        'finish_position' => $lineupData['finish_position'] ?? null,
                        'lap_time' => $lineupData['lap_time'] ?? null,
                        'dnf' => $lineupData['dnf'] ?? false,
                        'dsq' => $lineupData['dsq'] ?? false,
                        'notes' => $lineupData['notes'] ?? null,
                    ]);
                }
            });

            // Recargar heat con lineups actualizados
            $heat->load('lineups.pilot.club');

            return response()->json([
                'success' => true,
                'message' => 'Posiciones actualizadas exitosamente.',
                'data' => $heat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar posiciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint for Vue.js - Delete series
     */
    public function apiDestroySeries(Matchday $matchday, RaceSeries $series)
    {
        try {
            $series->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ronda eliminada exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la ronda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint for Vue.js - Assign single pilot to heat
     */
    public function apiAssignPilot(Request $request)
    {
        $validated = $request->validate([
            'pilot_id' => 'required|exists:pilots,id',
            'heat_id' => 'required|exists:race_heats,id',
        ]);

        try {
            // Get next available gate position
            $nextGatePosition = RaceLineup::where('race_heat_id', $validated['heat_id'])
                ->max('gate_position') + 1;

            if ($nextGatePosition > 8) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay posiciones disponibles en esta manga.'
                ], 400);
            }

            // Check if pilot is already in this heat
            $existingLineup = RaceLineup::where('race_heat_id', $validated['heat_id'])
                ->where('pilot_id', $validated['pilot_id'])
                ->first();

            if ($existingLineup) {
                return response()->json([
                    'success' => false,
                    'message' => 'El piloto ya está asignado a esta manga.'
                ], 400);
            }

            $lineup = RaceLineup::create([
                'race_heat_id' => $validated['heat_id'],
                'pilot_id' => $validated['pilot_id'],
                'gate_position' => $nextGatePosition,
            ]);

            $lineup->load('pilot.club');

            return response()->json([
                'success' => true,
                'message' => 'Piloto asignado exitosamente.',
                'data' => $lineup
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar piloto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint for Vue.js - Remove pilot from heat
     */
    public function apiRemovePilot(RaceLineup $lineup)
    {
        try {
            $lineup->delete();

            return response()->json([
                'success' => true,
                'message' => 'Piloto removido exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al remover piloto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Vue.js race sheets view
     */
    public function vueIndex(Matchday $matchday)
    {
        return view('admin.race-sheets.vue-index', compact('matchday'));
    }
}
