@extends('layouts.admin')

@section('title', 'Detalles de Categoría')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ $category->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categorías</a></li>
                    <li class="breadcrumb-item active">{{ $category->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Información de la categoría -->
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <i class="fas fa-tags fa-4x text-primary mb-3"></i>
                        </div>

                        <h3 class="profile-username text-center">{{ $category->name }}</h3>

                        <p class="text-muted text-center">
                            <span class="badge badge-{{ $category->type == 'novicios' ? 'info' : ($category->type == 'escuela' ? 'primary' : 'success') }} badge-lg">
                                {{ ucfirst($category->type) }}
                            </span>
                            @if($category->gender)
                                <span class="badge badge-{{ $category->gender == 'varones' ? 'primary' : 'pink' }} badge-lg ml-1">
                                    {{ ucfirst($category->gender) }}
                                </span>
                            @endif
                        </p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Pilotos Asignados</b> 
                                <span class="float-right">
                                    <span class="badge badge-info">{{ $category->pilots->count() }}</span>
                                </span>
                            </li>
                            <li class="list-group-item">
                                <b>Estado</b> 
                                <span class="float-right">
                                    <span class="badge badge-{{ $category->active ? 'success' : 'secondary' }}">
                                        {{ $category->active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </span>
                            </li>
                        </ul>

                        <div class="text-center">
                            @auth
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            @endauth
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información Adicional</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Rango de Edad:</strong></td>
                                        <td>
                                            @if($category->age_min && $category->age_max)
                                                {{ $category->age_min }}-{{ $category->age_max }} años
                                            @elseif($category->age_min)
                                                {{ $category->age_min }}+ años
                                            @elseif($category->age_max)
                                                hasta {{ $category->age_max }} años
                                            @else
                                                Sin límite de edad
                                            @endif
                                        </td>
                                    </tr>
                                    @if($category->description)
                                    <tr>
                                        <td><strong>Descripción:</strong></td>
                                        <td>{{ $category->description }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Creada:</strong></td>
                                        <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Actualizada:</strong></td>
                                        <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de pilotos en esta categoría -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pilotos en esta Categoría ({{ $category->pilots->count() }})</h3>
                    </div>
                    <div class="card-body">
                        @if($category->pilots->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Foto</th>
                                            <th>Nombre</th>
                                            <th>Club</th>
                                            <th>Edad</th>
                                            <th>Puntos</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($category->pilots as $pilot)
                                            <tr>
                                                <td class="text-center">
                                                    @if($pilot->photo_path)
                                                        <img src="{{ asset('storage/' . $pilot->photo_path) }}" 
                                                             alt="{{ $pilot->full_name }}" 
                                                             class="img-circle" 
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary img-circle d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 40px;">
                                                            <i class="fas fa-user text-white"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $pilot->full_name }}</strong>
                                                    @if($pilot->nickname)
                                                        <br><small class="text-muted">"{{ $pilot->nickname }}"</small>
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
                                                    <strong>{{ number_format($pilot->ranking_points ?? 0) }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge badge-{{ $pilot->status == 'active' ? 'success' : 'secondary' }}">
                                                        {{ $pilot->status == 'active' ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.pilots.show', $pilot) }}" 
                                                           class="btn btn-info btn-sm" 
                                                           title="Ver detalles">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.pilots.edit', $pilot) }}" 
                                                           class="btn btn-warning btn-sm" 
                                                           title="Editar">
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
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <h4>No hay pilotos en esta categoría</h4>
                                <p>Aún no se han asignado pilotos a esta categoría.</p>
                                <a href="{{ route('admin.pilots.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Agregar Primer Piloto
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
