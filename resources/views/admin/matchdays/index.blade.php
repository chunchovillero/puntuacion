@extends('layouts.admin')

@section('title', 'Gestión de Jornadas')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    Jornadas
                    @if(request('championship'))
                        @php $championship = \App\Models\Championship::find(request('championship')) @endphp
                        - {{ $championship->name }} ({{ $championship->year }})
                    @endif
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.championships.index') }}">Campeonatos</a></li>
                    @if(request('championship'))
                        <li class="breadcrumb-item"><a href="{{ route('admin.championships.show', $championship) }}">{{ $championship->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active">Jornadas</li>
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
                        <h3 class="card-title">Lista de Jornadas</h3>
                        @auth
                            <div class="card-tools">
                                @if(request('championship'))
                                    <a href="{{ route('admin.matchdays.create', ['championship' => request('championship')]) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nueva Jornada
                                    </a>
                                @else
                                    <a href="{{ route('admin.matchdays.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nueva Jornada
                                    </a>
                                @endif
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

                        <!-- Filtros -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Filtrar por Campeonato:</label>
                                <select class="form-control" id="championshipFilter">
                                    <option value="">Todos los campeonatos</option>
                                    @foreach(\App\Models\Championship::orderBy('year', 'desc')->orderBy('name')->get() as $champ)
                                        <option value="{{ $champ->id }}" {{ request('championship') == $champ->id ? 'selected' : '' }}>
                                            {{ $champ->name }} ({{ $champ->year }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Estado:</label>
                                <select class="form-control" id="statusFilter">
                                    <option value="">Todos los estados</option>
                                    <option value="scheduled">Programada</option>
                                    <option value="ongoing">En curso</option>
                                    <option value="completed">Completada</option>
                                    <option value="cancelled">Cancelada</option>
                                    <option value="postponed">Postergada</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-info btn-block" onclick="applyFilters()">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                            </div>
                        </div>

                        @if($matchdays->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Campeonato</th>
                                            <th>Jornada</th>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Organizador</th>
                                            <th>Pista</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($matchdays as $matchday)
                                            <tr>
                                                <td>{{ $matchday->id }}</td>
                                                <td>
                                                    <a href="{{ route('admin.championships.show', $matchday->championship) }}" 
                                                       class="text-primary">
                                                        {{ $matchday->championship->name }}
                                                    </a>
                                                    <br><small class="text-muted">{{ $matchday->championship->year }}</small>
                                                </td>
                                                <td>
                                                    <strong>Jornada {{ $matchday->number }}</strong>
                                                </td>
                                                <td>
                                                    {{ $matchday->date ? $matchday->date->format('d/m/Y') : '-' }}
                                                </td>
                                                <td>
                                                    {{ $matchday->start_time ?: '-' }}
                                                </td>
                                                <td>
                                                    @if($matchday->organizer_club_id)
                                                        <span class="badge badge-info">{{ $matchday->organizerClub->name }}</span>
                                                    @else
                                                        <span class="badge badge-success">AMBMX</span>
                                                    @endif
                                                </td>
                                                <td>{{ $matchday->venue ?: '-' }}</td>
                                                <td>
                                                    @switch($matchday->status)
                                                        @case('scheduled')
                                                            <span class="badge badge-secondary">Programada</span>
                                                            @break
                                                        @case('ongoing')
                                                            <span class="badge badge-warning">En curso</span>
                                                            @break
                                                        @case('completed')
                                                            <span class="badge badge-success">Completada</span>
                                                            @break
                                                        @case('cancelled')
                                                            <span class="badge badge-danger">Cancelada</span>
                                                            @break
                                                        @case('postponed')
                                                            <span class="badge badge-info">Postergada</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.matchdays.show', $matchday) }}" 
                                                           class="btn btn-info btn-sm" title="Ver">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @auth
                                                        <a href="{{ route('admin.matchdays.edit', $matchday) }}" 
                                                           class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('admin.matchdays.destroy', $matchday) }}" 
                                                              method="POST" style="display: inline;"
                                                              onsubmit="return confirm('¿Está seguro de eliminar esta jornada?')">
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
                                <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No hay jornadas registradas</h4>
                                <p class="text-muted">Comience creando la primera jornada</p>
                                @if(request('championship'))
                                    <a href="{{ route('admin.matchdays.create', ['championship' => request('championship')]) }}" 
                                       class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Crear Jornada
                                    </a>
                                @else
                                    <a href="{{ route('admin.matchdays.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Crear Jornada
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function applyFilters() {
    const championship = document.getElementById('championshipFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    let url = new URL(window.location.href);
    
    if (championship) {
        url.searchParams.set('championship', championship);
    } else {
        url.searchParams.delete('championship');
    }
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    window.location.href = url.toString();
}
</script>
@endsection
