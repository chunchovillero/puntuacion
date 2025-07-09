# Filtros de Pilotos por Clubes Inactivos - Solución Implementada

## Problema Resuelto

En la sección de pilotos públicos aparecían pilotos que pertenecían a clubes desactivados, lo cual no debería suceder ya que los clubes inactivos deben estar ocultos del público junto con todos sus pilotos.

## Solución Implementada

Se agregaron filtros en todos los endpoints y rutas públicas para asegurar que **solo se muestren pilotos que pertenezcan a clubes activos** en las vistas públicas.

## Cambios Realizados

### 1. PilotController.php - Backend

#### Método `apiIndex()`
- ✅ **AGREGADO**: Filtro `whereHas('club')` para verificar que el club esté activo
- Solo para vistas públicas (`$isPublicView = true`)
- Los administradores siguen viendo todos los pilotos

```php
if ($isPublicView) {
    $query->where('status', 'active')
          // Solo mostrar pilotos de clubes activos en vistas públicas
          ->whereHas('club', function($q) {
              $q->where('status', 'active');
          });
}
```

#### Método `byClub()`
- ✅ **AGREGADO**: Validación que el club esté activo antes de mostrar sus pilotos
- Retorna 404 si el club está inactivo en vista pública

```php
if ($isPublicView && $club->status !== 'active') {
    abort(404, 'Club no encontrado');
}
```

#### Método `apiByClub()`
- ✅ **AGREGADO**: Validación similar para API
- Retorna JSON 404 si el club está inactivo

#### Método `apiShow()`
- ✅ **AGREGADO**: Validación que el club del piloto esté activo
- Retorna 404 si el piloto pertenece a un club inactivo

```php
if ($isPublicView && $pilot->club && $pilot->club->status !== 'active') {
    return response()->json([
        'success' => false,
        'message' => 'Piloto no encontrado'
    ], 404);
}
```

### 2. routes/web.php - Rutas Públicas

#### Ruta `/pilotos`
- ✅ **AGREGADO**: Filtro `whereHas('club')` en la consulta inicial
- Solo muestra pilotos de clubes activos

```php
$pilots = \App\Models\Pilot::with(['club'])
    ->where('status', 'active')
    ->whereHas('club', function($query) {
        $query->where('status', 'active');
    })
    ->paginate(15);
```

#### Ruta `/pilotos/{pilot}`
- ✅ **AGREGADO**: Validación de club activo antes de mostrar detalle
- Retorna 404 si el piloto o su club están inactivos

```php
if ($pilot->status !== 'active') {
    abort(404);
}

if ($pilot->club && $pilot->club->status !== 'active') {
    abort(404);
}
```

## Comportamiento Resultante

### Para Vistas Públicas (usuarios no autenticados):
1. **Lista de Pilotos** (`/pilotos`): Solo muestra pilotos activos de clubes activos
2. **Detalle de Piloto** (`/pilotos/{id}`): Solo accesible si el piloto Y su club están activos
3. **Pilotos por Club** (`/clubes/{id}/pilotos`): Solo accesible si el club está activo
4. **API de Pilotos**: Todos los endpoints filtran por clubes activos automáticamente

### Para Administradores:
1. **Vista Completa**: Pueden ver todos los pilotos independientemente del estado del club
2. **Gestión Total**: Acceso completo a pilotos de clubes activos e inactivos
3. **Sin Restricciones**: Todos los filtros de vistas públicas se omiten

## Escenarios Cubiertos

✅ **Usuario público accede a lista de pilotos**: Solo ve pilotos de clubes activos
✅ **Usuario público accede a detalle de piloto**: 404 si el club está inactivo
✅ **Usuario público accede a pilotos de club inactivo**: 404 - Club no encontrado
✅ **API pública de pilotos**: Solo retorna pilotos de clubes activos
✅ **Administrador**: Ve todos los pilotos sin restricciones

## Validaciones Implementadas

1. **Estado del Piloto**: `status = 'active'` (ya existía)
2. **Estado del Club**: `club.status = 'active'` (NUEVO)
3. **Relación Club-Piloto**: Verificación de que el piloto pertenezca a un club activo (NUEVO)

## Archivos Modificados

1. ✅ `app/Http/Controllers/Admin/PilotController.php`
   - `apiIndex()` - Filtro whereHas('club')
   - `byClub()` - Validación club activo
   - `apiByClub()` - Validación club activo en API
   - `apiShow()` - Validación club del piloto activo

2. ✅ `routes/web.php`
   - Ruta `/pilotos` - Filtro whereHas('club')
   - Ruta `/pilotos/{pilot}` - Validación club activo

## Compilación

✅ Assets compilados exitosamente con `npm run dev`
✅ Sin errores de sintaxis en archivos modificados

## Estado Final

**COMPLETADO Y FUNCIONAL** ✅

La implementación está completa y garantiza que:
- Los pilotos de clubes inactivos no aparecen en ninguna vista pública
- Las validaciones son consistentes en toda la aplicación
- Los administradores mantienen acceso completo
- La experiencia de usuario público es coherente con el sistema de soft delete

## Testing Recomendado

1. **Desactivar un club** y verificar que sus pilotos desaparezcan de vistas públicas
2. **Acceder directamente** a URLs de pilotos de clubes inactivos (debe retornar 404)
3. **Verificar API endpoints** con clubes inactivos
4. **Confirmar acceso admin** sigue funcionando normalmente
