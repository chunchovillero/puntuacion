<template>
    <div class="home-page">
        <!-- Header de bienvenida -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Sistema BMX</h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <section class="content">
            <div class="container-fluid">
                <!-- Estadísticas rápidas -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ stats.pilots || 0 }}</h3>
                                <p>Pilotos Registrados</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-ninja"></i>
                            </div>
                            <router-link to="/pilotos" class="small-box-footer">
                                Ver más <i class="fas fa-arrow-circle-right"></i>
                            </router-link>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ stats.clubs || 0 }}</h3>
                                <p>Clubes</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-flag"></i>
                            </div>
                            <router-link to="/clubes" class="small-box-footer">
                                Ver más <i class="fas fa-arrow-circle-right"></i>
                            </router-link>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ stats.championships || 0 }}</h3>
                                <p>Campeonatos</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <router-link to="/campeonatos" class="small-box-footer">
                                Ver más <i class="fas fa-arrow-circle-right"></i>
                            </router-link>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ stats.matchdays || 0 }}</h3>
                                <p>Jornadas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <router-link to="/jornadas" class="small-box-footer">
                                Ver más <i class="fas fa-arrow-circle-right"></i>
                            </router-link>
                        </div>
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-tachometer-alt mr-1"></i>
                                    Acciones Rápidas
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <router-link to="/pilotos" class="btn btn-app">
                                            <i class="fas fa-user-ninja"></i> Gestionar Pilotos
                                        </router-link>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <router-link to="/clubes" class="btn btn-app">
                                            <i class="fas fa-flag"></i> Gestionar Clubes
                                        </router-link>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <router-link to="/campeonatos" class="btn btn-app">
                                            <i class="fas fa-trophy"></i> Campeonatos
                                        </router-link>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <router-link to="/jornadas" class="btn btn-app">
                                            <i class="fas fa-calendar"></i> Jornadas
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Información del Sistema
                                </h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Sistema de Puntuación BMX</strong></p>
                                <p>Gestiona pilotos, clubes, campeonatos y jornadas de manera eficiente.</p>
                                <hr>
                                <div v-if="currentUser">
                                    <p><strong>Usuario:</strong> {{ currentUser.name }}</p>
                                    <p><strong>Email:</strong> {{ currentUser.email }}</p>
                                </div>
                                <div v-else>
                                    <p>Visitando como invitado</p>
                                    <a href="/login" class="btn btn-primary btn-sm">
                                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    name: 'HomePage',
    data() {
        return {
            stats: {
                pilots: 0,
                clubs: 0,
                championships: 0,
                matchdays: 0
            },
            loading: false
        };
    },
    computed: {
        currentUser() {
            return window.Laravel?.user || null;
        }
    },
    mounted() {
        this.loadStats();
    },
    methods: {
        async loadStats() {
            this.loading = true;
            try {
                // Cargar estadísticas básicas
                const promises = [
                    this.loadPilotsCount(),
                    this.loadClubsCount(),
                    this.loadChampionshipsCount(),
                    this.loadMatchdaysCount()
                ];

                await Promise.allSettled(promises);
            } catch (error) {
                console.error('Error loading stats:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadPilotsCount() {
            try {
                const response = await fetch('/api/pilots?count=true');
                if (response.ok) {
                    const data = await response.json();
                    this.stats.pilots = data.total || data.length || 0;
                }
            } catch (error) {
                console.error('Error loading pilots count:', error);
            }
        },

        async loadClubsCount() {
            try {
                const response = await fetch('/api/clubs');
                if (response.ok) {
                    const data = await response.json();
                    this.stats.clubs = Array.isArray(data) ? data.length : (data.data ? data.data.length : 0);
                }
            } catch (error) {
                console.error('Error loading clubs count:', error);
            }
        },

        async loadChampionshipsCount() {
            try {
                const response = await fetch('/api/championships');
                if (response.ok) {
                    const data = await response.json();
                    this.stats.championships = Array.isArray(data) ? data.length : (data.data ? data.data.length : 0);
                }
            } catch (error) {
                console.error('Error loading championships count:', error);
            }
        },

        async loadMatchdaysCount() {
            try {
                const response = await fetch('/api/matchdays');
                if (response.ok) {
                    const data = await response.json();
                    this.stats.matchdays = Array.isArray(data) ? data.length : (data.data ? data.data.length : 0);
                }
            } catch (error) {
                console.error('Error loading matchdays count:', error);
            }
        }
    }
};
</script>

<style scoped>
.home-page {
    padding: 0;
}

.content-header {
    padding: 15px 0;
}

.small-box {
    border-radius: 10px;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.btn-app {
    border-radius: 8px;
    text-align: center;
    padding: 15px 10px;
    width: 100%;
    height: auto;
}

.card {
    border: none;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}
</style>
