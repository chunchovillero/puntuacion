@extends('layouts.admin')

@section('title', 'Editar Campeonato')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Editar Campeonato</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.championships.index') }}">Campeonatos</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Editar: {{ $championship->name }}</h3>
                    </div>
                    
                    <form action="{{ route('admin.championships.update', $championship) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="name">Nombre del Campeonato <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name', $championship->name) }}" 
                                               placeholder="Ej: Campeonato Metropolitano BMX">
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="year">Año <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control @error('year') is-invalid @enderror" 
                                               id="year" 
                                               name="year" 
                                               value="{{ old('year', $championship->year) }}" 
                                               min="2020" 
                                               max="2030">
                                        @error('year')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Descripción del campeonato...">{{ old('description', $championship->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Estado</label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" 
                                                name="status">
                                            <option value="planned" {{ old('status', $championship->status) == 'planned' ? 'selected' : '' }}>Planeado</option>
                                            <option value="active" {{ old('status', $championship->status) == 'active' ? 'selected' : '' }}>Activo</option>
                                            <option value="completed" {{ old('status', $championship->status) == 'completed' ? 'selected' : '' }}>Completado</option>
                                            <option value="cancelled" {{ old('status', $championship->status) == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="start_date">Fecha de Inicio</label>
                                        <input type="date" 
                                               class="form-control @error('start_date') is-invalid @enderror" 
                                               id="start_date" 
                                               name="start_date" 
                                               value="{{ old('start_date', $championship->start_date ? $championship->start_date->format('Y-m-d') : '') }}">
                                        @error('start_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="end_date">Fecha de Fin</label>
                                        <input type="date" 
                                               class="form-control @error('end_date') is-invalid @enderror" 
                                               id="end_date" 
                                               name="end_date" 
                                               value="{{ old('end_date', $championship->end_date ? $championship->end_date->format('Y-m-d') : '') }}">
                                        @error('end_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="rules">Reglamento</label>
                                <textarea class="form-control @error('rules') is-invalid @enderror" 
                                          id="rules" 
                                          name="rules" 
                                          rows="4" 
                                          placeholder="Reglas y normativas del campeonato...">{{ old('rules', $championship->rules) }}</textarea>
                                @error('rules')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="prizes">Premios</label>
                                <textarea class="form-control @error('prizes') is-invalid @enderror" 
                                          id="prizes" 
                                          name="prizes" 
                                          rows="3" 
                                          placeholder="Descripción de premios y reconocimientos...">{{ old('prizes', $championship->prizes) }}</textarea>
                                @error('prizes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Campeonato
                            </button>
                            <a href="{{ route('admin.championships.show', $championship) }}" class="btn btn-info ml-2">
                                <i class="fas fa-eye"></i> Ver Detalle
                            </a>
                            <a href="{{ route('admin.championships.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Información del Campeonato</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Creado:</strong> {{ $championship->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Última actualización:</strong> {{ $championship->updated_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Jornadas registradas:</strong> {{ $championship->matchdays()->count() }}</p>
                        
                        <hr>
                        
                        <div class="btn-group-vertical btn-block">
                            <a href="{{ route('admin.matchdays.index', ['championship' => $championship->id]) }}" 
                               class="btn btn-outline-info">
                                <i class="fas fa-calendar"></i> Gestionar Jornadas
                            </a>
                            <a href="{{ route('admin.championships.show', $championship) }}" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-eye"></i> Ver Detalle Completo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
