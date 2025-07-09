# 🏁 PLANILLA DE CARRERAS BMX - DOCUMENTACIÓN

## 📋 Descripción General

La **Planilla de Carreras** es una nueva funcionalidad que permite organizar y gestionar todas las carreras de una jornada BMX. Cada jornada puede tener múltiples series por categoría, y cada serie contiene 3 mangas (carreras) con un máximo de 8 pilotos cada una.

## 🏗️ Estructura de Datos

### 📊 Jerarquía
```
Jornada (Matchday)
├── Serie A - Categoría X (RaceSeries)
│   ├── Manga 1 (RaceHeat)
│   │   ├── Piloto 1 - Partidor 1 (RaceLineup)
│   │   ├── Piloto 2 - Partidor 2 (RaceLineup)
│   │   └── ... (hasta 8 pilotos)
│   ├── Manga 2 (RaceHeat)
│   └── Manga 3 (RaceHeat)
├── Serie B - Categoría X (si hay más de 8 pilotos)
└── Serie A - Categoría Y
```

### 🗃️ Tablas Creadas

#### `race_series` - Series de Carreras
- `id` - Identificador único
- `matchday_id` - Jornada a la que pertenece
- `category_id` - Categoría de la serie
- `name` - Nombre de la serie (ej: "Serie A", "Serie B")
- `series_number` - Número de orden (1, 2, 3...)
- `max_pilots` - Máximo de pilotos (por defecto 8)
- `transfer_to_final` - Cuántos pilotos avanzan a final
- `transfer_to_semifinal` - Cuántos pilotos avanzan a semifinal
- `transfer_to_quarterfinal` - Cuántos pilotos avanzan a cuartos
- `notes` - Notas adicionales

#### `race_heats` - Mangas/Carreras
- `id` - Identificador único
- `race_series_id` - Serie a la que pertenece
- `heat_number` - Número de manga (1, 2, 3)
- `name` - Nombre de la manga (ej: "Manga 1", "Manga 2")
- `scheduled_time` - Hora programada (opcional)
- `status` - Estado: scheduled, in_progress, completed, cancelled
- `notes` - Notas adicionales

#### `race_lineups` - Posiciones en el Partidor
- `id` - Identificador único
- `race_heat_id` - Manga a la que pertenece
- `pilot_id` - Piloto asignado
- `gate_position` - Posición en el partidor (1-8)
- `finish_position` - Posición de llegada (1-8, nullable)
- `lap_time` - Tiempo de vuelta en segundos (nullable)
- `dnf` - No terminó (boolean)
- `dsq` - Descalificado (boolean)
- `notes` - Notas adicionales

## 🎯 Funcionalidades Implementadas

### ✅ Gestión de Series
- **Creación automática**: Generar series para todas las categorías automáticamente
- **Creación manual**: Crear series individuales por categoría
- **Configuración de transferencias**: Definir cuántos pilotos avanzan a cada ronda
- **Edición de series**: Modificar configuración y asignaciones

### ✅ Gestión de Mangas
- **Creación automática**: Al crear una serie se generan automáticamente 3 mangas
- **Estados de manga**: Programada, en progreso, completada, cancelada
- **Gestión de horarios**: Asignar horarios específicos a cada manga

### ✅ Asignación de Pilotos
- **Asignación automática**: Distribuir pilotos aleatoriamente en las posiciones
- **Posiciones del partidor**: Asignar posiciones del 1 al 8 en cada manga
- **Validaciones**: Solo pilotos inscritos en la jornada y categoría correspondiente

### ✅ Visualización
- **Tabla estilo planilla**: Formato similar a las planillas tradicionales de BMX
- **Información por categoría**: Agrupación clara por categorías
- **Estados visuales**: Badges y colores para identificar estados
- **Responsive**: Diseño adaptativo para dispositivos móviles

## 🔗 Rutas Implementadas

### Rutas Administrativas (Autenticadas)
```php
// Ver planilla principal
GET /jornadas/{matchday}/planilla

// Generar todas las series automáticamente
POST /jornadas/{matchday}/planilla/generar-todas

// Crear serie individual
POST /jornadas/{matchday}/planilla/crear-serie

// Editar serie específica
GET /jornadas/{matchday}/planilla/serie/{series}/editar
PUT /jornadas/{matchday}/planilla/serie/{series}

// Eliminar serie
DELETE /jornadas/{matchday}/planilla/serie/{series}

// Asignar pilotos a serie
POST /jornadas/{matchday}/planilla/serie/{series}/asignar-pilotos

// Actualizar lineup de manga
PUT /jornadas/{matchday}/planilla/manga/{heat}/lineup
```

