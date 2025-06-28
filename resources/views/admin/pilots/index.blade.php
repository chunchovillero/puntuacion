@extends('layouts.admin')

@section('title', 'Gestión de Pilotos')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Pilotos BMX</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pilotos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Pilotos</h3>
                @auth
                <div class="card-tools">
                    <a href="{{ route('admin.pilots.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Piloto
                    </a>
                </div>
                @endauth
            </div>

            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Buscar piloto..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="club_id" class="form-control">
                                <option value="">Todos los clubes</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" {{ request('club_id') == $club->id ? 'selected' : '' }}>
                                        {{ $club->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="category_id" class="form-control">
                                <option value="">Todas las categorías</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.pilots.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Apodo</th>
                                <th>Club</th>
                                <th>Edad</th>
                                <th>Categoría</th>
                                <th>Puntos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pilots as $pilot)
                                <tr>
                                    <td class="text-center">
                                        @if($pilot->photo_path)
                                            <img src="{{ asset('storage/' . $pilot->photo_path) }}" 
                                                 alt="{{ $pilot->full_name }}" 
                                                 class="img-circle" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $pilot->full_name }}</strong>
                                        @if($pilot->email)
                                            <br><small class="text-muted">{{ $pilot->email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pilot->nickname)
                                            <span class="badge badge-info">{{ $pilot->nickname }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pilot->club)
                                            <a href="{{ route('admin.clubs.show', $pilot->club) }}" class="text-decoration-none">
                                                {{ $pilot->club->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">Sin club</span>
                                        @endif
                                    </td>
                                    <td>{{ $pilot->age }} años</td>
                                    <td>
                                        @if($pilot->category)
                                            <span class="badge badge-{{ $pilot->category->type == 'novicios' ? 'info' : ($pilot->category->type == 'escuela' ? 'primary' : 'success') }}">
                                                {{ $pilot->category->name }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">Sin categoría</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ number_format($pilot->ranking_points ?? 0) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $pilot->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ $pilot->status == 'active' ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.pilots.show', $pilot) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @auth
                                            <a href="{{ route('admin.pilots.edit', $pilot) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.pilots.destroy', $pilot) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('¿Estás seguro de que deseas eliminar este piloto?')">
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
                                    <td colspan="9" class="text-center text-muted">
                                        No se encontraron pilotos
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($pilots->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $pilots->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
