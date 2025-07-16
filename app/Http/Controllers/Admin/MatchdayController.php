<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matchday;
use App\Models\Championship;
use App\Models\Club;
use Illuminate\Http\Request;
use App\Models\Pilot;
use App\Models\ChampionshipRegistration;
use App\Models\MatchdayParticipant;

class MatchdayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Matchday::with(['championship', 'organizerClub'])
            ->withCount('participants'); // Agregar conteo de participantes
        
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
    public function show(Matchday $matchday, Request $request)
    {
        // Para vistas públicas, verificar que la jornada esté activa
        $isPublicView = !auth()->check() || $request->get('public_view', false);
        
        if ($isPublicView && $matchday->status === 'cancelled') {
            abort(404, 'Jornada no encontrada');
        }

        // Cargar relaciones necesarias
        $matchday->load([
            'championship',
            'organizerClub',
            'participants' => function($query) {
                $query->with(['pilot.club', 'pilot.category'])
                      ->whereIn('status', ['registered', 'confirmed', 'active'])
                      ->orderBy('created_at', 'asc');
            }
        ]);

        // Cargar conteos
        $matchday->loadCount('participants');

        // Obtener parámetros de la URL para Vue Router
        $fromPage = $request->get('from', 'matchdays');
        $championshipId = $request->get('championshipId');
        
        // Preparar datos iniciales para Vue.js
        $initialData = [
            'matchday' => $matchday,
            'page' => 'matchday-detail',
            'navigation' => [
                'from' => $fromPage,
                'championshipId' => $championshipId
            ]
        ];

        // Siempre devolver la vista app para que Vue.js maneje la interfaz
        return view('app')->with('initialData', $initialData);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Matchday $matchday, Request $request)
    {
        // Cargar relaciones necesarias
        $matchday->load([
            'championship',
            'organizerClub'
        ]);

        // Cargar datos adicionales para el formulario
        $championships = Championship::orderBy('year', 'desc')->orderBy('name')->get();
        $clubs = Club::orderBy('name')->get();
        
        // Obtener parámetros de navegación
        $fromPage = $request->get('from', 'matchdays');
        $championshipId = $request->get('championshipId');
        
        // Preparar datos iniciales para Vue.js
        $initialData = [
            'matchday' => $matchday,
            'championships' => $championships,
            'clubs' => $clubs,
            'page' => 'matchday-edit',
            'navigation' => [
                'from' => $fromPage,
                'championshipId' => $championshipId
            ]
        ];

        // Siempre devolver la vista app para que Vue.js maneje la interfaz
        return view('app')->with('initialData', $initialData);
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

    /**
     * Display participants for public viewing
     */
    public function participants(Matchday $matchday)
    {
        $participants = $matchday->participants()
                                ->with([
                                    'pilot.club', 
                                    'pilot.championshipRegistrations' => function($query) use ($matchday) {
                                        $query->where('championship_id', $matchday->championship_id);
                                    },
                                    'category'
                                ])
                                ->where('status', '!=', 'cancelled')
                                ->orderBy('created_at')
                                ->get();

        $participantsByCategory = $participants->groupBy('category.name');
        
        // Use public layout for non-authenticated users
        $view = auth()->check() ? 'admin.matchdays.participants' : 'public.matchdays.participants';
        
        return view($view, compact('matchday', 'participants', 'participantsByCategory'));
    }

    /**
     * Mostrar pagos de una jornada específica
     */
    public function payments(Matchday $matchday)
    {
        $matchday->load(['championship', 'organizerClub']);

        // Obtener pagos con información completa
        $payments = \App\Models\Payment::whereHas('matchdayParticipant', function($query) use ($matchday) {
            $query->where('matchday_id', $matchday->id);
        })
        ->with([
            'matchdayParticipant.pilot.club',
            'matchdayParticipant.category'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        // Estadísticas detalladas
        $allPayments = \App\Models\Payment::whereHas('matchdayParticipant', function($query) use ($matchday) {
            $query->where('matchday_id', $matchday->id);
        })->get();

        $paymentStats = [
            'total_payments' => $allPayments->count(),
            'approved_payments' => $allPayments->where('status', 'approved')->count(),
            'pending_payments' => $allPayments->where('status', 'pending')->count(),
            'rejected_payments' => $allPayments->where('status', 'rejected')->count(),
            'total_amount' => $allPayments->where('status', 'approved')->sum('amount'),
            'pending_amount' => $allPayments->where('status', 'pending')->sum('amount'),
            'expected_amount' => $matchday->participants()->count() * ($matchday->entry_fee ?? 5000),
        ];

        // Agrupación por método de pago
        $paymentMethods = $allPayments->groupBy('payment_method')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->where('status', 'approved')->sum('amount')
            ];
        });

        // Agrupación por estado
        $statusGroups = $allPayments->groupBy('status')->map(function($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount')
            ];
        });

        return view('admin.matchdays.payments', compact(
            'matchday', 
            'payments', 
            'paymentStats', 
            'paymentMethods', 
            'statusGroups'
        ));
    }

    /**
     * API: Listado de jornadas para Vue.js
     */
    public function apiIndex(Request $request)
    {
        $query = Matchday::with(['championship', 'organizerClub'])
            ->withCount('participants');
        
        // Filtros
        if ($request->has('championship') && $request->get('championship') !== '') {
            $query->where('championship_id', $request->get('championship'));
        }
        
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }
        
        if ($request->has('search') && $request->get('search') !== '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%")
                  ->orWhereHas('championship', function($champ) use ($search) {
                      $champ->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->has('date_from') && $request->get('date_from') !== '') {
            $query->whereDate('date', '>=', $request->get('date_from'));
        }
        
        if ($request->has('date_to') && $request->get('date_to') !== '') {
            $query->whereDate('date', '<=', $request->get('date_to'));
        }
        
        $matchdays = $query->orderBy('date', 'desc')->orderBy('number', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $matchdays
        ]);
    }

    /**
     * API: Exportar jornadas
     */
    public function apiExport(Request $request)
    {
        $query = Matchday::with(['championship', 'organizerClub'])
            ->withCount('participants');
        
        // Aplicar mismos filtros que en apiIndex
        if ($request->has('championship') && $request->get('championship') !== '') {
            $query->where('championship_id', $request->get('championship'));
        }
        
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }
        
        if ($request->has('search') && $request->get('search') !== '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%")
                  ->orWhereHas('championship', function($champ) use ($search) {
                      $champ->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $matchdays = $query->orderBy('date', 'desc')->orderBy('number', 'asc')->get();
        
        // Preparar datos para CSV
        $csvData = [];
        $csvData[] = ['Número', 'Campeonato', 'Año', 'Fecha', 'Hora', 'Lugar', 'Organizador', 'Participantes', 'Estado'];
        
        foreach ($matchdays as $matchday) {
            $csvData[] = [
                $matchday->number,
                $matchday->championship->name,
                $matchday->championship->year,
                $matchday->date ? $matchday->date->format('d/m/Y') : '',
                $matchday->start_time ?? '',
                $matchday->venue,
                $matchday->organizerClub ? $matchday->organizerClub->name : 'AMBMX',
                $matchday->participants_count,
                $this->getStatusText($matchday->status)
            ];
        }
        
        // Generar CSV
        $filename = 'jornadas_' . date('Y-m-d_H-i-s') . '.csv';
        $temp = tmpfile();
        
        foreach ($csvData as $row) {
            fputcsv($temp, $row, ';');
        }
        
        rewind($temp);
        $csv = stream_get_contents($temp);
        fclose($temp);
        
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Length', strlen($csv));
    }

    /**
     * API: Eliminar jornada
     */
    public function apiDestroy(Matchday $matchday)
    {
        try {
            // Verificar si tiene participantes
            if ($matchday->participants()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una jornada que tiene participantes inscritos.'
                ], 400);
            }
            
            $matchday->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Jornada eliminada correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la jornada: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API - Show the specified matchday
     */
    public function apiShow(Matchday $matchday, Request $request)
    {
        try {
            // Para vistas públicas, verificar que la jornada esté activa
            $isPublicView = !auth()->check() || $request->get('public_view', false);
            
            if ($isPublicView && $matchday->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Jornada no encontrada'
                ], 404);
            }
            
            // Cargar relaciones necesarias
            $matchday->load([
                'championship',
                'organizerClub',
                'participants' => function($query) {
                    $query->with(['pilot.club', 'pilot.category'])
                          ->whereIn('status', ['registered', 'confirmed', 'active'])
                          ->orderBy('created_at', 'asc');
                }
            ]);
            
            // Cargar conteos
            $matchday->loadCount('participants');
            
            return response()->json([
                'success' => true,
                'data' => $matchday
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la jornada: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vista Vue.js para jornadas
     */
    public function vueIndex()
    {
        return view('admin.matchdays.vue-index');
    }

    /**
     * Obtener texto del estado
     */
    private function getStatusText($status)
    {
        $statuses = [
            'scheduled' => 'Programada',
            'ongoing' => 'En curso',
            'in_progress' => 'En curso',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            'postponed' => 'Postergada'
        ];
        
        return $statuses[$status] ?? $status;
    }

    /**
     * API: Get matchdays by championship
     */
    public function apiByChampionship(Championship $championship, Request $request)
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
            
            $matchdays = $championship->matchdays()
                ->with(['organizerClub'])
                ->withCount('participants')
                ->orderBy('number', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $matchdays
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las jornadas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API - Update the specified matchday
     */
    public function apiUpdate(Request $request, Matchday $matchday)
    {
        try {
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
                'registration_start_date' => 'nullable|date',
                'registration_end_date' => 'nullable|date|after:registration_start_date',
                'public_registration_enabled' => 'boolean',
                'categories' => 'nullable|array',
                'entry_fee' => 'nullable|numeric|min:0',
                'requirements' => 'nullable|string'
            ]);

            // Verificar que no exista otra jornada con el mismo número en el campeonato
            $exists = Matchday::where('championship_id', $validated['championship_id'])
                             ->where('number', $validated['number'])
                             ->where('id', '!=', $matchday->id)
                             ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe otra jornada con este número en el campeonato seleccionado.',
                    'errors' => ['number' => ['Ya existe otra jornada con este número en el campeonato seleccionado.']]
                ], 422);
            }

            // Si se seleccionó organizador AMBMX, limpiar el club organizador
            if ($request->input('organizer_type') === 'ambmx') {
                $validated['organizer_club_id'] = null;
            }

            // Actualizar la jornada
            $matchday->update($validated);

            // Cargar relaciones para la respuesta
            $matchday->load([
                'championship',
                'organizerClub',
                'participants' => function($query) {
                    $query->with(['pilot.club', 'pilot.category'])
                          ->whereIn('status', ['registered', 'confirmed', 'active'])
                          ->orderBy('created_at', 'asc');
                }
            ]);

            $matchday->loadCount('participants');

            return response()->json([
                'success' => true,
                'message' => 'Jornada actualizada exitosamente',
                'data' => $matchday
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API - Search pilots available for a matchday
     */
    public function apiSearchPilotsForMatchday(Matchday $matchday, Request $request)
    {
        try {
            $search = $request->get('search', '');
            $limit = $request->get('limit', 10);
            
            // Obtener pilotos registrados en el campeonato de esta jornada
            $query = Pilot::select('pilots.*')
                ->join('championship_registrations', 'pilots.id', '=', 'championship_registrations.pilot_id')
                ->join('clubs', 'pilots.club_id', '=', 'clubs.id')
                ->where('championship_registrations.championship_id', $matchday->championship_id)
                ->where('championship_registrations.status', 'active')
                ->where('pilots.status', 'active')
                ->where('clubs.status', 'active')
                ->with(['club', 'category']);
            
            // Aplicar búsqueda por nombre o RUT
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->whereRaw("CONCAT(pilots.first_name, ' ', pilots.last_name) LIKE ?", ["%{$search}%"])
                      ->orWhere('pilots.rut', 'like', "%{$search}%")
                      ->orWhere('pilots.nickname', 'like', "%{$search}%");
                });
            }
            
            // Excluir pilotos ya registrados en esta jornada
            $query->whereNotExists(function($q) use ($matchday) {
                $q->select('id')
                  ->from('matchday_participants')
                  ->whereColumn('matchday_participants.pilot_id', 'pilots.id')
                  ->where('matchday_participants.matchday_id', $matchday->id)
                  ->whereIn('matchday_participants.status', ['registered', 'confirmed', 'active']);
            });
            
            $pilots = $query->orderBy('pilots.first_name')
                           ->orderBy('pilots.last_name')
                           ->limit($limit)
                           ->get();
            
            return response()->json([
                'success' => true,
                'data' => $pilots
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar pilotos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API - Add pilot to matchday
     */
    public function apiAddPilotToMatchday(Matchday $matchday, Request $request)
    {
        try {
            $validated = $request->validate([
                'pilot_id' => 'required|exists:pilots,id',
                'category_id' => 'nullable|exists:categories,id',
                'notes' => 'nullable|string|max:500'
            ]);
            
            // Verificar que el piloto esté registrado en el campeonato
            $championshipRegistration = ChampionshipRegistration::where('championship_id', $matchday->championship_id)
                ->where('pilot_id', $validated['pilot_id'])
                ->where('status', 'active')
                ->first();
                
            if (!$championshipRegistration) {
                return response()->json([
                    'success' => false,
                    'message' => 'El piloto no está registrado en este campeonato'
                ], 422);
            }
            
            // Verificar que el piloto no esté ya registrado en esta jornada
            $exists = MatchdayParticipant::where('matchday_id', $matchday->id)
                ->where('pilot_id', $validated['pilot_id'])
                ->whereIn('status', ['registered', 'confirmed', 'active'])
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El piloto ya está registrado en esta jornada'
                ], 422);
            }
            
            // Verificar que el piloto y su club estén activos
            $pilot = Pilot::with('club')->find($validated['pilot_id']);
            if ($pilot->status !== 'active' || $pilot->club->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'El piloto o su club no están activos'
                ], 422);
            }
            
            // Usar la categoría del registro del campeonato si no se especifica
            $categoryId = $validated['category_id'] ?? $championshipRegistration->category_id;
            
            // Generar número de registro
            $lastRegistrationNumber = MatchdayParticipant::where('matchday_id', $matchday->id)
                ->max('registration_number') ?? 0;
            
            // Crear el registro de participación
            $participant = MatchdayParticipant::create([
                'matchday_id' => $matchday->id,
                'pilot_id' => $validated['pilot_id'],
                'category_id' => $categoryId,
                'registration_number' => $lastRegistrationNumber + 1,
                'status' => 'registered',
                'notes' => $validated['notes'] ?? null,
                'registered_at' => now(),
                'entry_fee_paid' => 0
            ]);
            
            // Cargar relaciones para la respuesta
            $participant->load(['pilot.club', 'category']);
            
            return response()->json([
                'success' => true,
                'message' => 'Piloto agregado exitosamente a la jornada',
                'data' => $participant
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}
