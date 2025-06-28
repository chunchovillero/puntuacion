<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pilot;
use App\Models\Club;
use App\Models\Category;
use Illuminate\Http\Request;

class PilotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pilot::with(['club', 'category']);
        
        // Filtros
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('club_id') && $request->get('club_id') !== '') {
            $query->where('club_id', $request->get('club_id'));
        }
        
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }
        
        if ($request->has('category_id') && $request->get('category_id') !== '') {
            $query->where('category_id', $request->get('category_id'));
        }
        
        $pilots = $query->orderBy('ranking_points', 'desc')->paginate(15);
        $clubs = Club::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('type')->orderBy('name')->get();
        
        return view('admin.pilots.index', compact('pilots', 'clubs', 'categories'));
    }

    /**
     * Show pilots by club
     */
    public function byClub(Club $club, Request $request)
    {
        $query = $club->pilots();
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%");
            });
        }
        
        $pilots = $query->orderBy('ranking_points', 'desc')->paginate(15);
        
        return view('admin.pilots.by-club', compact('pilots', 'club'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clubs = Club::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('type')->orderBy('name')->get();
        return view('admin.pilots.create', compact('clubs', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'nickname' => 'nullable|string|max:30|unique:pilots',
            'age' => 'nullable|integer|min:5|max:100',
            'birth_date' => 'nullable|date|before:today',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100|unique:pilots',
            'club_id' => 'required|exists:clubs,id',
            'experience_level' => 'required|in:principiante,intermedio,avanzado,profesional',
            'ranking_points' => 'nullable|integer|min:0',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pilots', 'public');
            $validated['photo_path'] = $path;
        }

        // Manejar fecha de nacimiento y edad
        if (!empty($validated['birth_date'])) {
            // Si se proporciona fecha de nacimiento, calcular edad
            $birthDate = \Carbon\Carbon::parse($validated['birth_date']);
            $validated['age'] = $birthDate->age;
        } elseif (!empty($validated['age'])) {
            // Si solo se proporciona edad, calcular fecha de nacimiento aproximada
            $validated['birth_date'] = now()->subYears($validated['age'])->format('Y-m-d');
        } else {
            // Al menos uno debe estar presente
            return back()->withErrors(['age' => 'Debe proporcionar la edad o la fecha de nacimiento.'])->withInput();
        }

        // Set joined_club_date to current date if not provided
        if (!isset($validated['joined_club_date'])) {
            $validated['joined_club_date'] = now()->format('Y-m-d');
        }

        Pilot::create($validated);

        return redirect()->route('admin.pilots.index')
                        ->with('success', 'Piloto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pilot $pilot)
    {
        $pilot->load('category');
        return view('admin.pilots.show', compact('pilot'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pilot $pilot)
    {
        $clubs = Club::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('type')->orderBy('name')->get();
        return view('admin.pilots.edit', compact('pilot', 'clubs', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pilot $pilot)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'nickname' => 'nullable|string|max:30|unique:pilots,nickname,' . $pilot->id,
            'age' => 'nullable|integer|min:5|max:100',
            'birth_date' => 'nullable|date|before:today',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100|unique:pilots,email,' . $pilot->id,
            'club_id' => 'required|exists:clubs,id',
            'category_id' => 'required|exists:categories,id',
            'ranking_points' => 'nullable|integer|min:0',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pilots', 'public');
            $validated['photo_path'] = $path;
        }

        // Manejar fecha de nacimiento y edad
        if (!empty($validated['birth_date'])) {
            // Si se proporciona fecha de nacimiento, calcular edad
            $birthDate = \Carbon\Carbon::parse($validated['birth_date']);
            $validated['age'] = $birthDate->age;
        } elseif (!empty($validated['age'])) {
            // Si solo se proporciona edad, calcular fecha de nacimiento aproximada
            $validated['birth_date'] = now()->subYears($validated['age'])->format('Y-m-d');
        } else {
            // Mantener valores existentes si no se proporciona ninguno
            if (empty($pilot->birth_date) && empty($pilot->age)) {
                return back()->withErrors(['age' => 'Debe proporcionar la edad o la fecha de nacimiento.'])->withInput();
            }
        }

        $pilot->update($validated);

        return redirect()->route('admin.pilots.index')
                        ->with('success', 'Piloto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pilot $pilot)
    {
        $pilot->delete();
        return redirect()->route('admin.pilots.index')
                        ->with('success', 'Piloto eliminado exitosamente.');
    }
}
