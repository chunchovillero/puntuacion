<template>
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <router-link to="/" class="nav-link">Inicio</router-link>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item" v-if="currentUser">
                    <a class="nav-link" href="/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        Cerrar Sesión
                    </a>
                    <form id="logout-form" action="/logout" method="POST" style="display: none;">
                        <input type="hidden" name="_token" :value="csrfToken">
                    </form>
                </li>
                <li class="nav-item" v-else>
                    <a class="nav-link" href="/login">
                        <i class="fas fa-sign-in-alt mr-1"></i>
                        Iniciar Sesión
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <router-link to="/" class="brand-link">
                <img src="/dist/img/AdminLTELogo.png" alt="BMX Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">BMX Sistema</span>
            </router-link>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex" v-if="currentUser">
                    <div class="image">
                        <img src="/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ currentUser.name }}</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <router-link to="/" class="nav-link" exact-active-class="active">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </router-link>
                        </li>

                        <!-- Gestión -->
                        <li class="nav-header">GESTIÓN</li>
                        
                        <li class="nav-item">
                            <router-link to="/pilotos" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-user-ninja"></i>
                                <p>Pilotos</p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link to="/clubes" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-flag"></i>
                                <p>Clubes</p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link to="/categorias" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Categorías</p>
                            </router-link>
                        </li>

                        <!-- Competiciones -->
                        <li class="nav-header">COMPETICIONES</li>

                        <li class="nav-item">
                            <router-link to="/campeonatos" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-trophy"></i>
                                <p>Campeonatos</p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link to="/jornadas" class="nav-link" active-class="active">
                                <i class="nav-icon fas fa-calendar"></i>
                                <p>Jornadas</p>
                            </router-link>
                        </li>

                        <!-- Administración (solo para usuarios autenticados) -->
                        <template v-if="currentUser">
                            <li class="nav-header">ADMINISTRACIÓN</li>

                            <li class="nav-item">
                                <router-link to="/usuarios" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Usuarios</p>
                                </router-link>
                            </li>

                            <li class="nav-item">
                                <router-link to="/actividad" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-history"></i>
                                    <p>Registro de Actividad</p>
                                </router-link>
                            </li>

                            <li class="nav-item">
                                <router-link to="/configuracion" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-cog"></i>
                                    <p>Configuración</p>
                                </router-link>
                            </li>
                        </template>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <router-view></router-view>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Versión</b> 1.0.0
            </div>
            <strong>Sistema de Puntuación BMX &copy; {{ currentYear }}.</strong>
            Todos los derechos reservados.
        </footer>
    </div>
</template>

<script>
export default {
    name: 'AppLayout',
    computed: {
        currentUser() {
            return window.Laravel?.user && window.Laravel.user.authenticated ? window.Laravel.user : null;
        },
        csrfToken() {
            return window.Laravel?.csrfToken || '';
        },
        currentYear() {
            return new Date().getFullYear();
        }
    }
};
</script>

<style scoped>
/* Estilos específicos para el layout pueden ir aquí */
</style>
