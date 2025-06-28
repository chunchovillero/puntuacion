@extends('layouts.admin')

@section('title', 'Gestión de Campeonatos')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Campeonatos</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Campeonatos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Campeonatos</h3>
                        @auth
                        <div class="card-tools">
                            <a href="{{ route('admin.championships.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Nuevo Campeonato
                            </a>
                        </div>
                        @endauth
                    </div>
                    
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($championships->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Año</th>
                                            <th>Estado</th>
                                            <th>Jornadas</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Fin</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($championships as $championship)
                                            <tr>
                                                <td>{{ $championship->id }}</td>
                                                <td>{{ $championship->name }}</td>
                                                <td>
                                                    <span class="badge badge-info">{{ $championship->year }}</span>
                                                </td>
                                                <td>
                                                    @switch($championship->status)
                                                        @case('planned')
                                                            <span class="badge badge-secondary">Planeado</span>
                                                            @break
                                                        @case('active')
                                                            <span class="badge badge-success">Activo</span>
                                                            @break
                                                        @case('completed')
                                                            <span class="badge badge-primary">Completado</span>
                                                            @break
                                                        @case('cancelled')
                                                            <span class="badge badge-danger">Cancelado</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.matchdays.index', ['championship' => $championship->id]) }}" 
                                                       class="btn btn-info btn-xs">
                                                        <i class="fas fa-calendar"></i> {{ $championship->matchdays()->count() }} jornadas
                                                    </a>
                                                </td>
                                                <td>{{ $championship->start_date ? $championship->start_date->format('d/m/Y') : '-' }}</td>
                                                <td>{{ $championship->end_date ? $championship->end_date->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.championships.show', $championship) }}" 
                                                           class="btn btn-info btn-sm" title="Ver">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @auth
                                                        <a href="{{ route('admin.championships.edit', $championship) }}" 
                                                           class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('admin.championships.destroy', $championship) }}" 
                                                              method="POST" style="display: inline;"
                                                              onsubmit="return confirm('¿Está seguro de eliminar este campeonato?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        @endauth
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No hay campeonatos registrados</h4>
                                <p class="text-muted">Comience creando su primer campeonato</p>
                                <a href="{{ route('admin.championships.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear Campeonato
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
