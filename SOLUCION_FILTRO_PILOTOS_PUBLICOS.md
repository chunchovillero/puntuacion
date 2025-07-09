# Filtro de Pilotos Inactivos en Vistas Públicas

## Problema
El usuario solicitó que en las vistas públicas no aparezcan los pilotos inactivos, manteniendo solo los pilotos activos visibles para el público general.

## Solución Implementada

### 1. Rutas Web Públicas

#### a) Lista de Pilotos Públicos
**Archivo**: `routes/web.php`

```php
Route::get('/pilotos', function () {
    // Solo mostrar pilotos activos en vista pública
    $pilots = \App\Models\Pilot::with(['club'])
        ->where('status', 'active')
        ->paginate(15);
    $clubs = \App\Models\Club::orderBy('name')->get();
    
    return view('app')->with('initialData', [
        'pilots' => $pilots,
        'clubs' => $clubs,
        'page' => 'pilots-list'
    ]);
})->name('public.pilots.index');
```

### 2. Controlador de Clubes

#### a) Método `show()` - Detalle de Club
**Archivo**: `app/Http/Controllers/Admin/ClubController.php`

```php
public function show(Club $club)
{
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
```

### 3. Controlador de Pilotos

#### a) Método `byClub()` - Pilotos por Club
**Archivo**: `app/Http/Controllers/Admin/PilotController.php`

```php
public function byClub(Club $club, Request $request)
{
    // Solo mostrar pilotos activos en vista pública
    $query = $club->pilots()->where('status', 'active');
    
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
```

#### b) Método `apiIndex()` - API Lista de Pilotos
```php
public function apiIndex(Request $request)
{
    $query = Pilot::with(['club']);
    
    // Para vistas públicas, filtrar solo pilotos activos
    // Para admin, mostrar todos según filtros
    $isPublicView = !auth()->check() || $request->get('public_view', false);
    
    if ($isPublicView) {
        $query->where('status', 'active');
    }
    
    // ... resto de filtros y lógica
}
```

#### c) Método `apiShow()` - API Detalle de Piloto
```php
public function apiShow(Pilot $pilot, Request $request)
{
    try {
        // Para vistas públicas, verificar que el piloto esté activo
        $isPublicView = !auth()->check() || $request->get('public_view', false);
        
        if ($isPublicView && $pilot->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Piloto no encontrado'
            ], 404);
        }
        
        $pilot->load(['club']);
        
        return response()->json([
            'success' => true,
            'data' => $pilot
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener el piloto: ' . $e->getMessage()
        ], 500);
    }
}
```

#### d) Método `apiByClub()` - API Pilotos por Club
```php
public function apiByClub(Club $club, Request $request)
{
    try {
        $query = $club->pilots();
        
        // Para vistas públicas, filtrar solo pilotos activos
        $isPublicView = !auth()->check() || $request->get('public_view', false);
        
        if ($isPublicView) {
            $query->where('status', 'active');
        }
        
        $pilots = $query->orderBy('ranking_points', 'desc')
                       ->orderBy('last_name')
                       ->get();
            
        return response()->json([
            'success' => true,
            'data' => $pilots
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener los pilotos: ' . $e->getMessage()
        ], 500);
    }
}
```

## Lógica de Filtrado

### Diferenciación Público vs Administrativo

La implementación distingue entre vistas públicas y administrativas:

1. **Vista Pública**: 
   - Usuario no autenticado (`!auth()->check()`)
   - O parámetro explícito `public_view=true`
   - **Resultado**: Solo pilotos con `status = 'active'`

2. **Vista Administrativa**:
   - Usuario autenticado sin parámetro `public_view`
   - **Resultado**: Todos los pilotos (activos e inactivos) según filtros aplicados

### Puntos de Filtrado Implementados

1. **Ruta `/pilotos`** - Lista principal de pilotos
2. **Ruta `/clubes/{club}`** - Detalle de club con sus pilotos
3. **Ruta `/clubes/{club}/pilotos`** - Pilotos específicos de un club
4. **API `/api/pilots`** - Lista de pilotos vía API
5. **API `/api/pilots/{pilot}`** - Detalle de piloto vía API
6. **API `/api/clubs/{club}/pilots`** - Pilotos de club vía API

## Beneficios de la Implementación

1. **Experiencia del usuario público**: Solo ven pilotos activos
2. **Flexibilidad administrativa**: Los administradores pueden ver todos los pilotos
3. **Consistencia**: Filtro aplicado en todas las vistas públicas relevantes
4. **Mantenimiento**: Lógica centralizada y reutilizable
5. **Retrocompatibilidad**: No afecta las funciones administrativas existentes

## Archivos Modificados

### Backend
- `routes/web.php` - Ruta pública de pilotos
- `app/Http/Controllers/Admin/ClubController.php` - Método show
- `app/Http/Controllers/Admin/PilotController.php` - Métodos byClub, apiIndex, apiShow, apiByClub

## Estado Final
✅ **COMPLETADO**: Los pilotos inactivos ahora están ocultos en todas las vistas públicas, mientras que las vistas administrativas mantienen acceso completo a todos los pilotos.

### Rutas Públicas Afectadas
- `/pilotos` - Lista de pilotos
- `/clubes/{club}` - Detalle de club 
- `/clubes/{club}/pilotos` - Pilotos por club
- Todas las APIs públicas relacionadas

### Comportamiento Esperado
- **Público**: Solo ve pilotos activos
- **Administradores**: Ven todos los pilotos según configuración
- **Pilotos inactivos**: Permanecen en la base de datos pero ocultos al público
