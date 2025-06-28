@extends('layouts.admin')

@section('title', 'Pilotos del Club')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Pilotos de {{ $club->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.clubs.index') }}">Clubes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.clubs.show', $club) }}">{{ $club->name }}</a></li>
                    <li class="breadcrumb-item active">Pilotos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Información del club -->
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        @if($club->logo)
                            <img src="{{ asset('storage/' . $club->logo) }}" 
                                 alt="{{ $club->name }}" 
                                 class="img-fluid"
                                 style="max-width: 80px; max-height: 80px; object-fit: contain;">
                        @else
                            <div class="bg-secondary rounded d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-users fa-2x text-white"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col">
                        <h4 class="mb-1">{{ $club->name }}</h4>
                        @if($club->city)
                            <p class="text-muted mb-0">{{ $club->city }}</p>
                        @endif
                        <small class="text-muted">
                            Total de pilotos: {{ $pilots->total() }}
                        </small>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.clubs.show', $club) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Ver Club
                        </a>
                        @auth
                        <a href="{{ route('admin.pilots.create') }}?club_id={{ $club->id }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Piloto
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de pilotos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pilotos Registrados</h3>
            </div>

            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Buscar piloto..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-8">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('admin.pilots.by-club', $club) }}" class="btn btn-secondary">
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
                                <th>Edad</th>
                                <th>Nivel</th>
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
                                    <td>{{ $pilot->age }} años</td>
                                    <td>
                                        <span class="badge badge-{{ $pilot->experience_level == 'profesional' ? 'danger' : ($pilot->experience_level == 'avanzado' ? 'warning' : ($pilot->experience_level == 'intermedio' ? 'info' : 'secondary')) }}">
                                            {{ ucfirst($pilot->experience_level) }}
                                        </span>
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
                                    <td colspan="8" class="text-center text-muted">
                                        No hay pilotos registrados en este club
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
