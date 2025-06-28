@extends('layouts.admin')

@section('title', 'Gestión de Clubes BMX')
@section('page-title', 'Clubes BMX')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Clubes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Lista de Clubes BMX
                </h3>
                @auth
                    <div class="card-tools">
                        <a href="{{ route('admin.clubs.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Club
                        </a>
                    </div>
                @endauth
            </div>
            
            <!-- Filtros -->
            <div class="card-header">
                <form method="GET" class="form-inline">
                    <div class="input-group input-group-sm mr-3">
                        <input type="text" name="search" class="form-control" placeholder="Buscar clubes..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <select name="status" class="form-control form-control-sm mr-3">
                        <option value="">Todos los estados</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivos</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendidos</option>
                    </select>
                    
                    <button type="submit" class="btn btn-secondary btn-sm mr-2">Filtrar</button>
                    <a href="{{ route('admin.clubs.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
                </form>
            </div>

            <div class="card-body table-responsive p-0">
                @if($clubs->count() > 0)
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Pilotos</th>
                            <th>Estado</th>
                            <th>Fundado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clubs as $club)
                        <tr>
                            <td>
                                @if($club->logo)
                                    <img src="{{ asset('storage/' . $club->logo) }}" alt="{{ $club->name }}" class="img-circle elevation-2" width="40" height="40">
                                @else
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $club->name }}</strong>
                                @if($club->website)
                                    <br><small><a href="{{ $club->website }}" target="_blank" class="text-muted">{{ $club->website }}</a></small>
                                @endif
                            </td>
                            <td>
                                {{ $club->city }}{{ $club->city && $club->state ? ', ' : '' }}{{ $club->state }}
                                <br><small class="text-muted">{{ $club->country }}</small>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $club->pilots->count() }} pilotos</span>
                                <br><small class="text-muted">{{ $club->activePilots->count() }} activos</small>
                            </td>
                            <td>
                                @switch($club->status)
                                    @case('active')
                                        <span class="badge badge-success">Activo</span>
                                        @break
                                    @case('inactive')
                                        <span class="badge badge-secondary">Inactivo</span>
                                        @break
                                    @case('suspended')
                                        <span class="badge badge-danger">Suspendido</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                {{ $club->founded_date ? $club->founded_date->format('d/m/Y') : 'No especificado' }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.clubs.show', $club) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @auth
                                        <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.clubs.destroy', $club) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar" 
                                                    onclick="return confirm('¿Estás seguro de eliminar este club?')">
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
                @else
                <div class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay clubes registrados</h4>
                    @auth
                        <p class="text-muted">Comienza agregando el primer club BMX</p>
                        <a href="{{ route('admin.clubs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Primer Club
                        </a>
                    @else
                        <p class="text-muted">Aún no hay clubes en el sistema</p>
                    @endauth
                </div>
                @endif
            </div>
            
            @if($clubs->hasPages())
            <div class="card-footer">
                {{ $clubs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table td {
    vertical-align: middle;
}
.btn-group .btn {
    margin-right: 2px;
}
</style>
@endpush
