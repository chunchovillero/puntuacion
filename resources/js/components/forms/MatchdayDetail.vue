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
                            <h4 class="mb-0">{{ matchday.name || `Jornada ${matchday.number}` }}</h4>
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
                                <p v-if="matchday.venue" class="mb-1">
                                    <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                    <strong>Ubicación:</strong> {{ matchday.venue }}
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
                                <p v-if="matchday.number" class="mb-1">
                                    <i class="fas fa-hashtag text-success mr-2"></i>
                                    <strong>Número de Ronda:</strong> {{ matchday.number }}
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
                                    <dd class="col-sm-7">{{ matchday.name || `Jornada ${matchday.number}` }}</dd>
                                    
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

                <!-- Lista de participantes agrupados por categoría -->
                <div v-if="matchday.participants && matchday.participants.length > 0" class="mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-users mr-2"></i>
                            Participantes por Categoría ({{ matchday.participants.length }})
                        </h5>
                        <add-pilot-to-matchday 
                            v-if="canEdit"
                            :matchday-id="matchdayId"
                            @pilot-added="onPilotAdded"
                        />
                    </div>
                    
                    <!-- Acordeón de categorías -->
                    <div id="categoriesAccordion">
                        <div v-for="(categoryGroup, index) in categoriesData" :key="`category-${index}`" class="card mb-2">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <button 
                                        class="btn btn-link btn-block text-left"
                                        type="button" 
                                        @click="toggleCategory(index)"
                                        style="text-decoration: none;"
                                    >
                                        <i class="fas fa-layer-group mr-2"></i>
                                        {{ categoryGroup.category }}
                                        <span class="badge badge-primary ml-2">{{ categoryGroup.pilots.length }}</span>
                                        <i class="fas fa-chevron-down float-right mt-1" 
                                           :style="{ transform: isCategoryExpanded(index) ? 'rotate(180deg)' : 'rotate(0deg)', transition: 'transform 0.3s' }"></i>
                                    </button>
                                </h6>
                            </div>
                            
                            <div v-show="isCategoryExpanded(index)" class="card-body category-content">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="40"></th>
                                                <th>Piloto</th>
                                                <th>Club</th>
                                                <th>Edad</th>
                                                <th>Estado</th>
                                                <th width="100">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="participant in categoryGroup.pilots" :key="participant.id">
                                                <td>
                                                    <img 
                                                        v-if="participant.pilot?.photo" 
                                                        :src="'/storage/' + participant.pilot.photo" 
                                                        :alt="'Foto de ' + participant.pilot.first_name + ' ' + participant.pilot.last_name"
                                                        class="rounded-circle" 
                                                        style="width: 32px; height: 32px; object-fit: cover;"
                                                    >
                                                    <div 
                                                        v-else
                                                        class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                                        style="width: 32px; height: 32px;"
                                                    >
                                                        <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong>{{ participant.pilot?.first_name }} {{ participant.pilot?.last_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">ID: {{ participant.pilot?.id }}</small>
                                                </td>
                                                <td>
                                                    <span v-if="participant.pilot?.club">
                                                        {{ participant.pilot.club.name }}
                                                    </span>
                                                    <span v-else class="text-muted">Sin club</span>
                                                </td>
                                                <td>{{ participant.pilot?.age || 'N/A' }}</td>
                                                <td>
                                                    <span :class="getParticipantStatusClass(participant.status)">
                                                        {{ getParticipantStatusLabel(participant.status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <router-link 
                                                        :to="{ 
                                                            name: 'pilots.show', 
                                                            params: { id: participant.pilot?.id },
                                                            query: {
                                                                from: 'matchday',
                                                                matchdayId: matchdayId
                                                            }
                                                        }" 
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="Ver detalles del piloto"
                                                    >
                                                        <i class="fas fa-eye"></i>
                                                    </router-link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Estadísticas rápidas de la categoría -->
                                <div class="mt-3 text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-chart-bar mr-1"></i>
                                        {{ categoryGroup.stats.active }} activos, 
                                        {{ categoryGroup.stats.inactive }} inactivos, 
                                        {{ categoryGroup.stats.clubs }} clubes representados
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mensaje cuando no hay participantes -->
                <div v-else-if="!loading" class="mt-5">
                    <div class="text-center py-5">
                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay participantes registrados</h5>
                        <p class="text-muted">Esta jornada aún no tiene pilotos inscritos.</p>
                        <div v-if="canEdit" class="mt-3">
                            <add-pilot-to-matchday 
                                :matchday-id="matchdayId"
                                @pilot-added="onPilotAdded"
                            />
                        </div>
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
import AddPilotToMatchday from './AddPilotToMatchday.vue';

export default {
    name: 'MatchdayDetail',
    components: {
        AddPilotToMatchday
    },
    data() {
        return {
            matchday: null,
            loading: false,
            categoriesData: [], // Nueva propiedad para manejar el estado del acordeón
            expandedCategories: {} // Objeto para controlar el estado expandido de cada categoría
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
                }                } catch (error) {
                console.error('Error loading matchday:', error);
                this.matchday = null;
            } finally {
                this.loading = false;
                // Procesar categorías después de cargar
                this.processCategories();
            }
        },

        processCategories() {
            if (!this.matchday || !this.matchday.participants) {
                this.categoriesData = [];
                this.expandedCategories = {};
                return;
            }
            
            // Agrupar por categoría
            const grouped = {};
            
            this.matchday.participants.forEach(participant => {
                const categoryName = participant.pilot?.category?.name || 'Sin categoría';
                
                if (!grouped[categoryName]) {
                    grouped[categoryName] = {
                        category: categoryName,
                        pilots: [],
                        stats: {
                            active: 0,
                            inactive: 0,
                            clubs: new Set()
                        }
                    };
                }
                
                grouped[categoryName].pilots.push(participant);
                
                // Calcular estadísticas
                if (participant.status === 'active' || participant.status === 'confirmed') {
                    grouped[categoryName].stats.active++;
                } else {
                    grouped[categoryName].stats.inactive++;
                }
                
                if (participant.pilot?.club?.name) {
                    grouped[categoryName].stats.clubs.add(participant.pilot.club.name);
                }
            });
            
            // Convertir el Set de clubes a número
            Object.values(grouped).forEach(group => {
                group.stats.clubs = group.stats.clubs.size;
            });
            
            // Convertir a array y ordenar por nombre de categoría
            const result = Object.values(grouped).sort((a, b) => {
                // 'Sin categoría' siempre al final
                if (a.category === 'Sin categoría') return 1;
                if (b.category === 'Sin categoría') return -1;
                return a.category.localeCompare(b.category);
            });
            
            this.categoriesData = result;
            
            // Inicializar estado expandido (primera categoría expandida por defecto)
            const expandedState = {};
            result.forEach((category, index) => {
                expandedState[index] = index === 0; // Solo la primera expandida
            });
            this.expandedCategories = expandedState;
        },

        toggleCategory(index) {
            // Usar Vue.set para asegurar reactividad
            this.$set(this.expandedCategories, index, !this.expandedCategories[index]);
        },

        isCategoryExpanded(index) {
            return this.expandedCategories[index] || false;
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
        },

        onPilotAdded(newParticipant) {
            if (this.matchday && this.matchday.participants) {
                this.matchday.participants.push(newParticipant);
                this.matchday.participants_count++;
                this.processCategories();
            }
        },
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

/* Estilos para el acordeón de categorías */
#categoriesAccordion .card {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

#categoriesAccordion .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 0;
}

#categoriesAccordion .btn-link {
    color: #495057;
    text-decoration: none;
    padding: 1rem;
    width: 100%;
    text-align: left;
    border: none;
    background: none;
    font-weight: 500;
}

#categoriesAccordion .btn-link:hover {
    color: #007bff;
    background-color: #e9ecef;
    text-decoration: none;
}

#categoriesAccordion .btn-link:focus {
    box-shadow: none;
}

#categoriesAccordion .btn-link .fa-chevron-down {
    transition: transform 0.3s ease-in-out;
}

#categoriesAccordion .btn-link .fa-chevron-down.rotate-180 {
    transform: rotate(180deg);
}

#categoriesAccordion .category-content {
    border-top: 1px solid #dee2e6;
    animation: slideDown 0.3s ease-in-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
    }
    to {
        opacity: 1;
        max-height: 1000px;
    }
}

#categoriesAccordion .table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    color: #6c757d;
}

#categoriesAccordion .table td {
    vertical-align: middle;
    border-top: 1px solid #f1f3f4;
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
