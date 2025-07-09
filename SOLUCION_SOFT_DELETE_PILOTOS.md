# Implementación de Soft Delete para Pilotos

## Problema Original
El usuario solicitó que al "eliminar" un piloto, este no se elimine físicamente de la base de datos, sino que solo se marque como inactivo. Esto es una práctica común conocida como "soft delete" que preserva la integridad de los datos históricos.

## Cambios Implementados

### 1. Backend - Controlador de Pilotos

#### a) Método `destroy()` (Web Routes)
**Archivo**: `app/Http/Controllers/Admin/PilotController.php`

```php
public function destroy(Pilot $pilot)
{
    // En lugar de eliminar, cambiar el status a inactivo
    $pilot->update(['status' => 'inactive']);
    
    return redirect()->route('admin.pilots.index')
                    ->with('success', 'Piloto desactivado exitosamente.');
}
```

#### b) Método `apiDestroy()` (API Routes)
```php
public function apiDestroy(Pilot $pilot)
{
    try {
        $pilotName = $pilot->first_name . ' ' . $pilot->last_name;
        
        // En lugar de eliminar, cambiar el status a inactivo
        $pilot->update(['status' => 'inactive']);

        return response()->json([
            'success' => true,
            'message' => "Piloto '{$pilotName}' desactivado exitosamente"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al desactivar el piloto: ' . $e->getMessage()
        ], 500);
    }
}
```

#### c) Nuevo método `apiReactivate()` (API Routes)
```php
public function apiReactivate(Pilot $pilot)
{
    try {
        $pilotName = $pilot->first_name . ' ' . $pilot->last_name;
        
        // Cambiar el status a activo
        $pilot->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => "Piloto '{$pilotName}' reactivado exitosamente"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al reactivar el piloto: ' . $e->getMessage()
        ], 500);
    }
}
```

### 2. Rutas API

#### Nueva ruta para reactivar pilotos
**Archivo**: `routes/api.php`

```php
Route::patch('/{pilot}/reactivate', [PilotController::class, 'apiReactivate']);
```

### 3. Frontend - PilotDetail.vue

#### a) Botones dinámicos según el estado del piloto
```vue
<!-- Botón de desactivar solo para pilotos activos -->
<button 
    @click="confirmDelete" 
    class="btn btn-outline-warning btn-sm mr-2"
    v-if="canDelete && pilot.status === 'active'"
>
    <i class="fas fa-user-slash"></i> Desactivar
</button>

<!-- Botón de reactivar solo para pilotos inactivos -->
<button 
    @click="confirmReactivate" 
    class="btn btn-outline-success btn-sm mr-2"
    v-if="canDelete && pilot.status === 'inactive'"
>
    <i class="fas fa-user-check"></i> Reactivar
</button>
```

#### b) Métodos actualizados
```javascript
// Confirmación de desactivación
async confirmDelete() {
    if (confirm(`¿Estás seguro de desactivar al piloto "${this.pilot.first_name} ${this.pilot.last_name}"? El piloto quedará inactivo pero no se eliminará de la base de datos.`)) {
        await this.deletePilot();
    }
},

// Método de desactivación
async deletePilot() {
    // ... lógica actualizada con mensaje "desactivado exitosamente"
},

// Nuevo método de confirmación de reactivación
async confirmReactivate() {
    if (confirm(`¿Estás seguro de reactivar al piloto "${this.pilot.first_name} ${this.pilot.last_name}"? El piloto volverá a estar activo.`)) {
        await this.reactivatePilot();
    }
},

// Nuevo método de reactivación
async reactivatePilot() {
    try {
        const response = await fetch(`/api/pilots/${this.pilot.id}/reactivate`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        });

        if (response.ok) {
            this.showSuccessMessage('Piloto reactivado exitosamente');
            await this.loadPilot(); // Recargar datos
        } else {
            alert('Error al reactivar el piloto');
        }
    } catch (error) {
        console.error('Error reactivating pilot:', error);
        alert('Error al reactivar el piloto');
    }
}
```

