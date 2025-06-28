@extends('layouts.admin')

@section('title', 'Detalle de la Jornada')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Jornada {{ $matchday->number }}</h1>
                <small class="text-muted">{{ $matchday->championship->name }} ({{ $matchday->championship->year }})</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.championships.index') }}">Campeonatos</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.championships.show', $matchday->championship) }}">{{ $matchday->championship->name }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.matchdays.index', ['championship' => $matchday->championship_id]) }}">Jornadas</a></li>
                    <li class="breadcrumb-item active">Jornada {{ $matchday->number }}</li>
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
            <!-- Información de la Jornada -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar"></i> Información de la Jornada
                        </h3>
                        @auth
                        <div class="card-tools">
                            <a href="{{ route('admin.matchdays.edit', $matchday) }}" 
                               class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                        @endauth
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Campeonato:</strong>
                                <p class="text-muted">
                                    <a href="{{ route('admin.championships.show', $matchday->championship) }}" class="text-primary">
                                        {{ $matchday->championship->name }}
                                    </a>
                                    ({{ $matchday->championship->year }})
                                </p>

                                <strong>Número de Jornada:</strong>
                                <p class="text-muted">Jornada {{ $matchday->number }}</p>

                                <strong>Estado:</strong>
                                <p>
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
                                </p>

                                <strong>Organizador:</strong>
                                <p>
                                    @if($matchday->organizer_club_id)
                                        <span class="badge badge-info">{{ $matchday->organizerClub->name }}</span>
                                    @else
                                        <span class="badge badge-success">AMBMX</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>Fecha:</strong>
                                <p class="text-muted">{{ $matchday->date ? $matchday->date->format('d/m/Y') : 'No definida' }}</p>

                                <strong>Hora de Inicio:</strong>
                                <p class="text-muted">{{ $matchday->start_time ?: 'No definida' }}</p>

                                <strong>Pista:</strong>
                                <p class="text-muted">{{ $matchday->venue ?: 'No definida' }}</p>

                                @if($matchday->max_participants)
                                    <strong>Máximo de Participantes:</strong>
                                    <p class="text-muted">{{ $matchday->max_participants }} pilotos</p>
                                @endif
                            </div>
                        </div>

                        @if($matchday->address)
                            <strong>Dirección de la Pista:</strong>
                            <p class="text-muted">{{ $matchday->address }}</p>
                        @endif

                        @if($matchday->notes)
                            <strong>Notas:</strong>
                            <div class="text-muted" style="white-space: pre-line;">{{ $matchday->notes }}</div>
                        @endif
                    </div>
                </div>

                <!-- Sección para futuros resultados o participantes -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users"></i> Participantes y Resultados
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4">
                            <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Gestión de Participantes</h4>
                            <p class="text-muted">Esta funcionalidad estará disponible en futuras versiones del sistema</p>
                            @auth
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary" disabled>
                                    <i class="fas fa-user-plus"></i> Inscribir Piloto
                                </button>
                                <button type="button" class="btn btn-outline-info" disabled>
                                    <i class="fas fa-list"></i> Ver Participantes
                                </button>
                                <button type="button" class="btn btn-outline-success" disabled>
                                    <i class="fas fa-medal"></i> Registrar Resultados
                                </button>
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            @auth
            <!-- Panel lateral con acciones -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Acciones Rápidas</h3>
                    </div>
                    <div class="card-body">
                        <div class="btn-group-vertical btn-block">
                            <a href="{{ route('admin.matchdays.edit', $matchday) }}" 
                               class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar Jornada
                            </a>
                            <a href="{{ route('admin.championships.show', $matchday->championship) }}" 
                               class="btn btn-info">
                                <i class="fas fa-trophy"></i> Ver Campeonato
                            </a>
                            <a href="{{ route('admin.matchdays.index', ['championship' => $matchday->championship_id]) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-calendar"></i> Todas las Jornadas
                            </a>
                            <hr>
                            <form action="{{ route('admin.matchdays.destroy', $matchday) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('¿Está seguro de eliminar esta jornada?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash"></i> Eliminar Jornada
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @if($matchday->organizer_club_id)
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Club Organizador</h3>
                        </div>
                        <div class="card-body">
                            <h5>{{ $matchday->organizerClub->name }}</h5>
                            @if($matchday->organizerClub->address)
                                <p class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i> {{ $matchday->organizerClub->address }}
                                </p>
                            @endif
                            @if($matchday->organizerClub->phone)
                                <p class="text-muted">
                                    <i class="fas fa-phone"></i> {{ $matchday->organizerClub->phone }}
                                </p>
                            @endif
                            @if($matchday->organizerClub->email)
                                <p class="text-muted">
                                    <i class="fas fa-envelope"></i> {{ $matchday->organizerClub->email }}
                                </p>
                            @endif
                            <a href="{{ route('admin.clubs.show', $matchday->organizerClub) }}" 
                               class="btn btn-outline-info btn-sm">
                                <i class="fas fa-eye"></i> Ver Club
                            </a>
                        </div>
                    </div>
                @endif

                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Información</h3>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <strong>Creada:</strong> {{ $matchday->created_at->format('d/m/Y H:i') }}<br>
                            <strong>Actualizada:</strong> {{ $matchday->updated_at->format('d/m/Y H:i') }}
                        </small>
                        
                        @if($matchday->date)
                            <hr>
                            <div class="text-center">
                                <div class="h4">{{ $matchday->date->format('j') }}</div>
                                <div class="text-muted">{{ $matchday->date->format('M Y') }}</div>
                                <div class="text-muted">{{ $matchday->date->format('l') }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Navegación entre jornadas -->
                @php
                    $previousMatchday = $matchday->championship->matchdays()
                        ->where('number', '<', $matchday->number)
                        ->orderBy('number', 'desc')
                        ->first();
                    $nextMatchday = $matchday->championship->matchdays()
                        ->where('number', '>', $matchday->number)
                        ->orderBy('number', 'asc')
                        ->first();
                @endphp

                @if($previousMatchday || $nextMatchday)
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Navegación</h3>
                        </div>
                        <div class="card-body">
                            <div class="btn-group btn-block">
                                @if($previousMatchday)
                                    <a href="{{ route('admin.matchdays.show', $previousMatchday) }}" 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-chevron-left"></i> Jornada {{ $previousMatchday->number }}
                                    </a>
                                @endif
                                @if($nextMatchday)
                                    <a href="{{ route('admin.matchdays.show', $nextMatchday) }}" 
                                       class="btn btn-outline-secondary">
                                        Jornada {{ $nextMatchday->number }} <i class="fas fa-chevron-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @endauth
        </div>
    </div>
</section>
@endsection
