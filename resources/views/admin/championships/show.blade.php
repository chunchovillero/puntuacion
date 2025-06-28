@extends('layouts.admin')

@section('title', 'Detalle del Campeonato')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $championship->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.championships.index') }}">Campeonatos</a></li>
                    <li class="breadcrumb-item active">{{ $championship->name }}</li>
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

        <div class="row">
            <!-- Información del Campeonato -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-trophy"></i> Información del Campeonato
                        </h3>
                        <div class="card-tools">
                            @auth
                            <a href="{{ route('admin.championships.edit', $championship) }}" 
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            @endauth
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nombre:</strong>
                                <p class="text-muted">{{ $championship->name }}</p>

                                <strong>Año:</strong>
                                <p class="text-muted">{{ $championship->year }}</p>

                                <strong>Estado:</strong>
                                <p>
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
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>Fecha de Inicio:</strong>
                                <p class="text-muted">{{ $championship->start_date ? $championship->start_date->format('d/m/Y') : 'No definida' }}</p>

                                <strong>Fecha de Fin:</strong>
                                <p class="text-muted">{{ $championship->end_date ? $championship->end_date->format('d/m/Y') : 'No definida' }}</p>

                                <strong>Jornadas:</strong>
                                <p class="text-muted">{{ $championship->matchdays()->count() }} registradas</p>
                            </div>
                        </div>

                        @if($championship->description)
                            <strong>Descripción:</strong>
                            <p class="text-muted">{{ $championship->description }}</p>
                        @endif

                        @if($championship->rules)
                            <strong>Reglamento:</strong>
                            <div class="text-muted" style="white-space: pre-line;">{{ $championship->rules }}</div>
                        @endif

                        @if($championship->prizes)
                            <strong>Premios:</strong>
                            <div class="text-muted" style="white-space: pre-line;">{{ $championship->prizes }}</div>
                        @endif
                    </div>
                </div>

                <!-- Jornadas del Campeonato -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar"></i> Jornadas del Campeonato
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.matchdays.create', ['championship' => $championship->id]) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Nueva Jornada
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($championship->matchdays()->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Jornada</th>
                                            <th>Fecha</th>
                                            <th>Organizador</th>
                                            <th>Pista</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($championship->matchdays as $matchday)
                                            <tr>
                                                <td>
                                                    <strong>Jornada {{ $matchday->number }}</strong>
                                                </td>
                                                <td>
                                                    {{ $matchday->date ? $matchday->date->format('d/m/Y') : '-' }}
                                                    @if($matchday->start_time)
                                                        <br><small class="text-muted">{{ $matchday->start_time }}</small>
                                                    @endif
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
                                                        @case('in_progress')
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
                                                           class="btn btn-info btn-xs" title="Ver">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.matchdays.edit', $matchday) }}" 
                                                           class="btn btn-warning btn-xs" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
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
                                <h4 class="text-muted">No hay jornadas programadas</h4>
                                <p class="text-muted">Comience agregando la primera jornada del campeonato</p>
                                <a href="{{ route('admin.matchdays.create', ['championship' => $championship->id]) }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Crear Primera Jornada
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel lateral con acciones -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Acciones Rápidas</h3>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical btn-block">
                            @auth
                            <a href="{{ route('admin.championships.edit', $championship) }}" 
                               class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar Campeonato
                            </a>
                            <a href="{{ route('admin.matchdays.create', ['championship' => $championship->id]) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus"></i> Agregar Jornada
                            </a>
                            @endauth
                            <a href="{{ route('admin.matchdays.index', ['championship' => $championship->id]) }}" 
                               class="btn btn-info">
                                <i class="fas fa-list"></i> Ver Jornadas
                            </a>
                            <a href="{{ route('admin.championships.index') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver a Campeonatos
                            </a>
                        </div>
                    </div>
                                <i class="fas fa-calendar"></i> Ver Todas las Jornadas
                            </a>
                            <a href="{{ route('admin.championships.index') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver a Campeonatos
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Estadísticas</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="description-block border-right">
                                    <span class="description-percentage text-success">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <h5 class="description-header">{{ $championship->matchdays()->count() }}</h5>
                                    <span class="description-text">JORNADAS</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <span class="description-percentage text-info">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <h5 class="description-header">{{ $championship->matchdays()->where('status', 'completed')->count() }}</h5>
                                    <span class="description-text">COMPLETADAS</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Información</h3>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <strong>Creado:</strong> {{ $championship->created_at->format('d/m/Y H:i') }}<br>
                            <strong>Actualizado:</strong> {{ $championship->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
