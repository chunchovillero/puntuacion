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
        $query = Championship::query();
        
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
        
        $championships = $query->orderBy('year', 'desc')->orderBy('name')->get();
        
        // Use public layout for non-authenticated users
        $view = auth()->check() ? 'admin.championships.index' : 'public.championships.index';
        
        return view($view, compact('championships'));
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
        // Load matchdays with explicit ordering to avoid cached relationship issues
        $championship->load([
            'matchdays' => function($query) {
                $query->with('organizerClub')
                      ->withCount('participants') // Agregar conteo de participantes
                      ->orderBy('number', 'asc');
            },
            'registrations' => function($query) {
                $query->with(['pilot.club', 'category'])
                      ->where('status', 'active')
                      ->orderBy('bib_number', 'asc');
            }
        ]);
        
        // Use public layout for non-authenticated users
        $view = auth()->check() ? 'admin.championships.show' : 'public.championships.show';
        
        return view($view, compact('championship'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Championship $championship)
    {
        return view('admin.championships.edit', compact('championship'));
    }

    /**
     * API: Show the specified championship
     */
    public function apiShow(Championship $championship, Request $request)
    {
        try {
            // Para vistas públicas, verificar que el campeonato esté activo
            $isPublicView = !auth()->check() || $request->get('public_view', false);
            
            if ($isPublicView && !$championship->active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Campeonato no encontrado'
                ], 404);
            }
            
            // Load related data
            $championship->load([
                'matchdays' => function($query) {
                    $query->with('organizerClub')
                          ->withCount('participants')
                          ->orderBy('number', 'asc');
                },
                'registrations' => function($query) {
                    $query->with(['pilot.club', 'category'])
                          ->where('status', 'active')
                          ->orderBy('bib_number', 'asc');
                }
            ]);
            
            return response()->json($championship);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el campeonato: ' . $e->getMessage()
            ], 500);
        }
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
        try {
            // Verificar si hay jornadas asociadas
            if ($championship->matchdays()->count() > 0) {
                $message = 'No se puede eliminar el campeonato porque tiene jornadas asociadas.';
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }
                
                return redirect()->route('admin.championships.index')
                                ->with('error', $message);
            }

            $championship->delete();
            
            $message = 'Campeonato eliminado exitosamente.';
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('admin.championships.index')
                            ->with('success', $message);
        } catch (\Exception $e) {
            $message = 'Error al eliminar el campeonato: ' . $e->getMessage();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            
            return redirect()->route('admin.championships.index')
                            ->with('error', $message);
        }
    }

    /**
     * API method to get championships with pagination and filters
     */
    public function apiIndex(Request $request)
    {
        try {
            $query = Championship::query();
            
            // Filtros
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            if ($request->has('year') && !empty($request->year)) {
                $query->where('year', $request->year);
            }
            
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }
            
            // Ordenamiento
            $sortField = $request->get('sort_field', 'year');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);
            
            // Obtener datos
            $championships = $query->withCount(['matchdays', 'registrations'])->get();
            
            // Estadísticas
            $stats = $this->getChampionshipStats();
            
            // Años disponibles
            $availableYears = Championship::distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();
            
            return response()->json([
                'success' => true,
                'data' => $championships->map(function($championship) {
                    return [
                        'id' => $championship->id,
                        'name' => $championship->name,
                        'year' => $championship->year,
                        'description' => $championship->description,
                        'start_date' => $championship->start_date,
                        'end_date' => $championship->end_date,
                        'entry_fee' => $championship->entry_fee,
                        'prizes' => $championship->prizes,
                        'status' => $championship->status,
                        'active' => $championship->active,
                        'matchdays_count' => $championship->matchdays_count,
                        'registrations_count' => $championship->registrations_count,
                        'created_at' => $championship->created_at,
                        'updated_at' => $championship->updated_at,
                    ];
                }),
                'stats' => $stats,
                'available_years' => $availableYears
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener campeonatos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Export championships to CSV
     */
    public function apiExport(Request $request)
    {
        try {
            $query = Championship::query();
            
            // Aplicar mismos filtros que en apiIndex
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            if ($request->has('year') && !empty($request->year)) {
                $query->where('year', $request->year);
            }
            
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }
            
            $championships = $query->withCount(['matchdays', 'registrations'])
                ->orderBy('year', 'desc')
                ->orderBy('name')
                ->get();
            
            $csvContent = "Nombre,Año,Estado,Fecha Inicio,Fecha Fin,Cuota Inscripción,Jornadas,Pilotos Registrados,Creado\n";
            
            foreach ($championships as $championship) {
                $csvContent .= sprintf(
                    "%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                    '"' . str_replace('"', '""', $championship->name) . '"',
                    $championship->year,
                    $this->getStatusText($championship->status),
                    $championship->start_date ? $championship->start_date->format('d/m/Y') : '',
                    $championship->end_date ? $championship->end_date->format('d/m/Y') : '',
                    $championship->entry_fee ? '$' . number_format($championship->entry_fee, 0, ',', '.') : '',
                    $championship->matchdays_count,
                    $championship->registrations_count,
                    $championship->created_at->format('d/m/Y H:i')
                );
            }
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="campeonatos_' . date('Y-m-d') . '.csv"');
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar campeonatos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get championship statistics
     */
    private function getChampionshipStats()
    {
        return [
            'total' => Championship::count(),
            'active' => Championship::where('status', 'active')->count(),
            'completed' => Championship::where('status', 'completed')->count(),
            'planned' => Championship::where('status', 'planned')->count(),
            'totalMatchdays' => \App\Models\Matchday::count(),
        ];
    }

    /**
     * Vue.js index method
     */
    public function vueIndex()
    {
        return view('admin.championships.vue-index');
    }

    /**
     * API: Store a newly created championship
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'year' => 'required|integer|min:2020|max:2030',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'entry_fee' => 'nullable|numeric|min:0',
            'prizes' => 'nullable|string',
            'status' => 'required|in:planned,active,completed,cancelled',
            'active' => 'boolean'
        ]);

        try {
            // Verificar que no exista ya un campeonato con el mismo nombre y año
            $existing = Championship::where('name', $validated['name'])
                                  ->where('year', $validated['year'])
                                  ->exists();
            
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un campeonato con este nombre para el año ' . $validated['year']
                ], 422);
            }

            $championship = Championship::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Campeonato creado exitosamente',
                'data' => $championship
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el campeonato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Update the specified championship
     */
    public function apiUpdate(Request $request, Championship $championship)
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

        try {
            // Verificar que no exista ya otro campeonato con el mismo nombre y año
            $existing = Championship::where('name', $validated['name'])
                                  ->where('year', $validated['year'])
                                  ->where('id', '!=', $championship->id)
                                  ->exists();
            
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe otro campeonato con este nombre para el año ' . $validated['year']
                ], 422);
            }

            $championship->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Campeonato actualizado exitosamente',
                'data' => $championship
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el campeonato: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Remove the specified championship
     */
    public function apiDestroy(Championship $championship)
    {
        try {
            // Verificar si hay jornadas asociadas
            if ($championship->matchdays()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el campeonato porque tiene jornadas asociadas.'
                ], 422);
            }

            $championshipName = $championship->name;
            $championship->delete();

            return response()->json([
                'success' => true,
                'message' => "Campeonato '{$championshipName}' eliminado exitosamente"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el campeonato: ' . $e->getMessage()
            ], 500);
        }
    }
}
