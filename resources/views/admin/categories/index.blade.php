@extends('layouts.admin')

@section('title', 'Gestión de Categorías')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Categorías</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Categorías</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Categorías</h3>
                @auth
                <div class="card-tools">
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Categoría
                    </a>
                </div>
                @endauth
            </div>

            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Buscar categorías..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-control">
                                <option value="">Todos los tipos</option>
                                <option value="escuela" {{ request('type') == 'escuela' ? 'selected' : '' }}>Escuela</option>
                                <option value="novicios" {{ request('type') == 'novicios' ? 'selected' : '' }}>Novicios</option>
                                <option value="juvenil" {{ request('type') == 'juvenil' ? 'selected' : '' }}>Juvenil</option>
                                <option value="adulto" {{ request('type') == 'adulto' ? 'selected' : '' }}>Adulto</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="gender" class="form-control">
                                <option value="">Todos los géneros</option>
                                <option value="varones" {{ request('gender') == 'varones' ? 'selected' : '' }}>Varones</option>
                                <option value="mujeres" {{ request('gender') == 'mujeres' ? 'selected' : '' }}>Mujeres</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activa</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactiva</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Tabla de categorías -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Género</th>
                                <th>Rango de Edad</th>
                                <th>Pilotos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                        @if($category->description)
                                            <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $category->type == 'novicios' ? 'info' : ($category->type == 'escuela' ? 'primary' : 'success') }}">
                                            {{ ucfirst($category->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($category->gender)
                                            <span class="badge badge-{{ $category->gender == 'varones' ? 'primary' : 'pink' }}">
                                                {{ ucfirst($category->gender) }}
                                            </span>
                                        @else
                                            <span class="text-muted">Mixto</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($category->age_min && $category->age_max)
                                            {{ $category->age_min }}-{{ $category->age_max }} años
                                        @elseif($category->age_min)
                                            {{ $category->age_min }}+ años
                                        @elseif($category->age_max)
                                            hasta {{ $category->age_max }} años
                                        @else
                                            <span class="text-muted">Sin límite</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $category->pilots_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @auth
                                        <form action="{{ route('admin.categories.toggle-status', $category) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $category->active ? 'btn-success' : 'btn-secondary' }}">
                                                @if($category->active)
                                                    <i class="fas fa-check"></i> Activa
                                                @else
                                                    <i class="fas fa-times"></i> Inactiva
                                                @endif
                                            </button>
                                        </form>
                                        @else
                                        <span class="badge badge-{{ $category->active ? 'success' : 'secondary' }}">
                                            @if($category->active)
                                                <i class="fas fa-check"></i> Activa
                                            @else
                                                <i class="fas fa-times"></i> Inactiva
                                            @endif
                                        </span>
                                        @endauth
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.categories.show', $category) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @auth
                                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $category) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('¿Está seguro de eliminar esta categoría?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-sm" 
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endauth
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i> No hay categorías registradas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($categories->hasPages())
                    <div class="mt-3">
                        {{ $categories->appends(request()->all())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