## 🚀 Acceso a la Funcionalidad

### Desde la Vista de Jornada
1. Ir a **Jornadas** > [Seleccionar Jornada]
2. En la sección "Participantes y Resultados" hacer clic en **"Planilla de Carreras"**
3. Se abrirá la vista principal de la planilla

### Desde la Vista Pública de Participantes
- Hay un enlace **"Ver Planilla de Carreras"** en la parte superior

## 📝 Flujo de Trabajo Recomendado

### 1. Preparación
1. Asegurar que hay pilotos inscritos en la jornada
2. Verificar que las categorías están correctamente asignadas

### 2. Creación de Series
**Opción A - Automática (Recomendada)**:
1. Hacer clic en **"Generar Todas las Series"**
2. Seleccionar cuántos pilotos por serie (6, 7 u 8)
3. Se crearán automáticamente todas las series necesarias

**Opción B - Manual**:
1. Para cada categoría, hacer clic en **"Agregar Serie"**
2. Configurar transferencias y opciones
3. Asignar pilotos manualmente

### 3. Gestión de Posiciones
1. Usar la funcionalidad de edición para ajustar posiciones específicas
2. Las posiciones se asignan aleatoriamente por defecto
3. Se pueden reorganizar manualmente según necesidades

### 4. Durante las Carreras
1. Actualizar estados de las mangas (en progreso, completada)
2. Registrar tiempos y posiciones de llegada
3. Marcar DNF o DSQ según corresponda

## 🎨 Características de Diseño

### 📊 Tabla Estilo Planilla Tradicional
La visualización imita el formato tradicional de las planillas BMX:

```
Serie A - Transferencia 4 a final
--------------------------------------------------------
M1 | M2 | M3 | CLUB      | PILOTO           | DORSAL
--------------------------------------------------------
1  | 5  | 8  | Club BMX  | Juan Pérez       | 101
2  | 6  | 6  | Club ABC  | María González   | 102
3  | 8  | 5  | Club XYZ  | Pedro Martínez   | 103
--------------------------------------------------------
```

### 🏷️ Sistema de Estados y Badges
- **Serie**: Configuración de transferencias visible
- **Mangas**: Estados con colores (programada, en progreso, completada)
- **Resultados**: Badges para posiciones, DNF, DSQ
- **Dorsales**: Números destacados para fácil identificación

### 📱 Diseño Responsive
- Tablas responsivas que se adaptan a pantallas pequeñas
- Botones reorganizados en móviles
- Información condensada pero legible

## 🔧 Configuración Técnica

### Modelos Principales
- `RaceSeries` - Gestión de series
- `RaceHeat` - Gestión de mangas
- `RaceLineup` - Gestión de posiciones
- Relaciones con `Matchday`, `Category`, `Pilot`

### Controlador Principal
- `RaceSheetController` - Lógica de negocio principal
- Métodos para CRUD de series, asignación automática, gestión de lineups

### Migraciones
- Tres nuevas tablas con relaciones apropiadas
- Índices para optimizar consultas
- Restricciones de integridad referencial

## 🚧 Funcionalidades Futuras (En Desarrollo)

### 🔄 Próximas Versiones
- **Edición en tiempo real**: Actualización de posiciones vía AJAX
- **Drag & Drop**: Reorganizar pilotos arrastrando y soltando
- **Cronometraje**: Integración con sistemas de cronometraje
- **Reportes**: Generación de PDFs de planillas
- **API**: Endpoints para integración con apps móviles

### 🎯 Mejoras Planificadas
- **Validaciones avanzadas**: Verificar conflictos de horarios
- **Historial de cambios**: Auditoría de modificaciones
- **Notificaciones**: Avisos de cambios en tiempo real
- **Sincronización**: Backup automático de datos

## 🐛 Problemas Conocidos

### ⚠️ Limitaciones Actuales
1. **Base de datos**: Requiere que MySQL esté ejecutándose
2. **Edición manual**: Algunas funciones de edición están en desarrollo
3. **Validaciones**: Algunas validaciones avanzadas pendientes

### 🔨 Soluciones Temporales
1. Asegurar que WAMP/XAMPP esté ejecutándose antes de usar
2. Usar generación automática para casos complejos
3. Verificar manualmente las asignaciones

## 📞 Soporte

Para cualquier problema o sugerencia:
1. Verificar que la base de datos esté funcionando
2. Revisar los logs de Laravel en `storage/logs/`
3. Consultar la documentación de Laravel para funcionalidades avanzadas

---

**Versión**: 1.0.0  
**Fecha**: Enero 2025  
**Estado**: Funcional - En desarrollo activo