### 4. Frontend - PilotManager.vue

#### a) Botones dinámicos en vistas de tarjetas y tabla
```vue
<!-- Botón de desactivar solo para pilotos activos -->
<button 
    v-if="canDelete && pilot.status === 'active'" 
    @click="confirmDelete(pilot.id, pilot.first_name + ' ' + pilot.last_name)" 
    class="btn btn-outline-warning btn-sm"
>
    <i class="fas fa-user-slash"></i>
    Desactivar
</button>

<!-- Botón de reactivar solo para pilotos inactivos -->
<button 
    v-if="canDelete && pilot.status === 'inactive'" 
    @click="confirmReactivate(pilot.id, pilot.first_name + ' ' + pilot.last_name)" 
    class="btn btn-outline-success btn-sm"
>
    <i class="fas fa-user-check"></i>
    Reactivar
</button>
```

#### b) Métodos actualizados y nuevos
```javascript
// Métodos de confirmación y acción actualizados
async confirmDelete(pilotId, pilotName) {
    if (confirm(`¿Estás seguro de desactivar al piloto "${pilotName}"? El piloto quedará inactivo pero no se eliminará de la base de datos.`)) {
        await this.deletePilot(pilotId);
    }
},

async deletePilot(pilotId) {
    // ... mensaje actualizado: "Piloto desactivado exitosamente"
},

// Nuevos métodos para reactivación
async confirmReactivate(pilotId, pilotName) {
    if (confirm(`¿Estás seguro de reactivar al piloto "${pilotName}"? El piloto volverá a estar activo.`)) {
        await this.reactivatePilot(pilotId);
    }
},

async reactivatePilot(pilotId) {
    try {
        const response = await fetch(`/api/pilots/${pilotId}/reactivate`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        });

        if (response.ok) {
            this.showNotification('Piloto reactivado exitosamente', 'success');
            this.loadPilots();
        } else {
            throw new Error('Error al reactivar el piloto');
        }
    } catch (error) {
        console.error('Error reactivating pilot:', error);
        this.showNotification('Error al reactivar el piloto', 'error');
    }
}
```

## Cambios en la UI

### Iconos y Colores
- **Desactivar**: `fas fa-user-slash` con `btn-outline-warning` (amarillo)
- **Reactivar**: `fas fa-user-check` con `btn-outline-success` (verde)

### Mensajes de Confirmación
- **Desactivar**: "¿Estás seguro de desactivar al piloto...? El piloto quedará inactivo pero no se eliminará de la base de datos."
- **Reactivar**: "¿Estás seguro de reactivar al piloto...? El piloto volverá a estar activo."

### Notificaciones de Éxito
- **Desactivar**: "Piloto desactivado exitosamente"
- **Reactivar**: "Piloto reactivado exitosamente"

## Beneficios de esta Implementación

1. **Preservación de datos**: Los pilotos nunca se eliminan físicamente
2. **Integridad referencial**: Se mantienen las relaciones con competiciones y puntuaciones
3. **Reversibilidad**: Los pilotos pueden ser reactivados fácilmente
4. **UI intuitiva**: Botones contextuales según el estado del piloto
5. **Feedback claro**: Mensajes específicos para cada acción

## Archivos Modificados

### Backend
- `app/Http/Controllers/Admin/PilotController.php`
- `routes/api.php`

### Frontend
- `resources/js/components/forms/PilotDetail.vue`
- `resources/js/components/PilotManager.vue`

## Comandos Ejecutados
```bash
cd "c:\wamp64\www\puntuacion"
npm run dev
```

## Estado Final
✅ **COMPLETADO**: El sistema de soft delete para pilotos está completamente implementado y funcional.

Los pilotos ahora se desactivan en lugar de eliminarse, preservando todos los datos históricos y permitiendo su reactivación posterior cuando sea necesario.
