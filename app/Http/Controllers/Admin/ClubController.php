<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClubController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Club::with('pilots');
        
        // Filtros
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }
        
        $clubs = $query->paginate(10);
        
        // Para SPA de Vue.js: servir la app principal con datos iniciales
        return view('app')->with('initialData', [
            'clubs' => $clubs,
            'page' => 'clubs-list'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.clubs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:clubs',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'founded_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,suspended'
        ]);

        // Manejar subida de logo
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        Club::create($validated);

        return redirect()->route('admin.clubs.index')
                        ->with('success', 'Club creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Club $club)
    {
        // Cargar conteos de pilotos
        $club->loadCount(['pilots', 'activePilots']);
        
        // Solo cargar pilotos activos en vista pública
        $club->load(['pilots' => function($query) {
            $query->where('status', 'active')
                  ->orderBy('ranking_points', 'desc');
        }]);
        
        // Para SPA de Vue.js: servir la app principal con datos iniciales del club
        return view('app')->with('initialData', [
            'club' => $club,
            'page' => 'club-detail'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Club $club)
    {
        return view('admin.clubs.edit', compact('club'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Club $club)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:clubs,name,' . $club->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'founded_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,suspended'
        ]);

        // Manejar subida de nuevo logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($club->logo) {
                Storage::disk('public')->delete($club->logo);
            }
            
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        $club->update($validated);

        return redirect()->route('admin.clubs.index')
                        ->with('success', 'Club actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Club $club)
    {
        // En lugar de eliminar físicamente, cambiar el status a inactivo
        $club->update(['status' => 'inactive']);

        return redirect()->route('admin.clubs.index')
                        ->with('success', 'Club desactivado exitosamente.');
    }

    /**
     * API endpoint for Vue component - list clubs with pagination
     */
    public function apiIndex(Request $request)
    {
        $query = Club::withCount(['pilots', 'activePilots']);
        
        // Para vistas públicas, filtrar solo clubes activos
        // Para admin, mostrar todos según filtros
        $isPublicView = !auth()->check() || $request->get('public_view', false);
        
        if ($isPublicView) {
            $query->where('status', 'active');
        }
        
        // Filtros
        if ($request->has('search') && $request->get('search') !== '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }
        
        // Para admin, aplicar filtro de status si se especifica
        if (!$isPublicView && $request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }
        
        $clubs = $query->orderBy('name')
                      ->paginate($request->get('per_page', 10));
        
        // Formatear datos para la respuesta API
        $clubs->getCollection()->transform(function ($club) {
            return [
                'id' => $club->id,
                'name' => $club->name,
                'description' => $club->description,
                'logo' => $club->logo,
                'city' => $club->city,
                'state' => $club->state,
                'country' => $club->country,
                'website' => $club->website,
                'status' => $club->status,
                'founded_year' => $club->founded_date ? $club->founded_date->format('Y') : null,
                'pilots_count' => $club->pilots_count,
                'active_pilots_count' => $club->active_pilots_count,
                'created_at' => $club->created_at,
                'updated_at' => $club->updated_at
            ];
        });
        
        return response()->json($clubs);
    }

    /**
     * API endpoint for Vue component - export clubs to Excel
     */
    public function apiExport(Request $request)
    {
        $query = Club::withCount(['pilots', 'activePilots']);
        
        // Aplicar los mismos filtros que en apiIndex
        if ($request->has('search') && $request->get('search') !== '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }
        
        $clubs = $query->orderBy('name')->get();
        
        // Crear datos para Excel
        $exportData = [];
        $exportData[] = [
            'Nombre',
            'Descripción',
            'Ciudad',
            'Estado/Provincia',
            'País',
            'Teléfono',
            'Email',
            'Website',
            'Estado',
            'Año Fundación',
            'Total Pilotos',
            'Pilotos Activos',
            'Fecha Creación'
        ];
        
        foreach ($clubs as $club) {
            $exportData[] = [
                $club->name,
                $club->description,
                $club->city,
                $club->state,
                $club->country,
                $club->phone,
                $club->email,
                $club->website,
                ucfirst($club->status),
                $club->founded_date ? $club->founded_date->format('Y') : '',
                $club->pilots_count,
                $club->active_pilots_count,
                $club->created_at->format('d/m/Y H:i')
            ];
        }
        
        // Crear archivo Excel simple (CSV con headers para Excel)
        $filename = 'clubes_bmx_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($exportData) {
            $file = fopen('php://output', 'w');
            
            // Añadir BOM para UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            foreach ($exportData as $row) {
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show Vue index page for clubs management
     */
    public function vueIndex()
    {
        return view('admin.clubs.vue-index');
    }

    /**
     * API - Show the specified club
     */
    public function apiShow(Club $club, Request $request)
    {
        try {
            // Para vistas públicas, verificar que el club esté activo
            $isPublicView = !auth()->check() || $request->get('public_view', false);
            
            if ($isPublicView && $club->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Club no encontrado'
                ], 404);
            }
            
            // Cargar conteos de pilotos
            $club->loadCount(['pilots', 'activePilots']);
            
            // Solo cargar pilotos activos para vistas públicas
            if ($isPublicView) {
                $club->load(['pilots' => function($query) {
                    $query->where('status', 'active')
                          ->orderBy('ranking_points', 'desc');
                }]);
            } else {
                $club->load(['pilots' => function($query) {
                    $query->orderBy('ranking_points', 'desc');
                }]);
            }
            
            // Asegurar que los conteos estén disponibles en la respuesta
            $clubData = $club->toArray();
            $clubData['pilots_count'] = $club->pilots_count;
            $clubData['active_pilots_count'] = $club->active_pilots_count;
            
            return response()->json([
                'success' => true,
                'data' => $clubData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el club: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API - Store a newly created club
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:clubs,name',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean'
        ]);

        try {
            $club = new Club();
            $club->name = $request->name;
            $club->city = $request->city;
            $club->state = $request->state;
            $club->status = $request->has('status') ? $request->status : true;

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = Str::slug($request->name) . '_' . time() . '.' . $logo->getClientOriginalExtension();
                $logoPath = $logo->storeAs('public/clubs', $logoName);
                $club->logo = 'clubs/' . $logoName;
            }

            $club->save();
            $club->load('pilots');

            return response()->json([
                'success' => true,
                'message' => 'Club creado exitosamente',
                'data' => $club
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el club: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API - Update the specified club
     */
    public function apiUpdate(Request $request, Club $club)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:clubs,name,' . $club->id,
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'boolean'
        ]);

        try {
            $club->name = $request->name;
            $club->city = $request->city;
            $club->state = $request->state;
            $club->status = $request->has('status') ? $request->status : $club->status;

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($club->logo && Storage::exists('public/' . $club->logo)) {
                    Storage::delete('public/' . $club->logo);
                }

                $logo = $request->file('logo');
                $logoName = Str::slug($request->name) . '_' . time() . '.' . $logo->getClientOriginalExtension();
                $logoPath = $logo->storeAs('public/clubs', $logoName);
                $club->logo = 'clubs/' . $logoName;
            }

            $club->save();
            $club->load('pilots');

            return response()->json([
                'success' => true,
                'message' => 'Club actualizado exitosamente',
                'data' => $club
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el club: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Desactivar un club (soft delete)
     */
    public function apiDestroy(Club $club)
    {
        try {
            $clubName = $club->name;
            
            // En lugar de eliminar, cambiar el status a inactivo
            $club->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => "Club '{$clubName}' desactivado exitosamente"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar el club: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Reactivar un club
     */
    public function apiReactivate(Club $club)
    {
        try {
            $clubName = $club->name;
            
            // Cambiar el status a activo
            $club->update(['status' => 'active']);

            return response()->json([
                'success' => true,
                'message' => "Club '{$clubName}' reactivado exitosamente"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reactivar el club: ' . $e->getMessage()
            ], 500);
        }
    }
}
