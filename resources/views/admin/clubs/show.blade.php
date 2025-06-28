@extends('layouts.admin')

@section('title', 'Detalle del Club')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $club->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.clubs.index') }}">Clubes</a></li>
                    <li class="breadcrumb-item active">{{ $club->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Información del Club -->
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            @if($club->logo)
                                <img class="profile-user-img img-fluid"
                                     src="{{ asset('storage/' . $club->logo) }}"
                                     alt="{{ $club->name }}"
                                     style="width: 128px; height: 128px; object-fit: contain;">
                            @else
                                <div class="profile-user-img img-fluid bg-secondary d-inline-flex align-items-center justify-content-center"
                                     style="width: 128px; height: 128px;">
                                    <i class="fas fa-users fa-3x text-white"></i>
                                </div>
                            @endif
                        </div>

                        <h3 class="profile-username text-center">{{ $club->name }}</h3>

                        @if($club->city)
                            <p class="text-muted text-center">{{ $club->city }}{{ $club->state ? ', ' . $club->state : '' }}</p>
                        @endif

                        <p class="text-muted text-center">
                            <span class="badge badge-{{ $club->status == 'active' ? 'success' : ($club->status == 'suspended' ? 'warning' : 'secondary') }} badge-lg">
                                {{ $club->status == 'active' ? 'Activo' : ($club->status == 'suspended' ? 'Suspendido' : 'Inactivo') }}
                            </span>
                        </p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Pilotos Registrados</b> 
                                <a class="float-right">{{ $club->pilots->count() }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Pilotos Activos</b> 
                                <a class="float-right">{{ $club->pilots->where('status', 'active')->count() }}</a>
                            </li>
                            @if($club->founded_date)
                                <li class="list-group-item">
                                    <b>Fundado</b> 
                                    <a class="float-right">{{ $club->founded_date->format('Y') }}</a>
                                </li>
                            @endif
                        </ul>

                        <div class="row">
                            @auth
                            <div class="col-6">
                                <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-warning btn-block">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                            @else
                            <div class="col-12">
                                <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Enlaces de redes sociales -->
                @if($club->website || $club->facebook || $club->instagram || $club->twitter)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Enlaces</h3>
                        </div>
                        <div class="card-body">
                            @if($club->website)
                                <a href="{{ $club->website }}" target="_blank" class="btn btn-info btn-sm btn-block mb-2">
                                    <i class="fas fa-globe"></i> Sitio Web
                                </a>
                            @endif
                            @if($club->facebook)
                                <a href="{{ $club->facebook }}" target="_blank" class="btn btn-primary btn-sm btn-block mb-2">
                                    <i class="fab fa-facebook"></i> Facebook
                                </a>
                            @endif
                            @if($club->instagram)
                                <a href="{{ $club->instagram }}" target="_blank" class="btn btn-danger btn-sm btn-block mb-2">
                                    <i class="fab fa-instagram"></i> Instagram
                                </a>
                            @endif
                            @if($club->twitter)
                                <a href="{{ $club->twitter }}" target="_blank" class="btn btn-info btn-sm btn-block">
                                    <i class="fab fa-twitter"></i> Twitter
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Información detallada -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#info" data-toggle="tab">
                                    <i class="fas fa-info-circle"></i> Información
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#pilots" data-toggle="tab">
                                    <i class="fas fa-users"></i> Pilotos ({{ $club->pilots->count() }})
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Información del Club -->
                            <div class="active tab-pane" id="info">
                                @if($club->description)
                                    <div class="mb-4">
                                        <h5>Descripción</h5>
                                        <p>{{ $club->description }}</p>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Información General</h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Nombre:</strong></td>
                                                <td>{{ $club->name }}</td>
                                            </tr>
                                            @if($club->founded_date)
                                            <tr>
                                                <td><strong>Fecha de Fundación:</strong></td>
                                                <td>{{ $club->founded_date->format('d/m/Y') }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td><strong>Estado:</strong></td>
                                                <td>
                                                    <span class="badge badge-{{ $club->status == 'active' ? 'success' : ($club->status == 'suspended' ? 'warning' : 'secondary') }}">
                                                        {{ $club->status == 'active' ? 'Activo' : ($club->status == 'suspended' ? 'Suspendido' : 'Inactivo') }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Registrado:</strong></td>
                                                <td>{{ $club->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            @if($club->updated_at != $club->created_at)
                                            <tr>
                                                <td><strong>Última actualización:</strong></td>
                                                <td>{{ $club->updated_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Información de Contacto</h5>
                                        <table class="table table-borderless">
                                            @if($club->address)
                                            <tr>
                                                <td><strong>Dirección:</strong></td>
                                                <td>{{ $club->address }}</td>
                                            </tr>
                                            @endif
                                            @if($club->city)
                                            <tr>
                                                <td><strong>Ciudad:</strong></td>
                                                <td>{{ $club->city }}</td>
                                            </tr>
                                            @endif
                                            @if($club->state)
                                            <tr>
                                                <td><strong>Estado/Provincia:</strong></td>
                                                <td>{{ $club->state }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td><strong>País:</strong></td>
                                                <td>{{ $club->country }}</td>
                                            </tr>
                                            @if($club->postal_code)
                                            <tr>
                                                <td><strong>Código Postal:</strong></td>
                                                <td>{{ $club->postal_code }}</td>
                                            </tr>
                                            @endif
                                            @if($club->phone)
                                            <tr>
                                                <td><strong>Teléfono:</strong></td>
                                                <td>
                                                    <a href="tel:{{ $club->phone }}">{{ $club->phone }}</a>
                                                </td>
                                            </tr>
                                            @endif
                                            @if($club->email)
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>
                                                    <a href="mailto:{{ $club->email }}">{{ $club->email }}</a>
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Lista de Pilotos -->
                            <div class="tab-pane" id="pilots">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Pilotos del Club</h5>
                                    <div>
                                        <a href="{{ route('admin.pilots.create') }}?club_id={{ $club->id }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Agregar Piloto
                                        </a>
                                        <a href="{{ route('admin.pilots.by-club', $club) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-list"></i> Ver Todos
                                        </a>
                                    </div>
                                </div>

                                @if($club->pilots->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Foto</th>
                                                    <th>Nombre</th>
                                                    <th>Edad</th>
                                                    <th>Nivel</th>
                                                    <th>Puntos</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($club->pilots->take(10) as $pilot)
                                                    <tr>
                                                        <td class="text-center">
                                                            @if($pilot->photo_path)
                                                                <img src="{{ asset('storage/' . $pilot->photo_path) }}" 
                                                                     alt="{{ $pilot->full_name }}" 
                                                                     class="img-circle" 
                                                                     style="width: 30px; height: 30px; object-fit: cover;">
                                                            @else
                                                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                                     style="width: 30px; height: 30px;">
                                                                    <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <strong>{{ $pilot->full_name }}</strong>
                                                            @if($pilot->nickname)
                                                                <br><small class="text-muted">"{{ $pilot->nickname }}"</small>
                                                            @endif
                                                        </td>
                                                        <td>{{ $pilot->age }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $pilot->experience_level == 'profesional' ? 'danger' : ($pilot->experience_level == 'avanzado' ? 'warning' : ($pilot->experience_level == 'intermedio' ? 'info' : 'secondary')) }} badge-sm">
                                                                {{ ucfirst($pilot->experience_level) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ number_format($pilot->ranking_points ?? 0) }}</td>
                                                        <td>
                                                            <span class="badge badge-{{ $pilot->status == 'active' ? 'success' : 'secondary' }} badge-sm">
                                                                {{ $pilot->status == 'active' ? 'Activo' : 'Inactivo' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('admin.pilots.show', $pilot) }}" 
                                                                   class="btn btn-info btn-xs" 
                                                                   title="Ver detalles">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                @auth
                                                                <a href="{{ route('admin.pilots.edit', $pilot) }}" 
                                                                   class="btn btn-warning btn-xs" 
                                                                   title="Editar">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                @endauth
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    @if($club->pilots->count() > 10)
                                        <div class="text-center">
                                            <a href="{{ route('admin.pilots.by-club', $club) }}" class="btn btn-outline-primary">
                                                Ver todos los pilotos ({{ $club->pilots->count() }})
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        Este club aún no tiene pilotos registrados.
                                        <a href="{{ route('admin.pilots.create') }}?club_id={{ $club->id }}" class="alert-link">
                                            Agregar el primer piloto
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
