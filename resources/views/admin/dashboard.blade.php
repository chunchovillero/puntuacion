@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Panel de Control')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Welcome message -->
<div class="row">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    @auth
                        Bienvenido, {{ Auth::user()->name }}
                    @else
                        Bienvenido al Sistema de Gestión BMX
                    @endauth
                </h3>
            </div>
            <div class="card-body">
                @auth
                    <p>Tienes acceso completo al panel de administración. Puedes crear, editar y eliminar registros.</p>
                @else
                    <p>Estás viendo el sistema en modo público. Puedes ver toda la información pero no realizar cambios.</p>
                    <p><a href="{{ route('login') }}" class="btn btn-primary">Iniciar Sesión</a> para acceder al panel de administración.</p>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Info boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Usuarios Totales</span>
                <span class="info-box-number">
                    {{ \App\Models\User::count() ?? 0 }}
                </span>
            </div>
        </div>
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Clubes BMX</span>
                <span class="info-box-number">{{ \App\Models\Club::count() ?? 0 }}</span>
            </div>
        </div>
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix hidden-md-up"></div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-motorcycle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pilotos Activos</span>
                <span class="info-box-number">{{ \App\Models\Pilot::where('status', 'active')->count() ?? 0 }}</span>
            </div>
        </div>
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-trophy"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Campeonatos</span>
                <span class="info-box-number">{{ \App\Models\Championship::count() ?? 0 }}</span>
            </div>
        </div>
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-calendar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Jornadas</span>
                <span class="info-box-number">{{ \App\Models\Matchday::count() ?? 0 }}</span>
            </div>
        </div>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ \App\Models\Pilot::count() ?? 0 }}</h3>
                <p>Total Pilotos</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-ninja"></i>
            </div>
            <a href="{{ route('admin.pilots.index') }}" class="small-box-footer">Ver pilotos <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ \App\Models\Club::count() ?? 0 }}</h3>
                <p>Clubes Registrados</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('admin.clubs.index') }}" class="small-box-footer">Ver clubes <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ \App\Models\Championship::where('status', 'active')->count() ?? 0 }}</h3>
                <p>Campeonatos Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-trophy"></i>
            </div>
            <a href="{{ route('admin.championships.index') }}" class="small-box-footer">Ver campeonatos <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ \App\Models\Matchday::where('status', 'scheduled')->count() ?? 0 }}</h3>
                <p>Jornadas Programadas</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <a href="{{ route('admin.matchdays.index') }}" class="small-box-footer">Ver jornadas <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-7 connectedSortable">
        <!-- Últimos campeonatos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy mr-1"></i>
                    Últimos Campeonatos
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.championships.index') }}" class="btn btn-tool">
                        <i class="fas fa-eye"></i> Ver todos
                    </a>
                </div>
            </div>
            <div class="card-body">
                @php
                    $recentChampionships = \App\Models\Championship::orderBy('created_at', 'desc')->limit(5)->get();
                @endphp
                @if($recentChampionships->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($recentChampionships as $championship)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $championship->name }}</strong>
                                    <br><small class="text-muted">{{ $championship->year }}</small>
                                </div>
                                <div>
                                    @switch($championship->status)
                                        @case('planned')
                                            <span class="badge badge-secondary">Planeado</span>
                                            @break
                                        @case('active')
                                            <span class="badge badge-success">Activo</span>
                                            @break
                                        @case('completed')
                                            <span class="badge badge-primary">Completado</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge badge-danger">Cancelado</span>
                                            @break
                                    @endswitch
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-center text-muted">No hay campeonatos registrados</p>
                @endif
            </div>
        </div>

        <!-- Próximas jornadas -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar mr-1"></i>
                    Próximas Jornadas
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.matchdays.index') }}" class="btn btn-tool">
                        <i class="fas fa-eye"></i> Ver todas
                    </a>
                </div>
            </div>
            <div class="card-body">
                @php
                    $upcomingMatchdays = \App\Models\Matchday::where('date', '>=', now())
                        ->whereIn('status', ['scheduled', 'ongoing'])
                        ->orderBy('date', 'asc')
                        ->limit(5)
                        ->with('championship', 'organizerClub')
                        ->get();
                @endphp
                @if($upcomingMatchdays->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($upcomingMatchdays as $matchday)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Jornada {{ $matchday->number }}</strong> - {{ $matchday->championship->name }}
                                        <br><small class="text-muted">
                                            {{ $matchday->date ? $matchday->date->format('d/m/Y') : 'Fecha por definir' }}
                                            @if($matchday->start_time)
                                                - {{ $matchday->start_time }}
                                            @endif
                                        </small>
                                    </div>
                                    <div>
                                        @if($matchday->organizer_club_id)
                                            <span class="badge badge-info">{{ $matchday->organizerClub->name }}</span>
                                        @else
                                            <span class="badge badge-success">AMBMX</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-center text-muted">No hay jornadas programadas</p>
                @endif
            </div>
        </div>
    </section>
    <!-- /.Left col -->

    <!-- Right col (fixed) -->
    <section class="col-lg-5 connectedSortable">
        <!-- Pilotos por categoría -->
        <div class="card bg-gradient-primary">
            <div class="card-header border-0">
                <h3 class="card-title">
                    <i class="fas fa-tags mr-1"></i>
                    Pilotos por Categoría
                </h3>
            </div>
            <div class="card-body">
                @php
                    $categoriesStats = \App\Models\Category::withCount('pilots')->get();
                @endphp
                @if($categoriesStats->count() > 0)
                    @foreach($categoriesStats as $category)
                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                            <p class="text-light">
                                <i class="fas fa-tag mr-1"></i>
                                {{ $category->name }}
                            </p>
                            <p class="text-light">
                                <span class="badge badge-light">{{ $category->pilots_count }}</span>
                            </p>
                        </div>
                    @endforeach
                @else
                    <p class="text-light">No hay categorías configuradas</p>
                @endif
            </div>
        </div>

        <!-- Clubes más activos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Clubes más Activos</h3>
            </div>
            <div class="card-body">
                @php
                    $activeClubs = \App\Models\Club::withCount('pilots')->orderBy('pilots_count', 'desc')->limit(5)->get();
                @endphp
                @if($activeClubs->count() > 0)
                    @foreach($activeClubs as $club)
                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                            <p>
                                <i class="fas fa-users mr-1"></i>
                                {{ $club->name }}
                            </p>
                            <p>
                                <span class="badge badge-primary">{{ $club->pilots_count }} pilotos</span>
                            </p>
                        </div>
                    @endforeach
                @else
                    <p class="text-center text-muted">No hay datos de clubes</p>
                @endif
            </div>
        </div>
    </section>
    <!-- /.Right col -->
</div>
<!-- /.row (main row) -->
            <div class="inner">
                <h3 id="conversionRate">53<sup style="font-size: 20px">%</sup></h3>
                <p>Tasa de Conversión</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3 id="usersCount">{{ \App\Models\User::count() ?? 1 }}</h3>
                <p>Usuarios Registrados</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3 id="uniqueVisitors">{{ \App\Models\Pilot::count() ?? 0 }}</h3>
                <p>Total Pilotos</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-7 connectedSortable">
        <!-- Custom tabs (Charts with tabs)-->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Análisis de Ventas
                </h3>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="#revenue-chart" data-toggle="tab">Área</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#sales-chart" data-toggle="tab">Donut</a>
                        </li>
                    </ul>
                </div>
            </div><!-- /.card-header -->
            <div class="card-body">
                <div class="tab-content p-0">
                    <!-- Morris chart - Sales -->
                    <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
                        <canvas id="revenue-chart-canvas" height="300" style="height: 300px;"></canvas>
                    </div>
                    <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
                        <canvas id="sales-chart-canvas" height="300" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div><!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- Progress bars -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks mr-1"></i>
                    Progreso del Proyecto
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-center">
                            <strong>Desarrollo del Sistema</strong>
                        </p>
                        <div class="progress-group">
                            Backend API
                            <span class="float-right"><b>160</b>/200</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: 80%"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            Frontend UI/UX
                            <span class="float-right"><b>310</b>/400</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-danger" style="width: 77.5%"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            <span class="progress-text">Base de Datos</span>
                            <span class="float-right"><b>480</b>/800</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: 60%"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            Testing & QA
                            <span class="float-right"><b>250</b>/500</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" style="width: 50%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p class="text-center">
                            <strong>Métricas de Rendimiento</strong>
                        </p>
                        <div class="progress-group">
                            Tiempo de Carga
                            <span class="float-right"><b>90</b>%</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-info" style="width: 90%"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            Optimización SEO
                            <span class="float-right"><b>75</b>%</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: 75%"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            Seguridad
                            <span class="float-right"><b>95</b>%</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: 95%"></div>
                            </div>
                        </div>
                        <div class="progress-group">
                            Accesibilidad
                            <span class="float-right"><b>85</b>%</span>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->

        <!-- Calendar -->
        <div class="card bg-gradient-success">
            <div class="card-header border-0">
                <h3 class="card-title">
                    <i class="far fa-calendar-alt"></i>
                    Calendario de Eventos
                </h3>
                <!-- tools card -->
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body pt-0">
                <!--The calendar -->
                <div id="calendar" style="width: 100%"></div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </section>
    <!-- /.Left col -->

    <!-- right col (We are only adding the ID to make the widgets sortable)-->
    <section class="col-lg-5 connectedSortable">

        <!-- Información del Sistema -->
        <div class="card bg-gradient-info">
            <div class="card-header border-0">
                <h3 class="card-title">
                    <i class="fas fa-server mr-1"></i>
                    Información del Sistema
                </h3>
                <!-- card tools -->
                <div class="card-tools">
                    <button type="button" class="btn btn-info btn-sm" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <!-- /.card-tools -->
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless text-white">
                        <tbody>
                            <tr>
                                <td><i class="fab fa-laravel mr-2"></i><strong>Laravel:</strong></td>
                                <td>{{ app()->version() }}</td>
                            </tr>
                            <tr>
                                <td><i class="fab fa-php mr-2"></i><strong>PHP:</strong></td>
                                <td>{{ PHP_VERSION }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-cogs mr-2"></i><strong>Entorno:</strong></td>
                                <td><span class="badge badge-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">{{ strtoupper(app()->environment()) }}</span></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-user mr-2"></i><strong>Usuario:</strong></td>
                                <td>{{ Auth::user()->name ?? 'Invitado' }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-clock mr-2"></i><strong>Fecha:</strong></td>
                                <td>{{ now()->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-globe mr-2"></i><strong>Zona Horaria:</strong></td>
                                <td>{{ config('app.timezone') }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-memory mr-2"></i><strong>Memoria:</strong></td>
                                <td>{{ round(memory_get_usage(true) / 1024 / 1024, 2) }} MB</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.card-body-->
        </div>
        <!-- /.card -->

        <!-- Notificaciones recientes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bell mr-1"></i>
                    Notificaciones Recientes
                </h3>
                <div class="card-tools">
                    <span class="badge badge-warning">3</span>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-inbox text-info"></i> Nuevo mensaje
                            <span class="float-right text-muted text-sm">3 mins</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-users text-warning"></i> 5 nuevos usuarios
                            <span class="float-right text-muted text-sm">12 hours</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-file text-success"></i> 3 nuevos reportes
                            <span class="float-right text-muted text-sm">2 days</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /.card -->

        <!-- Actividad del sistema -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line mr-1"></i>
                    Actividad del Sistema
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 17%</span>
                            <h5 class="description-header">$35,210.43</h5>
                            <span class="description-text">INGRESOS TOTALES</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i> 0%</span>
                            <h5 class="description-header">$10,390.90</h5>
                            <span class="description-text">COSTOS TOTALES</span>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
                <div class="progress-group">
                    CPU Usage
                    <span class="float-right"><b>23</b>%</span>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-info" style="width: 23%"></div>
                    </div>
                </div>
                <div class="progress-group">
                    Memory Usage
                    <span class="float-right"><b>67</b>%</span>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-warning" style="width: 67%"></div>
                    </div>
                </div>
                <div class="progress-group">
                    Disk Space
                    <span class="float-right"><b>45</b>%</span>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" style="width: 45%"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->

        <!-- Lista de tareas actualizada -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ion ion-clipboard mr-1"></i>
                    Lista de Tareas
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTaskModal">
                        <i class="fas fa-plus"></i> Nueva Tarea
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <ul class="todo-list" data-widget="todo-list">
                    <li>
                        <span class="handle">
                            <i class="fas fa-ellipsis-v"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </span>
                        <div class="icheck-primary d-inline ml-2">
                            <input type="checkbox" value="" name="todo1" id="todoCheck1">
                            <label for="todoCheck1"></label>
                        </div>
                        <span class="text">Implementar autenticación con 2FA</span>
                        <small class="badge badge-danger"><i class="far fa-clock"></i> Alta prioridad</small>
                        <div class="tools">
                            <i class="fas fa-edit"></i>
                            <i class="fas fa-trash-o"></i>
                        </div>
                    </li>
                    <li>
                        <span class="handle">
                            <i class="fas fa-ellipsis-v"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </span>
                        <div class="icheck-primary d-inline ml-2">
                            <input type="checkbox" value="" name="todo2" id="todoCheck2" checked>
                            <label for="todoCheck2"></label>
                        </div>
                        <span class="text">Optimizar consultas de base de datos</span>
                        <small class="badge badge-success"><i class="far fa-clock"></i> Completado</small>
                        <div class="tools">
                            <i class="fas fa-edit"></i>
                            <i class="fas fa-trash-o"></i>
                        </div>
                    </li>
                    <li>
                        <span class="handle">
                            <i class="fas fa-ellipsis-v"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </span>
                        <div class="icheck-primary d-inline ml-2">
                            <input type="checkbox" value="" name="todo3" id="todoCheck3">
                            <label for="todoCheck3"></label>
                        </div>
                        <span class="text">Crear API de puntuación</span>
                        <small class="badge badge-warning"><i class="far fa-clock"></i> En progreso</small>
                        <div class="tools">
                            <i class="fas fa-edit"></i>
                            <i class="fas fa-trash-o"></i>
                        </div>
                    </li>
                    <li>
                        <span class="handle">
                            <i class="fas fa-ellipsis-v"></i>
                            <i class="fas fa-ellipsis-v"></i>
                        </span>
                        <div class="icheck-primary d-inline ml-2">
                            <input type="checkbox" value="" name="todo4" id="todoCheck4">
                            <label for="todoCheck4"></label>
                        </div>
                        <span class="text">Añadir tests unitarios</span>
                        <small class="badge badge-info"><i class="far fa-clock"></i> Planificado</small>
                        <div class="tools">
                            <i class="fas fa-edit"></i>
                            <i class="fas fa-trash-o"></i>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addTaskModal">
                    <i class="fas fa-plus"></i> Agregar elemento
                </button>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.right col -->
</div>
<!-- /.row (main row) -->

<!-- Modal para agregar tareas -->
<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Agregar Nueva Tarea</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addTaskForm">
                    <div class="form-group">
                        <label for="taskTitle">Título de la Tarea</label>
                        <input type="text" class="form-control" id="taskTitle" placeholder="Ingrese el título de la tarea" required>
                    </div>
                    <div class="form-group">
                        <label for="taskPriority">Prioridad</label>
                        <select class="form-control" id="taskPriority">
                            <option value="info">Baja</option>
                            <option value="warning">Media</option>
                            <option value="danger">Alta</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="taskDescription">Descripción</label>
                        <textarea class="form-control" id="taskDescription" rows="3" placeholder="Descripción opcional"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveTask">Guardar Tarea</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast notification -->
<div class="toast" id="taskToast" role="alert" aria-live="assertive" aria-atomic="true" style="position: absolute; top: 20px; right: 20px; z-index: 1050;">
    <div class="toast-header">
        <i class="fas fa-check-circle text-success mr-2"></i>
        <strong class="mr-auto">Notificación</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="toast-body">
        Tarea agregada correctamente.
    </div>
</div>
@endsection

@push('styles')
<!-- ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css">
<!-- FullCalendar -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css">
<style>
    .stats-card {
        transition: transform 0.2s;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .weather-widget {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .todo-list .tools {
        opacity: 0;
        transition: opacity 0.3s;
    }
    .todo-list li:hover .tools {
        opacity: 1;
    }
    .real-time-data {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
</style>
@endpush

@push('scripts')
<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<!-- Moment -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<!-- FullCalendar -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar estadísticas en tiempo real
    function updateStats() {
        // Simular datos dinámicos
        const orders = Math.floor(Math.random() * 50) + 150;
        const conversion = Math.floor(Math.random() * 20) + 45;
        const visitors = Math.floor(Math.random() * 30) + 50;
        
        document.getElementById('ordersCount').textContent = orders;
        document.getElementById('conversionRate').innerHTML = conversion + '<sup style="font-size: 20px">%</sup>';
        document.getElementById('uniqueVisitors').textContent = visitors;
    }

    // Actualizar estadísticas cada 30 segundos
    setInterval(updateStats, 30000);

    // Datos de ejemplo para los gráficos
    
    // Gráfico de área
    var areaChartCanvas = document.getElementById('revenue-chart-canvas').getContext('2d');
    var areaChartData = {
        labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'],
        datasets: [
            {
                label: 'Ingresos',
                backgroundColor: 'rgba(60,141,188,0.3)',
                borderColor: 'rgba(60,141,188,1)',
                pointRadius: 4,
                pointColor: '#3b8bba',
                pointStrokeColor: 'rgba(60,141,188,1)',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data: [28000, 48000, 40000, 19000, 86000, 27000, 90000],
                fill: true,
                tension: 0.4
            },
            {
                label: 'Gastos',
                backgroundColor: 'rgba(210, 214, 222, 0.3)',
                borderColor: 'rgba(210, 214, 222, 1)',
                pointRadius: 4,
                pointColor: 'rgba(210, 214, 222, 1)',
                pointStrokeColor: '#c1c7d1',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(220,220,220,1)',
                data: [18000, 35000, 25000, 15000, 50000, 20000, 65000],
                fill: true,
                tension: 0.4
            }
        ]
    };

    var areaChart = new Chart(areaChartCanvas, {
        type: 'line',
        data: areaChartData,
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Meses'
                    },
                    grid: {
                        display: false,
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Valor ($)'
                    },
                    grid: {
                        color: 'rgba(200, 200, 200, 0.3)',
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Gráfico Donut
    var donutChartCanvas = document.getElementById('sales-chart-canvas').getContext('2d');
    var donutData = {
        labels: [
            'Ventas Online',
            'Ventas en Tienda',
            'Ventas por Teléfono',
            'Ventas Corporativas',
            'Otros'
        ],
        datasets: [
            {
                data: [700, 500, 400, 600, 300],
                backgroundColor: [
                    '#f56954',
                    '#00a65a', 
                    '#f39c12',
                    '#00c0ef',
                    '#3c8dbc'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }
        ]
    };

    var donutChart = new Chart(donutChartCanvas, {
        type: 'doughnut',
        data: donutData,
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Inicializar FullCalendar
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: [
                {
                    title: 'Reunión de equipo',
                    start: '2024-01-15',
                    backgroundColor: '#f39c12',
                    borderColor: '#f39c12'
                },
                {
                    title: 'Lanzamiento de producto',
                    start: '2024-01-20',
                    backgroundColor: '#00a65a',
                    borderColor: '#00a65a'
                },
                {
                    title: 'Capacitación',
                    start: '2024-01-25',
                    end: '2024-01-26',
                    backgroundColor: '#3c8dbc',
                    borderColor: '#3c8dbc'
                }
            ],
            height: 300
        });
        calendar.render();
    }

    // Funcionalidad para agregar tareas
    document.getElementById('saveTask').addEventListener('click', function() {
        const title = document.getElementById('taskTitle').value;
        const priority = document.getElementById('taskPriority').value;
        const description = document.getElementById('taskDescription').value;
        
        if (title.trim() === '') {
            alert('Por favor, ingrese un título para la tarea.');
            return;
        }
        
        // Crear nueva tarea en la lista
        const todoList = document.querySelector('.todo-list');
        const newTaskId = 'todoCheck' + Date.now();
        const priorityText = {
            'info': 'Baja',
            'warning': 'Media', 
            'danger': 'Alta'
        };
        
        const newTask = document.createElement('li');
        newTask.innerHTML = `
            <span class="handle">
                <i class="fas fa-ellipsis-v"></i>
                <i class="fas fa-ellipsis-v"></i>
            </span>
            <div class="icheck-primary d-inline ml-2">
                <input type="checkbox" value="" name="${newTaskId}" id="${newTaskId}">
                <label for="${newTaskId}"></label>
            </div>
            <span class="text">${title}</span>
            <small class="badge badge-${priority}"><i class="far fa-clock"></i> ${priorityText[priority]} prioridad</small>
            <div class="tools">
                <i class="fas fa-edit"></i>
                <i class="fas fa-trash-o"></i>
            </div>
        `;
        
        todoList.appendChild(newTask);
        
        // Limpiar formulario y cerrar modal
        document.getElementById('addTaskForm').reset();
        $('#addTaskModal').modal('hide');
        
        // Mostrar notificación
        $('#taskToast').toast({ delay: 3000 }).toast('show');
    });

    // Funcionalidad para eliminar tareas
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('fa-trash-o')) {
            const taskItem = e.target.closest('li');
            if (confirm('¿Está seguro de que desea eliminar esta tarea?')) {
                taskItem.remove();
            }
        }
    });

    // Hacer las tarjetas arrastrablesli
    $('.connectedSortable').sortable({
        placeholder: 'sort-highlight',
        connectWith: '.connectedSortable',
        handle: '.card-header, .nav-tabs',
        forcePlaceholderSize: true,
        zIndex: 999999
    });

    // Animación para las tarjetas estadísticas
    $('.small-box, .info-box').hover(
        function() {
            $(this).addClass('stats-card');
        },
        function() {
            $(this).removeClass('stats-card');
        }
    );

    // Actualizar fecha y hora en tiempo real
    function updateDateTime() {
        const now = new Date();
        const options = { 
            year: 'numeric', 
            month: '2-digit', 
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        const dateTimeString = now.toLocaleDateString('es-ES', options);
        
        // Buscar la celda de fecha en la tabla del sistema
        const dateCell = document.querySelector('td:contains("Fecha:")');
        if (dateCell && dateCell.nextElementSibling) {
            dateCell.nextElementSibling.textContent = dateTimeString;
        }
    }

    // Actualizar cada segundo
    setInterval(updateDateTime, 1000);
});
</script>
@endpush
