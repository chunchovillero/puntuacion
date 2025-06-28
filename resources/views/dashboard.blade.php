@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h4>¡Bienvenido, {{ Auth::user()->name }}!</h4>
                    <p>Has iniciado sesión correctamente.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Panel de Usuario</h5>
                                    <p class="card-text">Accede a tu panel de usuario personalizado.</p>
                                    <a href="#" class="btn btn-primary">Mi Perfil</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Panel de Administración</h5>
                                    <p class="card-text">Accede al panel de administración avanzado con AdminLTE.</p>
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-success">Ir a Admin</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
