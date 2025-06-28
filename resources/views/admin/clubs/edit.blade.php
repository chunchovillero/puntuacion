@extends('layouts.admin')

@section('title', 'Editar Club')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Editar Club: {{ $club->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.clubs.index') }}">Clubes</a></li>
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
                <h3 class="card-title">Información del Club</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a href="{{ route('admin.clubs.show', $club) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> Ver
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.clubs.update', $club) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <!-- Información básica -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Información Básica</h5>
                            
                            <div class="form-group">
                                <label for="name">Nombre del Club *</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $club->name) }}" 
                                       required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Descripción del club">{{ old('description', $club->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="logo">Logo del Club</label>
                                @if($club->logo)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $club->logo) }}" 
                                             alt="{{ $club->name }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 150px; max-height: 150px; object-fit: contain;">
                                        <div class="mt-1">
                                            <small class="text-muted">Logo actual</small>
                                        </div>
                                    </div>
                                @endif
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" 
                                               class="custom-file-input @error('logo') is-invalid @enderror" 
                                               id="logo" 
                                               name="logo" 
                                               accept="image/*">
                                        <label class="custom-file-label" for="logo">{{ $club->logo ? 'Cambiar logo' : 'Seleccionar logo' }}</label>
                                    </div>
                                </div>
                                @error('logo')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Archivos permitidos: JPG, JPEG, PNG. Máximo 2MB.</small>
                            </div>

                            <div class="form-group">
                                <label for="founded_date">Fecha de Fundación</label>
                                <input type="date" 
                                       class="form-control @error('founded_date') is-invalid @enderror" 
                                       id="founded_date" 
                                       name="founded_date" 
                                       value="{{ old('founded_date', $club->founded_date) }}">
                                @error('founded_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status">Estado *</label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="active" {{ old('status', $club->status) == 'active' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactive" {{ old('status', $club->status) == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="suspended" {{ old('status', $club->status) == 'suspended' ? 'selected' : '' }}>Suspendido</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Información de contacto -->
                        <div class="col-md-6">
                            <h5 class="mb-3">Información de Contacto</h5>
                            
                            <div class="form-group">
                                <label for="address">Dirección</label>
                                <input type="text" 
                                       class="form-control @error('address') is-invalid @enderror" 
                                       id="address" 
                                       name="address" 
                                       value="{{ old('address', $club->address) }}">
                                @error('address')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="city">Ciudad</label>
                                <input type="text" 
                                       class="form-control @error('city') is-invalid @enderror" 
                                       id="city" 
                                       name="city" 
                                       value="{{ old('city', $club->city) }}">
                                @error('city')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="state">Estado/Provincia</label>
                                <input type="text" 
                                       class="form-control @error('state') is-invalid @enderror" 
                                       id="state" 
                                       name="state" 
                                       value="{{ old('state', $club->state) }}">
                                @error('state')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="country">País *</label>
                                <input type="text" 
                                       class="form-control @error('country') is-invalid @enderror" 
                                       id="country" 
                                       name="country" 
                                       value="{{ old('country', $club->country) }}" 
                                       required>
                                @error('country')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="postal_code">Código Postal</label>
                                <input type="text" 
                                       class="form-control @error('postal_code') is-invalid @enderror" 
                                       id="postal_code" 
                                       name="postal_code" 
                                       value="{{ old('postal_code', $club->postal_code) }}">
                                @error('postal_code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone">Teléfono</label>
                                <input type="text" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $club->phone) }}">
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
                                       value="{{ old('email', $club->email) }}">
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="website">Sitio Web</label>
                                <input type="url" 
                                       class="form-control @error('website') is-invalid @enderror" 
                                       id="website" 
                                       name="website" 
                                       value="{{ old('website', $club->website) }}" 
                                       placeholder="https://ejemplo.com">
                                @error('website')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <h6 class="mt-4 mb-2">Redes Sociales</h6>
                            
                            <div class="form-group">
                                <label for="facebook">Facebook</label>
                                <input type="url" 
                                       class="form-control @error('facebook') is-invalid @enderror" 
                                       id="facebook" 
                                       name="facebook" 
                                       value="{{ old('facebook', $club->facebook) }}" 
                                       placeholder="https://facebook.com/club">
                                @error('facebook')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="instagram">Instagram</label>
                                <input type="url" 
                                       class="form-control @error('instagram') is-invalid @enderror" 
                                       id="instagram" 
                                       name="instagram" 
                                       value="{{ old('instagram', $club->instagram) }}" 
                                       placeholder="https://instagram.com/club">
                                @error('instagram')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="twitter">Twitter</label>
                                <input type="url" 
                                       class="form-control @error('twitter') is-invalid @enderror" 
                                       id="twitter" 
                                       name="twitter" 
                                       value="{{ old('twitter', $club->twitter) }}" 
                                       placeholder="https://twitter.com/club">
                                @error('twitter')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar Club
                    </button>
                    <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">
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
});
</script>
@endsection
