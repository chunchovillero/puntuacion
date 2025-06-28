@extends('layouts.admin')

@section('title', 'Editar Jornada')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Editar Jornada {{ $matchday->number }}</h1>
                <small class="text-muted">{{ $matchday->championship->name }} ({{ $matchday->championship->year }})</small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.championships.index') }}">Campeonatos</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.championships.show', $matchday->championship) }}">{{ $matchday->championship->name }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.matchdays.index', ['championship' => $matchday->championship_id]) }}">Jornadas</a></li>
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
                        <h3 class="card-title">Editar: Jornada {{ $matchday->number }}</h3>
                    </div>
                    
                    <form action="{{ route('admin.matchdays.update', $matchday) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="championship_id">Campeonato <span class="text-danger">*</span></label>
                                        <select class="form-control @error('championship_id') is-invalid @enderror" 
                                                id="championship_id" 
                                                name="championship_id">
                                            @foreach(\App\Models\Championship::orderBy('year', 'desc')->orderBy('name')->get() as $champ)
                                                <option value="{{ $champ->id }}" 
                                                        {{ old('championship_id', $matchday->championship_id) == $champ->id ? 'selected' : '' }}>
                                                    {{ $champ->name }} ({{ $champ->year }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('championship_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="number">Número de Jornada <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control @error('number') is-invalid @enderror" 
                                               id="number" 
                                               name="number" 
                                               value="{{ old('number', $matchday->number) }}" 
                                               min="1" 
                                               placeholder="Ej: 1, 2, 3...">
                                        @error('number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">Fecha de la Jornada</label>
                                        <input type="date" 
                                               class="form-control @error('date') is-invalid @enderror" 
                                               id="date" 
                                               name="date" 
                                               value="{{ old('date', $matchday->date ? $matchday->date->format('Y-m-d') : '') }}">
                                        @error('date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_time">Hora de Inicio</label>
                                        <input type="time" 
                                               class="form-control @error('start_time') is-invalid @enderror" 
                                               id="start_time" 
                                               name="start_time" 
                                               value="{{ old('start_time', $matchday->start_time) }}">
                                        @error('start_time')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="organizer_type">Organizador</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="organizer_type" id="organizer_ambmx" value="ambmx" 
                                                   {{ old('organizer_type', $matchday->organizer_club_id ? 'club' : 'ambmx') == 'ambmx' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="organizer_ambmx">
                                                AMBMX (Asociación Metropolitana BMX)
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="organizer_type" id="organizer_club" value="club" 
                                                   {{ old('organizer_type', $matchday->organizer_club_id ? 'club' : 'ambmx') == 'club' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="organizer_club">
                                                Club específico
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="club_organizer_row" style="{{ old('organizer_type', $matchday->organizer_club_id ? 'club' : 'ambmx') == 'club' ? '' : 'display: none;' }}">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="organizer_club_id">Club Organizador</label>
                                        <select class="form-control @error('organizer_club_id') is-invalid @enderror" 
                                                id="organizer_club_id" 
                                                name="organizer_club_id">
                                            <option value="">Seleccione un club</option>
                                            @foreach(\App\Models\Club::orderBy('name')->get() as $club)
                                                <option value="{{ $club->id }}" 
                                                        {{ old('organizer_club_id', $matchday->organizer_club_id) == $club->id ? 'selected' : '' }}>
                                                    {{ $club->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('organizer_club_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="venue">Nombre de la Pista</label>
                                <input type="text" 
                                       class="form-control @error('venue') is-invalid @enderror" 
                                       id="venue" 
                                       name="venue" 
                                       value="{{ old('venue', $matchday->venue) }}" 
                                       placeholder="Ej: Pista Municipal de BMX">
                                @error('venue')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="address">Dirección de la Pista</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" 
                                          name="address" 
                                          rows="2" 
                                          placeholder="Dirección completa de la pista...">{{ old('address', $matchday->address) }}</textarea>
                                @error('address')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Estado</label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" 
                                                name="status">
                                            <option value="scheduled" {{ old('status', $matchday->status) == 'scheduled' ? 'selected' : '' }}>Programada</option>
                                            <option value="ongoing" {{ old('status', $matchday->status) == 'ongoing' ? 'selected' : '' }}>En curso</option>
                                            <option value="completed" {{ old('status', $matchday->status) == 'completed' ? 'selected' : '' }}>Completada</option>
                                            <option value="cancelled" {{ old('status', $matchday->status) == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                                            <option value="postponed" {{ old('status', $matchday->status) == 'postponed' ? 'selected' : '' }}>Postergada</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="max_participants">Máximo de Participantes</label>
                                        <input type="number" 
                                               class="form-control @error('max_participants') is-invalid @enderror" 
                                               id="max_participants" 
                                               name="max_participants" 
                                               value="{{ old('max_participants', $matchday->max_participants) }}" 
                                               min="1" 
                                               placeholder="Ej: 100">
                                        @error('max_participants')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notas</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3" 
                                          placeholder="Notas adicionales sobre la jornada...">{{ old('notes', $matchday->notes) }}</textarea>
                                @error('notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Jornada
                            </button>
                            <a href="{{ route('admin.matchdays.show', $matchday) }}" class="btn btn-info ml-2">
                                <i class="fas fa-eye"></i> Ver Detalle
                            </a>
                            <a href="{{ route('admin.championships.show', $matchday->championship) }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left"></i> Volver al Campeonato
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Información de la Jornada</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Creada:</strong> {{ $matchday->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Última actualización:</strong> {{ $matchday->updated_at->format('d/m/Y H:i') }}</p>
                        
                        <hr>
                        
                        <div class="btn-group-vertical btn-block">
                            <a href="{{ route('admin.matchdays.show', $matchday) }}" 
                               class="btn btn-outline-info">
                                <i class="fas fa-eye"></i> Ver Detalle Completo
                            </a>
                            <a href="{{ route('admin.championships.show', $matchday->championship) }}" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-trophy"></i> Ver Campeonato
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const organizerRadios = document.querySelectorAll('input[name="organizer_type"]');
    const clubRow = document.getElementById('club_organizer_row');
    const clubSelect = document.getElementById('organizer_club_id');
    
    organizerRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'club') {
                clubRow.style.display = '';
                clubSelect.required = true;
            } else {
                clubRow.style.display = 'none';
                clubSelect.required = false;
                clubSelect.value = '';
            }
        });
    });
});
</script>
@endsection
