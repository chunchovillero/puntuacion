@extends('layouts.admin')

@section('title', 'Editar Piloto')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Editar Piloto: {{ $pilot->full_name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.pilots.index') }}">Pilotos</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información del Piloto</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.pilots.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="{{ route('admin.pilots.show', $pilot) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> Ver
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.pilots.update', $pilot) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <!-- Información personal -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Información Personal</h5>
                            
                            <div class="form-group">
                                <label for="first_name">Nombre *</label>
                                <input type="text" 
                                       class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" 
                                       name="first_name" 
                                       value="{{ old('first_name', $pilot->first_name) }}" 
                                       required>
                                @error('first_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="last_name">Apellido *</label>
                                <input type="text" 
                                       class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" 
                                       name="last_name" 
                                       value="{{ old('last_name', $pilot->last_name) }}" 
                                       required>
                                @error('last_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="nickname">Apodo</label>
                                <input type="text" 
                                       class="form-control @error('nickname') is-invalid @enderror" 
                                       id="nickname" 
                                       name="nickname" 
                                       value="{{ old('nickname', $pilot->nickname) }}" 
                                       placeholder="Apodo o nombre artístico">
                                @error('nickname')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="age">Edad *</label>
                                <input type="number" 
                                       class="form-control @error('age') is-invalid @enderror" 
                                       id="age" 
                                       name="age" 
                                       value="{{ old('age', $pilot->age) }}" 
                                       min="5" 
                                       max="100" 
                                       required>
                                @error('age')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="birth_date">Fecha de Nacimiento</label>
                                <input type="date" 
                                       class="form-control @error('birth_date') is-invalid @enderror" 
                                       id="birth_date" 
                                       name="birth_date" 
                                       value="{{ old('birth_date', $pilot->birth_date ? $pilot->birth_date->format('Y-m-d') : '') }}">
                                @error('birth_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">La edad se calculará automáticamente si se cambia la fecha</small>
                            </div>

                            <div class="form-group">
                                <label for="phone">Teléfono</label>
                                <input type="text" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $pilot->phone) }}">
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $pilot->email) }}">
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Información BMX -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Información BMX</h5>
                            
                            <div class="form-group">
                                <label for="club_id">Club *</label>
                                <select class="form-control @error('club_id') is-invalid @enderror" 
                                        id="club_id" 
                                        name="club_id" 
                                        required>
                                    <option value="">Seleccionar club</option>
                                    @foreach($clubs as $club)
                                        <option value="{{ $club->id }}" {{ old('club_id', $pilot->club_id) == $club->id ? 'selected' : '' }}>
                                            {{ $club->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('club_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="category_id">Categoría *</label>
                                <select class="form-control @error('category_id') is-invalid @enderror" 
                                        id="category_id" 
                                        name="category_id" 
                                        required>
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $pilot->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                            @if($category->age_min && $category->age_max)
                                                ({{ $category->age_min }}-{{ $category->age_max }} años)
                                            @elseif($category->age_min)
                                                ({{ $category->age_min }}+ años)
                                            @elseif($category->age_max)
                                                (hasta {{ $category->age_max }} años)
                                            @endif
                                            @if($category->gender)
                                                - {{ ucfirst($category->gender) }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="ranking_points">Puntos de Ranking</label>
                                <input type="number" 
                                       class="form-control @error('ranking_points') is-invalid @enderror" 
                                       id="ranking_points" 
                                       name="ranking_points" 
                                       value="{{ old('ranking_points', $pilot->ranking_points ?? 0) }}" 
                                       min="0"
                                       placeholder="0">
                                @error('ranking_points')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Puntos acumulados en competencias</small>
                            </div>

                            <div class="form-group">
                                <label for="status">Estado *</label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="active" {{ old('status', $pilot->status) == 'active' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactive" {{ old('status', $pilot->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="photo">Foto del Piloto</label>
                                @if($pilot->photo_path)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $pilot->photo_path) }}" 
                                             alt="{{ $pilot->full_name }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                        <div class="mt-1">
                                            <small class="text-muted">Foto actual</small>
                                        </div>
                                    </div>
                                @endif
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" 
                                               class="custom-file-input @error('photo') is-invalid @enderror" 
                                               id="photo" 
                                               name="photo" 
                                               accept="image/*">
                                        <label class="custom-file-label" for="photo">{{ $pilot->photo_path ? 'Cambiar foto' : 'Seleccionar foto' }}</label>
                                    </div>
                                </div>
                                @error('photo')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Archivos permitidos: JPG, JPEG, PNG. Máximo 2MB.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar Piloto
                    </button>
                    <a href="{{ route('admin.pilots.index') }}" class="btn btn-secondary">
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
    // Actualizar el label del archivo seleccionado
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    // Sincronizar edad y fecha de nacimiento
    $('#birth_date').on('change', function() {
        let birthDate = new Date($(this).val());
        if (birthDate && !isNaN(birthDate)) {
            let today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            let monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            if (age >= 5 && age <= 100) {
                $('#age').val(age);
            }
        }
    });

    $('#age').on('change', function() {
        let age = parseInt($(this).val());
        if (age >= 5 && age <= 100) {
            // Calcular fecha de nacimiento aproximada (1 de enero del año correspondiente)
            let currentYear = new Date().getFullYear();
            let birthYear = currentYear - age;
            let birthDate = birthYear + '-01-01';
            $('#birth_date').val(birthDate);
        }
    });
});
</script>
@endsection
