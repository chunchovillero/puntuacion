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
        
        $categories = $query->withCount('pilots')->orderBy('type')->orderBy('name')->paginate(15);
        
        return view('admin.categories.index', compact('categories'));
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
}
