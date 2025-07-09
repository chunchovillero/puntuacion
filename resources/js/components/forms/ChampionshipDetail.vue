<template>
    <div class="championship-detail">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy mr-2"></i>
                    Detalles del Campeonato
                </h3>
                <div class="card-tools">
                    <router-link :to="{ name: 'championships' }" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Campeonatos
                    </router-link>
                    <router-link 
                        v-if="canEdit" 
                        :to="{ name: 'championships.edit', params: { id: championshipId } }" 
                        class="btn btn-primary btn-sm ml-2"
                    >
                        <i class="fas fa-edit mr-1"></i>
                        Editar
                    </router-link>
                </div>
            </div>

            <div v-if="loading" class="card-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3">Cargando información del campeonato...</p>
            </div>

            <div v-else-if="championship" class="card-body">
                <div class="row">
                    <!-- Información principal -->
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h4 class="mb-0">{{ championship.name }}</h4>
                            <span :class="getStatusClass(championship.status)">
                                {{ getStatusLabel(championship.status) }}
                            </span>
                        </div>
                        
                        <div v-if="championship.description" class="mb-4">
                            <h6 class="text-muted mb-2">Descripción</h6>
                            <p class="text-justify">{{ championship.description }}</p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Información General</h6>
                                <p class="mb-1">
                                    <i class="fas fa-calendar text-primary mr-2"></i>
                                    <strong>Año:</strong> {{ championship.year }}
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-leaf text-primary mr-2"></i>
                                    <strong>Temporada:</strong> {{ championship.season || 'No especificada' }}
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-globe text-primary mr-2"></i>
                                    <strong>Tipo:</strong> {{ getTypeLabel(championship.type) }}
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-eye text-primary mr-2"></i>
                                    <strong>Visibilidad:</strong> {{ championship.is_public ? 'Público' : 'Privado' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Fechas Importantes</h6>
                                <p v-if="championship.start_date" class="mb-1">
                                    <i class="fas fa-play text-success mr-2"></i>
                                    <strong>Inicio:</strong> {{ formatDate(championship.start_date, false) }}
                                </p>
                                <p v-if="championship.end_date" class="mb-1">
                                    <i class="fas fa-stop text-danger mr-2"></i>
                                    <strong>Finalización:</strong> {{ formatDate(championship.end_date, false) }}
                                </p>
                                <p v-if="championship.registration_start" class="mb-1">
                                    <i class="fas fa-user-plus text-info mr-2"></i>
                                    <strong>Inscripciones desde:</strong> {{ formatDate(championship.registration_start, false) }}
                                </p>
                                <p v-if="championship.registration_end" class="mb-0">
                                    <i class="fas fa-user-times text-warning mr-2"></i>
                                    <strong>Inscripciones hasta:</strong> {{ formatDate(championship.registration_end, false) }}
                                </p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-2">Estadísticas</h6>
                                <div class="row">
                                    <div class="col-md-3 col-6">
                                        <div class="info-box bg-primary">
                                            <div class="info-box-content">
                                                <span class="info-box-text text-white">Participantes</span>
                                                <span class="info-box-number text-white">{{ championship.participants_count || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="info-box bg-success">
                                            <div class="info-box-content">
                                                <span class="info-box-text text-white">Jornadas</span>
                                                <span class="info-box-number text-white">{{ championship.matchdays_count || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="info-box bg-info">
                                            <div class="info-box-content">
                                                <span class="info-box-text text-white">Categorías</span>
                                                <span class="info-box-number text-white">{{ championship.categories_count || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="info-box bg-warning">
                                            <div class="info-box-content">
                                                <span class="info-box-text text-white">Clubes</span>
                                                <span class="info-box-number text-white">{{ championship.clubs_count || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Configuraciones -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Configuraciones</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <i class="fas fa-user-plus text-primary mr-2"></i>
                                        <strong>Inscripción externa:</strong> 
                                        <span :class="championship.allow_external_registration ? 'text-success' : 'text-danger'">
                                            {{ championship.allow_external_registration ? 'Permitida' : 'No permitida' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Fechas de registro -->
                        <div class="row text-muted small">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-plus-circle mr-1"></i>
                                    Creado: {{ formatDate(championship.created_at) }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-edit mr-1"></i>
                                    Última actualización: {{ formatDate(championship.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Panel lateral -->
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Resumen del Campeonato
                                </h6>
                            </div>
                            <div class="card-body">
                                <dl class="row small">
                                    <dt class="col-sm-5">Nombre:</dt>
                                    <dd class="col-sm-7">{{ championship.name }}</dd>
                                    
                                    <dt class="col-sm-5">Año:</dt>
                                    <dd class="col-sm-7">{{ championship.year }}</dd>
                                    
                                    <dt class="col-sm-5">Tipo:</dt>
                                    <dd class="col-sm-7">{{ getTypeLabel(championship.type) }}</dd>
                                    
                                    <dt class="col-sm-5">Estado:</dt>
                                    <dd class="col-sm-7">
                                        <span :class="getStatusClass(championship.status)">
                                            {{ getStatusLabel(championship.status) }}
                                        </span>
                                    </dd>
                                    
                                    <dt class="col-sm-5">Duración:</dt>
                                    <dd class="col-sm-7">{{ getDuration() }}</dd>
                                </dl>
                            </div>
                        </div>

                        <!-- Acciones rápidas -->
                        <div v-if="canEdit" class="card mt-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-tools mr-1"></i>
                                    Acciones
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <router-link 
                                        :to="{ name: 'championships.edit', params: { id: championshipId } }" 
                                        class="btn btn-primary btn-sm"
                                    >
                                        <i class="fas fa-edit mr-1"></i>
                                        Editar Campeonato
                                    </router-link>
                                    <router-link 
                                        :to="{ name: 'matchdays', query: { championship: championshipId } }" 
                                        class="btn btn-outline-primary btn-sm"
                                    >
                                        <i class="fas fa-calendar mr-1"></i>
                                        Ver Jornadas
                                    </router-link>
                                    <button 
                                        v-if="championship.participants_count > 0"
                                        @click="exportParticipants" 
                                        class="btn btn-outline-success btn-sm"
                                    >
                                        <i class="fas fa-download mr-1"></i>
                                        Exportar Participantes
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Progreso del campeonato -->
                        <div v-if="championship.start_date && championship.end_date" class="card mt-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    Progreso
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="progress mb-2">
                                    <div 
                                        class="progress-bar" 
                                        role="progressbar" 
                                        :style="`width: ${getProgress()}%`"
                                        :aria-valuenow="getProgress()"
                                        aria-valuemin="0" 
                                        aria-valuemax="100"
                                    ></div>
                                </div>
                                <small class="text-muted">{{ getProgress() }}% completado</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de jornadas (si existen) -->
                <div v-if="matchdays && matchdays.length > 0" class="mt-5">
                    <h5 class="mb-3">
                        <i class="fas fa-calendar mr-2"></i>
                        Jornadas del Campeonato ({{ matchdays.length }})
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Jornada</th>
                                    <th>Fecha</th>
                                    <th>Ubicación</th>
                                    <th>Estado</th>
                                    <th>Participantes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="matchday in matchdays" :key="matchday.id">
                                    <td>{{ matchday.name || `Jornada ${matchday.round_number}` }}</td>
                                    <td>{{ formatDate(matchday.date, false) }}</td>
                                    <td>{{ matchday.location || 'Por definir' }}</td>
                                    <td>
                                        <span :class="getMatchdayStatusClass(matchday.status)">
                                            {{ getMatchdayStatusLabel(matchday.status) }}
                                        </span>
                                    </td>
                                    <td>{{ matchday.participants_count || 0 }}</td>
                                    <td>
                                        <button 
                                            @click="navigateToMatchday(matchday.id)"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Ver detalles de la jornada"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-else class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h4 class="text-muted">Campeonato no encontrado</h4>
                <p class="text-muted">El campeonato que buscas no existe o ha sido eliminado.</p>
                <router-link :to="{ name: 'championships' }" class="btn btn-primary">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver a Campeonatos
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ChampionshipDetail',
    data() {
        return {
            championship: null,
            matchdays: [],
            loading: false
        };
    },
    computed: {
        championshipId() {
            return this.$route.params.id;
        },
        canEdit() {
            return window.Laravel?.user?.authenticated || false;
        }
    },
    mounted() {
        this.loadChampionship();
        this.loadChampionshipMatchdays();
    },
    methods: {
        async loadChampionship() {
            this.loading = true;
            try {
                const response = await fetch(`/api/championships/${this.championshipId}`);
                if (!response.ok) {
                    throw new Error('Campeonato no encontrado');
                }
                const data = await response.json();
                this.championship = data;
            } catch (error) {
                console.error('Error loading championship:', error);
                this.championship = null;
            } finally {
                this.loading = false;
            }
        },

        async loadChampionshipMatchdays() {
            try {
                const response = await fetch(`/api/championships/${this.championshipId}/matchdays`);
                if (response.ok) {
                    const data = await response.json();
                    this.matchdays = data.data || data;
                }
            } catch (error) {
                console.error('Error loading championship matchdays:', error);
            }
        },

        getTypeLabel(type) {
            const labels = {
                'local': 'Local',
                'regional': 'Regional',
                'national': 'Nacional',
                'international': 'Internacional'
            };
            return labels[type] || 'Desconocido';
        },

        getStatusClass(status) {
            const classes = {
                'draft': 'badge badge-secondary',
                'planned': 'badge badge-info',
                'registration_open': 'badge badge-success',
                'registration_closed': 'badge badge-warning',
                'active': 'badge badge-primary',
                'completed': 'badge badge-dark',
                'cancelled': 'badge badge-danger'
            };
            return classes[status] || 'badge badge-secondary';
        },

        getStatusLabel(status) {
            const labels = {
                'draft': 'Borrador',
                'planned': 'Planificado',
                'registration_open': 'Inscripciones Abiertas',
                'registration_closed': 'Inscripciones Cerradas',
                'active': 'En Curso',
                'completed': 'Finalizado',
                'cancelled': 'Cancelado'
            };
            return labels[status] || 'Desconocido';
        },

        getMatchdayStatusClass(status) {
            const classes = {
                'scheduled': 'badge badge-info',
                'active': 'badge badge-primary',
                'completed': 'badge badge-success',
                'cancelled': 'badge badge-danger'
            };
            return classes[status] || 'badge badge-secondary';
        },

        getMatchdayStatusLabel(status) {
            const labels = {
                'scheduled': 'Programada',
                'active': 'En Curso',
                'completed': 'Completada',
                'cancelled': 'Cancelada'
            };
            return labels[status] || 'Desconocido';
        },

        getDuration() {
            if (!this.championship || !this.championship.start_date || !this.championship.end_date) {
                return 'No definida';
            }
            
            const start = new Date(this.championship.start_date);
            const end = new Date(this.championship.end_date);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays === 1) {
                return '1 día';
            } else if (diffDays < 30) {
                return `${diffDays} días`;
            } else if (diffDays < 365) {
                const months = Math.round(diffDays / 30);
                return `${months} ${months === 1 ? 'mes' : 'meses'}`;
            } else {
                const years = Math.round(diffDays / 365);
                return `${years} ${years === 1 ? 'año' : 'años'}`;
            }
        },

        getProgress() {
            if (!this.championship || !this.championship.start_date || !this.championship.end_date) {
                return 0;
            }

            const now = new Date();
            const start = new Date(this.championship.start_date);
            const end = new Date(this.championship.end_date);

            if (now < start) return 0;
            if (now > end) return 100;

            const total = end - start;
            const current = now - start;
            return Math.round((current / total) * 100);
        },

        async exportParticipants() {
            try {
                const response = await fetch(`/api/championships/${this.championshipId}/participants/export`);
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = `participantes_${this.championship.name}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    this.showNotification('Exportación completada', 'success');
                } else {
                    throw new Error('Error al exportar');
                }
            } catch (error) {
                console.error('Error exporting participants:', error);
                this.showNotification('Error al exportar participantes', 'error');
            }
        },

        formatDate(dateString, includeTime = true) {
            if (!dateString) return 'No disponible';
            try {
                const date = new Date(dateString);
                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                
                if (includeTime) {
                    options.hour = '2-digit';
                    options.minute = '2-digit';
                }
                
                return date.toLocaleDateString('es-ES', options);
            } catch (error) {
                return 'Fecha inválida';
            }
        },

        navigateToMatchday(matchdayId) {
            this.$router.push({
                name: 'matchdays.show',
                params: { id: matchdayId },
                query: {
                    from: 'championship',
                    championshipId: this.championshipId
                }
            });
        },

        showNotification(message, type = 'info') {
            console.log(`${type.toUpperCase()}: ${message}`);
            if (type === 'error') {
                alert('Error: ' + message);
            }
        }
    }
};
</script>

<style scoped>
.championship-detail {
    max-width: 1200px;
    margin: 0 auto;
}

.info-box {
    border-radius: 0.25rem;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
}

.info-box-content {
    text-align: center;
}

.info-box-text {
    font-size: 0.75rem;
    font-weight: 600;
}

.info-box-number {
    font-size: 1.25rem;
    font-weight: bold;
}

.text-justify {
    text-align: justify;
}

.d-grid {
    display: grid;
}

.gap-2 {
    gap: 0.5rem;
}

.small {
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .card-tools {
        flex-direction: column;
        align-items: stretch;
    }
    
    .card-tools .btn {
        margin-bottom: 0.25rem;
    }
}
</style>
