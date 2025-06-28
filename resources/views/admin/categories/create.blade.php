@extends('layouts.admin')

@section('title', 'Nueva Categoría')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Nueva Categoría</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categorías</a></li>
                    <li class="breadcrumb-item active">Nueva</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información de la Categoría</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <!-- Información básica -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Información Básica</h5>
                            
                            <div class="form-group">
                                <label for="name">Nombre de la Categoría *</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required
                                       placeholder="Ej: Escuela Varones">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="type">Tipo de Categoría *</label>
                                <select class="form-control @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type" 
                                        required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="escuela" {{ old('type') == 'escuela' ? 'selected' : '' }}>Escuela</option>
                                    <option value="novicios" {{ old('type') == 'novicios' ? 'selected' : '' }}>Novicios</option>
                                    <option value="juvenil" {{ old('type') == 'juvenil' ? 'selected' : '' }}>Juvenil</option>
                                    <option value="adulto" {{ old('type') == 'adulto' ? 'selected' : '' }}>Adulto</option>
                                    <option value="veteranos" {{ old('type') == 'veteranos' ? 'selected' : '' }}>Veteranos</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="gender">Género</label>
                                <select class="form-control @error('gender') is-invalid @enderror" 
                                        id="gender" 
                                        name="gender">
                                    <option value="">Mixto (sin restricción)</option>
                                    <option value="varones" {{ old('gender') == 'varones' ? 'selected' : '' }}>Varones</option>
                                    <option value="mujeres" {{ old('gender') == 'mujeres' ? 'selected' : '' }}>Mujeres</option>
                                </select>
                                @error('gender')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Deja en blanco para categorías mixtas</small>
                            </div>

                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3"
                                          placeholder="Descripción opcional de la categoría">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Configuración de edad -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Rango de Edad</h5>
                            
                            <div class="form-group">
                                <label for="age_min">Edad Mínima</label>
                                <input type="number" 
                                       class="form-control @error('age_min') is-invalid @enderror" 
                                       id="age_min" 
                                       name="age_min" 
                                       value="{{ old('age_min') }}" 
                                       min="1" 
                                       max="100"
                                       placeholder="Ej: 6">
                                @error('age_min')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Deja en blanco si no hay límite mínimo</small>
                            </div>

                            <div class="form-group">
                                <label for="age_max">Edad Máxima</label>
                                <input type="number" 
                                       class="form-control @error('age_max') is-invalid @enderror" 
                                       id="age_max" 
                                       name="age_max" 
                                       value="{{ old('age_max') }}" 
                                       min="1" 
                                       max="100"
                                       placeholder="Ej: 12">
                                @error('age_max')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Deja en blanco si no hay límite máximo</small>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="active" 
                                           name="active" 
                                           value="1" 
                                           {{ old('active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">
                                        Categoría activa
                                    </label>
                                </div>
                                <small class="form-text text-muted">Solo las categorías activas aparecerán en los formularios</small>
                            </div>

                            <!-- Ejemplos -->
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Ejemplos de Categorías</h3>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Escuela Varones:</strong> Tipo "escuela", Género "varones", Edad 6-12</li>
                                        <li><strong>Novicios:</strong> Tipo "novicios", Sin género, Edad 6-12</li>
                                        <li><strong>Juvenil Mujeres:</strong> Tipo "juvenil", Género "mujeres", Edad 13-16</li>
                                        <li><strong>Adulto:</strong> Tipo "adulto", Sin género, Edad 17+</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Crear Categoría
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Validar que age_max sea mayor que age_min
    $('#age_min, #age_max').on('change', function() {
        let ageMin = parseInt($('#age_min').val()) || 0;
        let ageMax = parseInt($('#age_max').val()) || 999;
        
        if (ageMin > 0 && ageMax > 0 && ageMin > ageMax) {
            alert('La edad máxima debe ser mayor o igual a la edad mínima.');
            $(this).focus();
        }
    });
});
</script>
@endsection
