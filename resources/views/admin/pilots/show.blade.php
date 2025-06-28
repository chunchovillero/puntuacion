@extends('layouts.admin')

@section('title', 'Detalle del Piloto')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $pilot->full_name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.pilots.index') }}">Pilotos</a></li>
                    <li class="breadcrumb-item active">{{ $pilot->full_name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Información del Piloto -->
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            @if($pilot->photo_path)
                                <img class="profile-user-img img-fluid img-circle"
                                     src="{{ asset('storage/' . $pilot->photo_path) }}"
                                     alt="{{ $pilot->full_name }}"
                                     style="width: 128px; height: 128px; object-fit: cover;">
                            @else
                                <div class="profile-user-img img-fluid img-circle bg-secondary d-inline-flex align-items-center justify-content-center"
                                     style="width: 128px; height: 128px;">
                                    <i class="fas fa-user fa-3x text-white"></i>
                                </div>
                            @endif
                        </div>

                        <h3 class="profile-username text-center">{{ $pilot->full_name }}</h3>

                        @if($pilot->nickname)
                            <p class="text-muted text-center">
                                <span class="badge badge-info badge-lg">"{{ $pilot->nickname }}"</span>
                            </p>
                        @endif

                        <p class="text-muted text-center">
                            @if($pilot->category)
                                <span class="badge badge-{{ $pilot->category->type == 'novicios' ? 'info' : ($pilot->category->type == 'escuela' ? 'primary' : 'success') }} badge-lg">
                                    {{ $pilot->category->name }}
                                    @if($pilot->category->age_min && $pilot->category->age_max)
                                        ({{ $pilot->category->age_min }}-{{ $pilot->category->age_max }} años)
                                    @elseif($pilot->category->age_min)
                                        ({{ $pilot->category->age_min }}+ años)
                                    @endif
                                </span>
                            @else
                                <span class="badge badge-secondary badge-lg">Sin categoría</span>
                            @endif
                        </p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Puntos de Ranking</b> 
                                <a class="float-right">{{ number_format($pilot->ranking_points ?? 0) }}</a>
                            </li>
                            <li class="list-group-item">
                                <b>Edad</b> 
                                <a class="float-right">{{ $pilot->age }} años</a>
                            </li>
                            <li class="list-group-item">
                                <b>Estado</b> 
                                <span class="float-right">
                                    <span class="badge badge-{{ $pilot->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ $pilot->status == 'active' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </span>
                            </li>
                        </ul>

                        <div class="row">
                            @auth
                            <div class="col-6">
                                <a href="{{ route('admin.pilots.edit', $pilot) }}" class="btn btn-warning btn-block">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.pilots.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                            @else
                            <div class="col-12">
                                <a href="{{ route('admin.pilots.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>
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
                                <a class="nav-link" href="#club" data-toggle="tab">
                                    <i class="fas fa-users"></i> Club
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Información Personal -->
                            <div class="active tab-pane" id="info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Información Personal</h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Nombre Completo:</strong></td>
                                                <td>{{ $pilot->full_name }}</td>
                                            </tr>
                                            @if($pilot->nickname)
                                            <tr>
                                                <td><strong>Apodo:</strong></td>
                                                <td>{{ $pilot->nickname }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td><strong>Edad:</strong></td>
                                                <td>{{ $pilot->age }} años</td>
                                            </tr>
                                            @if($pilot->birth_date)
                                            <tr>
                                                <td><strong>Fecha de Nacimiento:</strong></td>
                                                <td>{{ $pilot->birth_date->format('d/m/Y') }}</td>
                                            </tr>
                                            @endif
                                            @if($pilot->phone)
                                            <tr>
                                                <td><strong>Teléfono:</strong></td>
                                                <td>
                                                    <a href="tel:{{ $pilot->phone }}">{{ $pilot->phone }}</a>
                                                </td>
                                            </tr>
                                            @endif
                                            @if($pilot->email)
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>
                                                    <a href="mailto:{{ $pilot->email }}">{{ $pilot->email }}</a>
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Información BMX</h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Categoría:</strong></td>
                                                <td>
                                                    @if($pilot->category)
                                                        <span class="badge badge-{{ $pilot->category->type == 'novicios' ? 'info' : ($pilot->category->type == 'escuela' ? 'primary' : 'success') }}">
                                                            {{ $pilot->category->name }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary">Sin categoría</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Puntos de Ranking:</strong></td>
                                                <td>{{ number_format($pilot->ranking_points ?? 0) }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Estado:</strong></td>
                                                <td>
                                                    <span class="badge badge-{{ $pilot->status == 'active' ? 'success' : 'secondary' }}">
                                                        {{ $pilot->status == 'active' ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Registrado:</strong></td>
                                                <td>{{ $pilot->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            @if($pilot->updated_at != $pilot->created_at)
                                            <tr>
                                                <td><strong>Última actualización:</strong></td>
                                                <td>{{ $pilot->updated_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del Club -->
                            <div class="tab-pane" id="club">
                                @if($pilot->club)
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            @if($pilot->club->logo)
                                                <img src="{{ asset('storage/' . $pilot->club->logo) }}" 
                                                     alt="{{ $pilot->club->name }}" 
                                                     class="img-fluid"
                                                     style="max-width: 120px; max-height: 120px; object-fit: contain;">
                                            @else
                                                <div class="bg-secondary rounded d-inline-flex align-items-center justify-content-center" 
                                                     style="width: 120px; height: 120px;">
                                                    <i class="fas fa-users fa-3x text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-9">
                                            <h4>{{ $pilot->club->name }}</h4>
                                            @if($pilot->club->description)
                                                <p class="text-muted">{{ $pilot->club->description }}</p>
                                            @endif
                                            
                                            <table class="table table-borderless">
                                                @if($pilot->club->city)
                                                <tr>
                                                    <td><strong>Ciudad:</strong></td>
                                                    <td>{{ $pilot->club->city }}</td>
                                                </tr>
                                                @endif
                                                @if($pilot->club->address)
                                                <tr>
                                                    <td><strong>Dirección:</strong></td>
                                                    <td>{{ $pilot->club->address }}</td>
                                                </tr>
                                                @endif
                                                @if($pilot->club->phone)
                                                <tr>
                                                    <td><strong>Teléfono:</strong></td>
                                                    <td>
                                                        <a href="tel:{{ $pilot->club->phone }}">{{ $pilot->club->phone }}</a>
                                                    </td>
                                                </tr>
                                                @endif
                                                @if($pilot->club->email)
                                                <tr>
                                                    <td><strong>Email:</strong></td>
                                                    <td>
                                                        <a href="mailto:{{ $pilot->club->email }}">{{ $pilot->club->email }}</a>
                                                    </td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td><strong>Estado:</strong></td>
                                                    <td>
                                                        <span class="badge badge-{{ $pilot->club->status == 'active' ? 'success' : 'secondary' }}">
                                                            {{ $pilot->club->status == 'active' ? 'Activo' : 'Inactivo' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div class="mt-3">
                                                <a href="{{ route('admin.clubs.show', $pilot->club) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Ver Club Completo
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Este piloto no está asignado a ningún club.
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
