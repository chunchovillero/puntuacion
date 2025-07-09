<template>
    <div class="matchday-detail">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar mr-2"></i>
                    Detalle de la Jornada
                </h3>
                <div class="card-tools">
                    <router-link :to="getBackUrl()" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        {{ getBackText() }}
                    </router-link>
                    <router-link 
                        v-if="canEdit" 
                        :to="{ name: 'matchdays.edit', params: { id: matchdayId } }" 
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
                <p class="mt-3">Cargando información de la jornada...</p>
            </div>

            <div v-else-if="matchday" class="card-body">
                <div class="row">
                    <!-- Información principal -->
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h4 class="mb-0">{{ matchday.name || `Jornada ${matchday.round_number}` }}</h4>
                            <span :class="getStatusClass(matchday.status)">
                                {{ getStatusLabel(matchday.status) }}
                            </span>
                        </div>
                        
                        <div v-if="matchday.description" class="mb-4">
                            <h6 class="text-muted mb-2">Descripción</h6>
                            <p class="text-justify">{{ matchday.description }}</p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Información General</h6>
                                <p v-if="matchday.date" class="mb-1">
                                    <i class="fas fa-calendar text-primary mr-2"></i>
                                    <strong>Fecha:</strong> {{ formatDate(matchday.date, false) }}
                                </p>
                                <p v-if="matchday.location" class="mb-1">
                                    <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                    <strong>Ubicación:</strong> {{ matchday.location }}
                                </p>
                                <p v-if="matchday.organizer_club" class="mb-1">
                                    <i class="fas fa-users text-primary mr-2"></i>
                                    <strong>Club Organizador:</strong> {{ matchday.organizer_club.name }}
                                </p>
                                <p v-if="matchday.championship" class="mb-0">
                                    <i class="fas fa-trophy text-primary mr-2"></i>
                                    <strong>Campeonato:</strong> 
                                    <router-link :to="{ name: 'championships.show', params: { id: matchday.championship.id } }">
                                        {{ matchday.championship.name }}
                                    </router-link>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Detalles de la Jornada</h6>
                                <p v-if="matchday.round_number" class="mb-1">
                                    <i class="fas fa-hashtag text-success mr-2"></i>
                                    <strong>Número de Ronda:</strong> {{ matchday.round_number }}
                                </p>
                                <p v-if="matchday.registration_deadline" class="mb-1">
                                    <i class="fas fa-clock text-warning mr-2"></i>
                                    <strong>Fecha límite inscripción:</strong> {{ formatDate(matchday.registration_deadline) }}
                                </p>
                                <p v-if="matchday.entry_fee" class="mb-1">
                                    <i class="fas fa-dollar-sign text-success mr-2"></i>
                                    <strong>Costo de inscripción:</strong> ${{ matchday.entry_fee }}
                                </p>
                                <p class="mb-0">
                                    <i class="fas fa-users text-info mr-2"></i>
                                    <strong>Participantes:</strong> {{ matchday.participants_count || 0 }}
                                </p>
                            </div>
                        </div>

                        <!-- Fechas de registro -->
                        <div class="row text-muted small">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-plus-circle mr-1"></i>
                                    Creada: {{ formatDate(matchday.created_at) }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-edit mr-1"></i>
                                    Última actualización: {{ formatDate(matchday.updated_at) }}
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
                                    Resumen de la Jornada
                                </h6>
                            </div>
                            <div class="card-body">
                                <dl class="row small">
                                    <dt class="col-sm-5">Nombre:</dt>
                                    <dd class="col-sm-7">{{ matchday.name || `Jornada ${matchday.round_number}` }}</dd>
                                    
                                    <dt class="col-sm-5">Fecha:</dt>
                                    <dd class="col-sm-7">{{ formatDate(matchday.date, false) || 'Por definir' }}</dd>
                                    
                                    <dt class="col-sm-5">Estado:</dt>
                                    <dd class="col-sm-7">
                                        <span :class="getStatusClass(matchday.status)">
                                            {{ getStatusLabel(matchday.status) }}
                                        </span>
                                    </dd>
                                    
                                    <dt class="col-sm-5">Participantes:</dt>
                                    <dd class="col-sm-7">{{ matchday.participants_count || 0 }}</dd>
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
                                        :to="{ name: 'matchdays.edit', params: { id: matchdayId } }" 
                                        class="btn btn-primary btn-sm"
                                    >
                                        <i class="fas fa-edit mr-1"></i>
                                        Editar Jornada
                                    </router-link>
                                    <router-link 
                                        :to="{ name: 'matchdays', query: { championship: matchday.championship_id } }" 
                                        class="btn btn-outline-primary btn-sm"
                                    >
                                        <i class="fas fa-list mr-1"></i>
                                        Ver Todas las Jornadas
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de participantes (si existen) -->
                <div v-if="matchday.participants && matchday.participants.length > 0" class="mt-5">
                    <h5 class="mb-3">
                        <i class="fas fa-users mr-2"></i>
                        Participantes de la Jornada ({{ matchday.participants.length }})
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Piloto</th>
                                    <th>Club</th>
                                    <th>Categoría</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="participant in matchday.participants" :key="participant.id">
                                    <td>{{ participant.pilot?.first_name }} {{ participant.pilot?.last_name }}</td>
                                    <td>{{ participant.pilot?.club?.name || 'Sin club' }}</td>
                                    <td>{{ participant.pilot?.category?.name || 'Sin categoría' }}</td>
                                    <td>
                                        <span :class="getParticipantStatusClass(participant.status)">
                                            {{ getParticipantStatusLabel(participant.status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <router-link 
                                            :to="{ name: 'pilots.show', params: { id: participant.pilot?.id } }" 
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </router-link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-else class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h4 class="text-muted">Jornada no encontrada</h4>
                <p class="text-muted">La jornada que buscas no existe o ha sido eliminada.</p>
                <router-link :to="{ name: 'matchdays' }" class="btn btn-primary">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver a Jornadas
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'MatchdayDetail',
    data() {
        return {
            matchday: null,
            loading: false
        };
    },
    computed: {
        matchdayId() {
            return this.$route.params.id;
        },
        canEdit() {
            return window.Laravel?.user?.authenticated || false;
        },
        // Detectar desde dónde viene el usuario
        fromChampionship() {
            return this.$route.query.from === 'championship' && this.$route.query.championshipId;
        },
        fromChampionshipId() {
            return this.$route.query.championshipId;
        }
    },
    mounted() {
        this.loadMatchday();
    },
    methods: {
        async loadMatchday() {
            this.loading = true;
            try {
                // Primero verificar si hay datos iniciales del servidor
                if (window.Laravel && window.Laravel.initialData && window.Laravel.initialData.page === 'matchday-detail') {
                    this.matchday = window.Laravel.initialData.matchday;
                } else {
                    // Si no hay datos iniciales, cargar desde API
                    const response = await fetch(`/api/matchdays/${this.matchdayId}`);
                    if (!response.ok) {
                        throw new Error('Jornada no encontrada');
                    }
                    const data = await response.json();
                    this.matchday = data.success ? data.data : data;
                }
            } catch (error) {
                console.error('Error loading matchday:', error);
                this.matchday = null;
            } finally {
                this.loading = false;
            }
        },

        // Métodos para navegación inteligente
        getBackUrl() {
            if (this.fromChampionship) {
                return `/campeonatos/${this.fromChampionshipId}`;
            }
            return '/jornadas';
        },
        
        getBackText() {
            if (this.fromChampionship) {
                return 'Campeonato';
            }
            return 'Jornadas';
        },

        getStatusClass(status) {
            const classes = {
                'active': 'badge badge-success',
                'completed': 'badge badge-primary',
                'cancelled': 'badge badge-danger',
                'postponed': 'badge badge-warning',
                'draft': 'badge badge-secondary'
            };
            return classes[status] || 'badge badge-secondary';
        },

        getStatusLabel(status) {
            const labels = {
                'active': 'Activa',
                'completed': 'Completada',
                'cancelled': 'Cancelada',
                'postponed': 'Pospuesta',
                'draft': 'Borrador'
            };
            return labels[status] || 'Desconocido';
        },

        getParticipantStatusClass(status) {
            const classes = {
                'active': 'badge badge-success',
                'inactive': 'badge badge-secondary',
                'disqualified': 'badge badge-danger'
            };
            return classes[status] || 'badge badge-secondary';
        },

        getParticipantStatusLabel(status) {
            const labels = {
                'active': 'Activo',
                'inactive': 'Inactivo',
                'disqualified': 'Descalificado'
            };
            return labels[status] || 'Desconocido';
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
        }
    }
};
</script>

<style scoped>
.matchday-detail {
    max-width: 1200px;
    margin: 0 auto;
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
