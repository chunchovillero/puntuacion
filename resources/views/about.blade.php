@extends('layouts.app')

@section('title', 'Acerca de - Mi Aplicación')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <h1 class="mb-4">Acerca de Esta Aplicación</h1>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">¿Qué es esta aplicación?</h5>
                <p class="card-text">
                    Esta es una aplicación web desarrollada completamente con <strong>Laravel</strong> y <strong>Blade</strong> como motor de plantillas. 
                    No utiliza frameworks de JavaScript frontend como Vue.js, React o Angular, manteniendo una arquitectura simple y efectiva.
                </p>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Tecnologías Utilizadas</h5>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> PHP {{ PHP_VERSION }}</li>
                            <li><i class="fas fa-check text-success"></i> Laravel {{ app()->version() }}</li>
                            <li><i class="fas fa-check text-success"></i> Blade Templates</li>
                            <li><i class="fas fa-check text-success"></i> Bootstrap 5</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> HTML5</li>
                            <li><i class="fas fa-check text-success"></i> CSS3</li>
                            <li><i class="fas fa-check text-success"></i> JavaScript Vanilla</li>
                            <li><i class="fas fa-check text-success"></i> Font Awesome</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Ventajas de usar solo Blade</h5>
                <ul>
                    <li><strong>Simplicidad:</strong> Menos complejidad en el frontend</li>
                    <li><strong>Rendimiento:</strong> Carga más rápida sin frameworks pesados de JavaScript</li>
                    <li><strong>SEO Friendly:</strong> Renderizado del lado del servidor</li>
                    <li><strong>Mantenimiento:</strong> Menor dependencia de librerías externas</li>
                    <li><strong>Aprendizaje:</strong> Curva de aprendizaje más suave</li>
                </ul>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Estructura del Proyecto</h5>
                <p>La aplicación sigue la estructura estándar de Laravel:</p>
                <div class="bg-light p-3 rounded">
                    <code>
                        app/ - Lógica de la aplicación<br>
                        resources/views/ - Plantillas Blade<br>
                        routes/ - Definición de rutas<br>
                        public/ - Archivos públicos (CSS, JS, imágenes)<br>
                        database/ - Migraciones y seeders<br>
                        config/ - Archivos de configuración
                    </code>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Volver al Inicio
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
