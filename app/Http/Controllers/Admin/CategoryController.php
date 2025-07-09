<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();
        
        // Filtros
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('type') && $request->get('type') !== '') {
            $query->where('type', $request->get('type'));
        }
        
        if ($request->has('gender') && $request->get('gender') !== '') {
            $query->where('gender', $request->get('gender'));
        }
        
        if ($request->has('status') && $request->get('status') !== '') {
            $active = $request->get('status') === 'active';
            $query->where('active', $active);
        }
        
        // Obtener categorías con conteo de pilotos, ordenadas por tipo y nombre
        $categories = $query->withCount('pilots')
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(15);
        
        // Obtener campeonatos activos ordenados por año más reciente primero
        $championships = \App\Models\Championship::where('active', true)
            ->orderBy('year', 'desc')
            ->orderBy('name')
            ->get();
        
        // Para cada categoría, obtener el conteo de pilotos registrados por campeonato
        // Optimizamos la consulta haciendo una sola consulta para todos los conteos
        $categoryIds = $categories->pluck('id')->toArray();
        $championshipIds = $championships->pluck('id')->toArray();
        
        if (!empty($categoryIds) && !empty($championshipIds)) {
            $registrationCounts = \App\Models\ChampionshipRegistration::whereIn('category_id', $categoryIds)
                ->whereIn('championship_id', $championshipIds)
                ->selectRaw('category_id, championship_id, COUNT(*) as count')
                ->groupBy('category_id', 'championship_id')
                ->get()
                ->groupBy('category_id');
            
            $categories->each(function($category) use ($championships, $registrationCounts) {
                $counts = [];
                $categoryRegistrations = $registrationCounts->get($category->id, collect());
                
                foreach ($championships as $championship) {
                    $registration = $categoryRegistrations->firstWhere('championship_id', $championship->id);
                    $counts[$championship->id] = $registration ? $registration->count : 0;
                }
                
                $category->championship_counts = $counts;
            });
        } else {
            // Si no hay categorías o campeonatos, inicializar arrays vacíos
            $categories->each(function($category) use ($championships) {
                $counts = [];
                foreach ($championships as $championship) {
                    $counts[$championship->id] = 0;
                }
                $category->championship_counts = $counts;
            });
        }
        
        return view('admin.categories.index', compact('categories', 'championships'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'type' => 'required|string|max:50',
            'gender' => 'nullable|string|in:varones,mujeres',
            'age_min' => 'nullable|integer|min:1|max:100',
            'age_max' => 'nullable|integer|min:1|max:100|gte:age_min',
            'description' => 'nullable|string|max:500',
            'active' => 'boolean'
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load('pilots.club');
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'type' => 'required|string|max:50',
            'gender' => 'nullable|string|in:varones,mujeres',
            'age_min' => 'nullable|integer|min:1|max:100',
            'age_max' => 'nullable|integer|min:1|max:100|gte:age_min',
            'description' => 'nullable|string|max:500',
            'active' => 'boolean'
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Verificar si hay pilotos asignados a esta categoría
        if ($category->pilots()->count() > 0) {
            return redirect()->route('admin.categories.index')
                            ->with('error', 'No se puede eliminar la categoría porque tiene pilotos asignados.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Categoría eliminada exitosamente.');
    }

    /**
     * Toggle the active status of a category.
     */
    public function toggleStatus(Category $category)
    {
        $category->update(['active' => !$category->active]);
        
        $status = $category->active ? 'activada' : 'desactivada';
        return redirect()->route('admin.categories.index')
                        ->with('success', "Categoría {$status} exitosamente.");
    }

    /**
     * Método alternativo para cargar conteos de campeonatos de manera más robusta
     */
    private function loadChampionshipCounts($categories, $championships)
    {
        try {
            // Método optimizado actual
            $categoryIds = $categories->pluck('id')->toArray();
            $championshipIds = $championships->pluck('id')->toArray();
            
            if (!empty($categoryIds) && !empty($championshipIds)) {
                $registrationCounts = \App\Models\ChampionshipRegistration::whereIn('category_id', $categoryIds)
                    ->whereIn('championship_id', $championshipIds)
                    ->selectRaw('category_id, championship_id, COUNT(*) as count')
                    ->groupBy('category_id', 'championship_id')
                    ->get()
                    ->groupBy('category_id');
                
                foreach ($categories as $category) {
                    $counts = [];
                    $categoryRegistrations = $registrationCounts->get($category->id, collect());
                    
                    foreach ($championships as $championship) {
                        $registration = $categoryRegistrations->firstWhere('championship_id', $championship->id);
                        $counts[$championship->id] = $registration ? $registration->count : 0;
                    }
                    
                    $category->championship_counts = $counts;
                }
            } else {
                foreach ($categories as $category) {
                    $counts = [];
                    foreach ($championships as $championship) {
                        $counts[$championship->id] = 0;
                    }
                    $category->championship_counts = $counts;
                }
            }
        } catch (\Exception $e) {
            // Fallback: usar método individual si hay problemas
            foreach ($categories as $category) {
                $counts = $category->getChampionshipCounts($championshipIds);
                $category->championship_counts = $counts;
            }
        }
    }

    /**
     * API endpoint for Vue component - list categories with pagination and stats
     */
    public function apiIndex(Request $request)
    {
        $query = Category::query();
        
        // Filtros
        if ($request->has('search') && $request->get('search') !== '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('type') && $request->get('type') !== '') {
            $query->where('type', $request->get('type'));
        }
        
        if ($request->has('gender') && $request->get('gender') !== '') {
            $query->where('gender', $request->get('gender'));
        }
        
        if ($request->has('status') && $request->get('status') !== '') {
            $active = $request->get('status') === 'active';
            $query->where('active', $active);
        }
        
        // Obtener categorías con conteo de pilotos
        $categories = $query->withCount('pilots')
            ->orderBy('type')
            ->orderBy('name')
            ->paginate($request->get('per_page', 15));
        
        // Obtener campeonatos activos
        $championships = \App\Models\Championship::where('active', true)
            ->orderBy('year', 'desc')
            ->orderBy('name')
            ->get();
        
        // Cargar conteos de campeonatos para categorías
        $this->loadChampionshipCountsForApi($categories, $championships);
        
        // Calcular estadísticas
        $allCategories = Category::withCount('pilots')->get();
        $stats = [
            'total' => $allCategories->count(),
            'active' => $allCategories->where('active', true)->count(),
            'championships' => $championships->count(),
            'totalPilots' => $allCategories->sum('pilots_count')
        ];
        
        // Formatear datos para la respuesta API
        $categories->getCollection()->transform(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type,
                'gender' => $category->gender,
                'age_min' => $category->age_min,
                'age_max' => $category->age_max,
                'description' => $category->description,
                'active' => $category->active,
                'pilots_count' => $category->pilots_count,
                'championship_counts' => $category->championship_counts ?? [],
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at
            ];
        });
        
        return response()->json([
            'data' => $categories->items(),
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
            'total' => $categories->total(),
            'per_page' => $categories->perPage(),
            'from' => $categories->firstItem(),
            'to' => $categories->lastItem(),
            'championships' => $championships->map(function($championship) {
                return [
                    'id' => $championship->id,
                    'name' => $championship->name,
                    'year' => $championship->year
                ];
            }),
            'stats' => $stats
        ]);
    }

    /**
     * API endpoint for Vue component - export categories to CSV
     */
    public function apiExport(Request $request)
    {
        $query = Category::withCount('pilots');
        
        // Aplicar los mismos filtros que en apiIndex
        if ($request->has('search') && $request->get('search') !== '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('type') && $request->get('type') !== '') {
            $query->where('type', $request->get('type'));
        }
        
        if ($request->has('gender') && $request->get('gender') !== '') {
            $query->where('gender', $request->get('gender'));
        }
        
        if ($request->has('status') && $request->get('status') !== '') {
            $active = $request->get('status') === 'active';
            $query->where('active', $active);
        }
        
        $categories = $query->orderBy('type')->orderBy('name')->get();
        
        // Crear datos para Excel
        $exportData = [];
        $exportData[] = [
            'Nombre',
            'Tipo',
            'Género',
            'Edad Mínima',
            'Edad Máxima',
            'Descripción',
            'Estado',
            'Total Pilotos',
            'Fecha Creación'
        ];
        
        foreach ($categories as $category) {
            $exportData[] = [
                $category->name,
                ucfirst($category->type),
                $category->gender ? ucfirst($category->gender) : 'Mixto',
                $category->age_min ?: '',
                $category->age_max ?: '',
                $category->description ?: '',
                $category->active ? 'Activa' : 'Inactiva',
                $category->pilots_count,
                $category->created_at->format('d/m/Y H:i')
            ];
        }
        
        // Crear archivo CSV
        $filename = 'categorias_bmx_' . date('Y-m-d_H-i-s') . '.csv';
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
     * Show Vue index page for categories management
     */
    public function vueIndex()
    {
        return view('admin.categories.vue-index');
    }

    /**
     * Helper method to load championship counts for API response
     */
    private function loadChampionshipCountsForApi($categories, $championships)
    {
        $categoryIds = $categories->pluck('id')->toArray();
        $championshipIds = $championships->pluck('id')->toArray();
        
        if (!empty($categoryIds) && !empty($championshipIds)) {
            $registrationCounts = \App\Models\ChampionshipRegistration::whereIn('category_id', $categoryIds)
                ->whereIn('championship_id', $championshipIds)
                ->selectRaw('category_id, championship_id, COUNT(*) as count')
                ->groupBy('category_id', 'championship_id')
                ->get()
                ->groupBy('category_id');
            
            $categories->each(function($category) use ($championships, $registrationCounts) {
                $counts = [];
                $categoryRegistrations = $registrationCounts->get($category->id, collect());
                
                foreach ($championships as $championship) {
                    $registration = $categoryRegistrations->firstWhere('championship_id', $championship->id);
                    $counts[$championship->id] = $registration ? $registration->count : 0;
                }
                
                $category->championship_counts = $counts;
            });
        } else {
            $categories->each(function($category) use ($championships) {
                $counts = [];
                foreach ($championships as $championship) {
                    $counts[$championship->id] = 0;
                }
                $category->championship_counts = $counts;
            });
        }
    }
}
