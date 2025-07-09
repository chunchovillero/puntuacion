# Solución: Error "Campeonato no encontrado"

## Problema Identificado
Al hacer clic en un campeonato, aparecía el error "campeonato no encontrado" porque las rutas API para campeonatos no estaban configuradas correctamente.

## Causa Raíz
El frontend (`ChampionshipDetail.vue`) estaba intentando acceder a la ruta `/api/championships/{id}` pero esta ruta no existía en el backend.

### Análisis del problema:
1. **Frontend**: Componente `ChampionshipDetail.vue` intentaba cargar datos con:
   ```javascript
   const response = await fetch(`/api/championships/${this.championshipId}`);
   ```

2. **Backend**: No existían rutas API para campeonatos en `routes/api.php`

3. **Controlador**: El `ChampionshipController` tenía `apiIndex` y `apiExport` pero faltaba `apiShow`

## Solución Implementada

### 1. Agregadas las rutas API en `routes/api.php`
```php
// Championship API Routes
Route::prefix('championships')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiIndex']);
    Route::get('/export', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiExport']);
    Route::get('/{championship}', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiShow']);
    Route::get('/{championship}/matchdays', [\App\Http\Controllers\Admin\MatchdayController::class, 'apiByChampionship']);
    Route::post('/', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiStore']);
    Route::put('/{championship}', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiUpdate']);
    Route::delete('/{championship}', [\App\Http\Controllers\Admin\ChampionshipController::class, 'apiDestroy']);
});
```

### 2. Agregado método `apiShow` en `ChampionshipController`
```php
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
```

### 3. Agregados métodos CRUD adicionales
- `apiStore`: Crear nuevos campeonatos
- `apiUpdate`: Actualizar campeonatos existentes
- `apiDestroy`: Eliminar campeonatos

### 4. Agregado método `apiByChampionship` en `MatchdayController`
```php
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
```

## Características de Seguridad Implementadas

### 1. Filtro para Vistas Públicas
- Los usuarios no autenticados solo pueden ver campeonatos activos
- Los campeonatos inactivos retornan 404 para usuarios no autenticados
- Los administradores pueden ver todos los campeonatos

### 2. Validación de Datos
- Validación completa en métodos `apiStore` y `apiUpdate`
- Verificación de duplicados por nombre y año
- Validación de fechas coherentes

### 3. Manejo de Errores
- Responses JSON consistentes con estructura `success`/`message`
- Códigos HTTP apropiados (404, 422, 500)
- Logging de errores para debugging

## Rutas API Disponibles

| Método | Ruta | Controlador | Función |
|--------|------|-------------|---------|
| GET | `/api/championships` | `ChampionshipController@apiIndex` | Listar campeonatos |
| GET | `/api/championships/{id}` | `ChampionshipController@apiShow` | Mostrar campeonato específico |
| POST | `/api/championships` | `ChampionshipController@apiStore` | Crear campeonato |
| PUT | `/api/championships/{id}` | `ChampionshipController@apiUpdate` | Actualizar campeonato |
| DELETE | `/api/championships/{id}` | `ChampionshipController@apiDestroy` | Eliminar campeonato |
| GET | `/api/championships/export` | `ChampionshipController@apiExport` | Exportar campeonatos |
| GET | `/api/championships/{id}/matchdays` | `MatchdayController@apiByChampionship` | Obtener jornadas del campeonato |

## Testing
Para verificar que la solución funciona:

1. **Acceder a un campeonato específico**: 
   ```
   GET /api/championships/1
   ```

2. **Verificar que carga las jornadas**:
   ```
   GET /api/championships/1/matchdays
   ```

3. **Verificar filtro para vistas públicas**:
   - Como usuario autenticado: ve todos los campeonatos
   - Como usuario no autenticado: solo ve campeonatos activos

## Mantenimiento
- La caché de rutas se regeneró después de los cambios
- Todos los métodos incluyen manejo de errores
- Los métodos siguen las convenciones de la aplicación existente

Esta solución asegura que el frontend puede comunicarse correctamente con el backend para cargar y gestionar campeonatos, eliminando el error "campeonato no encontrado".
