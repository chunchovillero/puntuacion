@extends('layouts.admin')

@section('title', 'Crear Usuario')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Crear Usuario</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
                    <li class="breadcrumb-item active">Crear</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus mr-2"></i>
                            Nuevo Usuario del Sistema
                        </h3>
                    </div>

                    <form action="{{ route('admin.usuarios.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Nombre completo <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Ingresa el nombre completo" 
                                       required>
                                @error('name')
                                    <span class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Correo electrónico <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="usuario@ejemplo.com" 
                                       required>
                                @error('email')
                                    <span class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Mínimo 8 caracteres" 
                                       required>
                                @error('password')
                                    <span class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirmar contraseña <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Repite la contraseña" 
                                       required>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Información:</strong> El usuario será creado con el email verificado automáticamente.
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Crear Usuario
                            </button>
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-question-circle mr-2"></i>
                            Ayuda
                        </h3>
                    </div>
                    <div class="card-body">
                        <h5><i class="fas fa-key text-primary mr-2"></i>Requisitos de contraseña:</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-2"></i>Mínimo 8 caracteres</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Se recomienda usar letras, números y símbolos</li>
                        </ul>

                        <hr>

                        <h5><i class="fas fa-shield-alt text-warning mr-2"></i>Permisos:</h5>
                        <p class="text-muted">
                            Los usuarios creados tendrán acceso completo al sistema de administración BMX.
                        </p>

                        <hr>

                        <h5><i class="fas fa-envelope text-info mr-2"></i>Verificación:</h5>
                        <p class="text-muted">
                            El email se marcará como verificado automáticamente al crear el usuario.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
