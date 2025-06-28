@extends('layouts.admin')

@section('title', 'Detalles del Usuario')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Detalles del Usuario</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <div class="profile-user-img img-fluid img-circle bg-primary d-inline-flex align-items-center justify-content-center" 
                                 style="width: 128px; height: 128px; font-size: 48px; color: white;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </div>

                        <h3 class="profile-username text-center">{{ $user->name }}</h3>

                        <p class="text-muted text-center">{{ $user->email }}</p>

                        <div class="text-center">
                            @if($user->email_verified_at)
                                <span class="badge badge-success badge-lg">
                                    <i class="fas fa-check mr-1"></i>Email Verificado
                                </span>
                            @else
                                <span class="badge badge-warning badge-lg">
                                    <i class="fas fa-clock mr-1"></i>Email Pendiente
                                </span>
                            @endif

                            @if($user->id === auth()->id())
                                <br><br>
                                <span class="badge badge-info badge-lg">
                                    <i class="fas fa-user mr-1"></i>Cuenta Actual
                                </span>
                            @endif
                        </div>

                        <hr>

                        <strong><i class="fas fa-calendar mr-1"></i> Fecha de Registro</strong>
                        <p class="text-muted">{{ $user->created_at->format('d/m/Y H:i') }}</p>

                        <strong><i class="fas fa-clock mr-1"></i> Última Actualización</strong>
                        <p class="text-muted">{{ $user->updated_at->format('d/m/Y H:i') }}</p>

                        @if($user->email_verified_at)
                            <strong><i class="fas fa-envelope-check mr-1"></i> Email Verificado</strong>
                            <p class="text-muted">{{ $user->email_verified_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#activity" data-toggle="tab">
                                    <i class="fas fa-list mr-1"></i>Información General
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#settings" data-toggle="tab">
                                    <i class="fas fa-cog mr-1"></i>Configuración
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="activity">
                                <div class="row">
                                    <div class="col-12">
                                        <h5><i class="fas fa-user-circle mr-2"></i>Datos del Usuario</h5>
                                        <table class="table table-striped">
                                            <tr>
                                                <td><strong>ID de Usuario:</strong></td>
                                                <td>{{ $user->id }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nombre Completo:</strong></td>
                                                <td>{{ $user->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Correo Electrónico:</strong></td>
                                                <td>{{ $user->email }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Estado del Email:</strong></td>
                                                <td>
                                                    @if($user->email_verified_at)
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check mr-1"></i>Verificado el {{ $user->email_verified_at->format('d/m/Y') }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-clock mr-1"></i>Pendiente de verificación
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Fecha de Registro:</strong></td>
                                                <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Última Actualización:</strong></td>
                                                <td>{{ $user->updated_at->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="settings">
                                <div class="row">
                                    <div class="col-12">
                                        <h5><i class="fas fa-tools mr-2"></i>Acciones Disponibles</h5>
                                        <p class="text-muted">Acciones que puedes realizar con este usuario:</p>
                                        
                                        <div class="list-group">
                                            <a href="{{ route('admin.usuarios.edit', $user) }}" class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">
                                                        <i class="fas fa-edit text-warning mr-2"></i>Editar Usuario
                                                    </h6>
                                                </div>
                                                <p class="mb-1">Modificar nombre, email o contraseña del usuario.</p>
                                            </a>

                                            @if($user->id !== auth()->id())
                                                <div class="list-group-item list-group-item-action list-group-item-danger">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1">
                                                            <i class="fas fa-trash text-danger mr-2"></i>Eliminar Usuario
                                                        </h6>
                                                    </div>
                                                    <p class="mb-1">Eliminar permanentemente este usuario del sistema.</p>
                                                    <form action="{{ route('admin.usuarios.destroy', $user) }}" method="POST" class="mt-2" 
                                                          onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash mr-1"></i> Confirmar Eliminación
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <div class="list-group-item list-group-item-light">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1">
                                                            <i class="fas fa-ban text-muted mr-2"></i>Eliminar Usuario
                                                        </h6>
                                                    </div>
                                                    <p class="mb-1 text-muted">No puedes eliminar tu propia cuenta.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-footer">
                        <a href="{{ route('admin.usuarios.edit', $user) }}" class="btn btn-warning">
                            <i class="fas fa-edit mr-1"></i> Editar Usuario
                        </a>
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left mr-1"></i> Volver a la Lista
                        </a>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('admin.usuarios.destroy', $user) }}" method="POST" style="display: inline-block;" class="ml-2"
                                  onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash mr-1"></i> Eliminar Usuario
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
