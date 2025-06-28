@extends('layouts.admin')

@section('title', 'Crear Campeonato')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Crear Campeonato</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.championships.index') }}">Campeonatos</a></li>
                    <li class="breadcrumb-item active">Crear</li>
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
                        <h3 class="card-title">Información del Campeonato</h3>
                    </div>
                    
                    <form action="{{ route('admin.championships.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="name">Nombre del Campeonato <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name') }}" 
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
                                               value="{{ old('year', date('Y')) }}" 
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
                                          placeholder="Descripción del campeonato...">{{ old('description') }}</textarea>
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
                                            <option value="planned" {{ old('status') == 'planned' ? 'selected' : '' }}>Planeado</option>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Activo</option>
                                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completado</option>
                                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
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
                                               value="{{ old('start_date') }}">
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
                                               value="{{ old('end_date') }}">
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
                                          placeholder="Reglas y normativas del campeonato...">{{ old('rules') }}</textarea>
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
                                          placeholder="Descripción de premios y reconocimientos...">{{ old('prizes') }}</textarea>
                                @error('prizes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Crear Campeonato
                            </button>
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
                        <h3 class="card-title">Información</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Estados del Campeonato:</strong></p>
                        <ul class="list-unstyled">
                            <li><span class="badge badge-secondary">Planeado</span> - En preparación</li>
                            <li><span class="badge badge-success">Activo</span> - En desarrollo</li>
                            <li><span class="badge badge-primary">Completado</span> - Finalizado</li>
                            <li><span class="badge badge-danger">Cancelado</span> - Suspendido</li>
                        </ul>
                        
                        <hr>
                        
                        <p><strong>Nota:</strong> Una vez creado el campeonato, podrá agregar las jornadas correspondientes desde la vista de detalle.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
