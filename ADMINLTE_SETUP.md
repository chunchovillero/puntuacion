# Sistema BMX con AdminLTE

## 🎨 Implementación de AdminLTE

Se ha implementado **AdminLTE 3.2** en el proyecto Laravel + Vue.js SPA para proporcionar una interfaz moderna y profesional similar a http://intranet.ambmx.com/.

### ✅ Cambios Realizados

#### 1. **Instalación de AdminLTE**
- AdminLTE 3.2 ya estaba instalado via npm
- Font Awesome añadido para iconos completos
- CSS y JS de AdminLTE importados correctamente

#### 2. **Estructura de Plantilla**
```html
<!-- app.blade.php -->
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper" id="app">
        <!-- Vue SPA se monta aquí -->
    </div>
</body>
```

#### 3. **Componente AppLayout**
- **Navbar superior** con toggle del sidebar y opciones de usuario
- **Sidebar lateral** con navegación completa por módulos
- **Área de contenido** principal para router-view
- **Footer** con información del sistema

#### 4. **Estructura de Páginas**
Las páginas ahora siguen la estructura AdminLTE:

```vue
<template>
    <div class="page-name">
        <!-- Content Header (breadcrumbs) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Título de Página</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item active">Página Actual</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Cards, tablas, formularios aquí -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Contenido</h3>
                    </div>
                    <div class="card-body">
                        <!-- Contenido principal -->
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
```

### 🎯 Componentes AdminLTE Disponibles

#### **Cards**
```vue
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Título</h3>
        <div class="card-tools">
            <!-- Botones de herramientas -->
        </div>
    </div>
    <div class="card-body">
        <!-- Contenido -->
    </div>
    <div class="card-footer">
        <!-- Footer opcional -->
    </div>
</div>
```

#### **Small Boxes (Estadísticas)**
```vue
<div class="small-box bg-info">
    <div class="inner">
        <h3>{{ number }}</h3>
        <p>Descripción</p>
    </div>
    <div class="icon">
        <i class="fas fa-icon"></i>
    </div>
    <router-link to="/ruta" class="small-box-footer">
        Ver más <i class="fas fa-arrow-circle-right"></i>
    </router-link>
</div>
```

#### **Botones**
```vue
<!-- Botones principales -->
<button class="btn btn-primary">Primario</button>
<button class="btn btn-success">Éxito</button>
<button class="btn btn-warning">Advertencia</button>
<button class="btn btn-danger">Peligro</button>

<!-- Tamaños -->
<button class="btn btn-primary btn-sm">Pequeño</button>
<button class="btn btn-primary">Normal</button>
<button class="btn btn-primary btn-lg">Grande</button>

<!-- Con iconos -->
<button class="btn btn-primary">
    <i class="fas fa-plus"></i> Nuevo
</button>
```

#### **Formularios**
```vue
<div class="form-group">
    <label for="input">Etiqueta</label>
    <input type="text" class="form-control" id="input" placeholder="Placeholder">
</div>

<div class="form-group">
    <label for="select">Seleccionar</label>
    <select class="form-control" id="select">
        <option>Opción 1</option>
        <option>Opción 2</option>
    </select>
</div>
```

#### **Tablas**
```vue
<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Columna 1</th>
                <th>Columna 2</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Dato 1</td>
                <td>Dato 2</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

### 🚀 Para Desarrolladores

#### **Añadir Nueva Página**
1. Crea el componente con estructura AdminLTE
2. Añade la ruta en `router/index.js`
3. Actualiza la navegación en `AppLayout.vue`

#### **Iconos Disponibles**
- Font Awesome 5 (ejemplo: `fas fa-user`, `fas fa-cog`, `fas fa-chart-bar`)
- AdminLTE incluye iconos específicos

#### **Clases de Color**
- `bg-primary`, `bg-success`, `bg-warning`, `bg-danger`, `bg-info`
- `text-primary`, `text-success`, `text-warning`, `text-danger`, `text-info`

#### **Utilidades de Espaciado**
- Márgenes: `m-0`, `m-1`, `m-2`, `m-3`, `m-4`, `m-5`
- Padding: `p-0`, `p-1`, `p-2`, `p-3`, `p-4`, `p-5`
- Específicos: `mt-2` (margin-top), `pb-3` (padding-bottom)

### 📱 Responsive Design

AdminLTE es totalmente responsive:
- **Sidebar automático**: Se colapsa en móviles
- **Cards adaptables**: Se ajustan al ancho de pantalla
- **Tablas responsive**: Con scroll horizontal automático
- **Navegación móvil**: Menu hamburguesa funcional

### 🎨 Personalización

Para personalizar colores y estilos, edita:
```scss
// resources/sass/app.scss
// Custom styles
.content-wrapper {
    padding: 20px;
}

.card {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}
```

### 🔧 Build y Deploy

```bash
# Desarrollo
npm run dev

# Producción
npm run production

# Watch mode
npm run watch
```

### ✨ Resultado

El sistema ahora tiene la misma apariencia profesional que http://intranet.ambmx.com/ con:
- Sidebar lateral con navegación completa
- Header con branding y opciones de usuario
- Cards y componentes modernos
- Iconografía consistente
- Responsive design completo
- Animaciones y transiciones suaves
