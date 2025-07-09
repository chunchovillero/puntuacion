<template>
    <div class="club-detail">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Detalles del Club
                </h3>
                <div class="card-tools">
                    <router-link :to="{ name: 'clubs' }" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Clubes
                    </router-link>
                    <router-link 
                        v-if="canEdit" 
                        :to="{ name: 'clubs.edit', params: { id: clubId } }" 
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
                <p class="mt-3">Cargando información del club...</p>
            </div>

            <div v-else-if="club" class="card-body">
                <div class="row">
                    <!-- Información principal -->
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h4 class="mb-0">{{ club.name }}</h4>
                            <span :class="getStatusClass(club.status)">
                                {{ getStatusLabel(club.status) }}
                            </span>
                        </div>
                        
                        <div v-if="club.description" class="mb-4">
                            <h6 class="text-muted mb-2">Descripción</h6>
                            <p class="text-justify">{{ club.description }}</p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Información General</h6>
                                <p v-if="club.city" class="mb-1">
                                    <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                    <strong>Ciudad:</strong> {{ club.city }}
                                </p>
                                <p v-if="club.state" class="mb-1">
                                    <i class="fas fa-map text-primary mr-2"></i>
                                    <strong>Estado/Provincia:</strong> {{ club.state }}
                                </p>
                                <p v-if="club.country" class="mb-1">
                                    <i class="fas fa-globe text-primary mr-2"></i>
                                    <strong>País:</strong> {{ club.country }}
                                </p>
                                <p v-if="club.founded_date" class="mb-0">
                                    <i class="fas fa-calendar text-primary mr-2"></i>
                                    <strong>Fundado:</strong> {{ formatDate(club.founded_date, false) }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Contacto</h6>
                                <p v-if="club.phone" class="mb-1">
                                    <i class="fas fa-phone text-success mr-2"></i>
                                    <strong>Teléfono:</strong> {{ club.phone }}
                                </p>
                                <p v-if="club.email" class="mb-1">
                                    <i class="fas fa-envelope text-info mr-2"></i>
                                    <strong>Email:</strong> 
                                    <a :href="'mailto:' + club.email">{{ club.email }}</a>
                                </p>
                                <p v-if="club.website" class="mb-1">
                                    <i class="fas fa-link text-warning mr-2"></i>
                                    <strong>Sitio web:</strong> 
                                    <a :href="club.website" target="_blank">{{ club.website }}</a>
                                </p>
                                <p v-if="club.address" class="mb-0">
                                    <i class="fas fa-home text-danger mr-2"></i>
                                    <strong>Dirección:</strong> {{ club.address }}
                                </p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-2">Estadísticas</h6>
                                <div class="row">
                                    <div class="col-md-6 col-6">
                                        <div class="info-box bg-primary">
                                            <div class="info-box-content">
                                                <span class="info-box-text text-white">Total Pilotos</span>
                                                <span class="info-box-number text-white">{{ club.pilots_count || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <div class="info-box bg-success">
                                            <div class="info-box-content">
                                                <span class="info-box-text text-white">Pilotos Activos</span>
                                                <span class="info-box-number text-white">{{ club.active_pilots_count || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Redes sociales -->
                        <div v-if="hasSocialMedia" class="mb-4">
                            <h6 class="text-muted mb-2">Redes Sociales</h6>
                            <div class="btn-group">
                                <a v-if="club.facebook" :href="club.facebook" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fab fa-facebook mr-1"></i> Facebook
                                </a>
                                <a v-if="club.instagram" :href="club.instagram" target="_blank" class="btn btn-outline-danger btn-sm">
                                    <i class="fab fa-instagram mr-1"></i> Instagram
                                </a>
                                <a v-if="club.twitter" :href="club.twitter" target="_blank" class="btn btn-outline-info btn-sm">
                                    <i class="fab fa-twitter mr-1"></i> Twitter
                                </a>
                            </div>
                        </div>

                        <!-- Fechas de registro -->
                        <div class="row text-muted small">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-plus-circle mr-1"></i>
                                    Registrado: {{ formatDate(club.created_at) }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-edit mr-1"></i>
                                    Última actualización: {{ formatDate(club.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Panel lateral -->
                    <div class="col-md-4">
                        <!-- Logo del club -->
                        <div v-if="club.logo" class="card bg-light mb-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-image mr-1"></i>
                                    Logo del Club
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <img :src="`/storage/${club.logo}`" :alt="club.name" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        </div>

                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Resumen del Club
                                </h6>
                            </div>
                            <div class="card-body">
                                <dl class="row small">
                                    <dt class="col-sm-5">Nombre:</dt>
                                    <dd class="col-sm-7">{{ club.name }}</dd>
                                    
                                    <dt class="col-sm-5">Ciudad:</dt>
                                    <dd class="col-sm-7">{{ club.city || 'No especificada' }}</dd>
                                    
                                    <dt class="col-sm-5">Estado:</dt>
                                    <dd class="col-sm-7">
                                        <span :class="getStatusClass(club.status)">
                                            {{ getStatusLabel(club.status) }}
                                        </span>
                                    </dd>
                                    
                                    <dt class="col-sm-5">Pilotos:</dt>
                                    <dd class="col-sm-7">{{ club.pilots_count || 0 }}</dd>
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
                                        :to="{ name: 'clubs.edit', params: { id: clubId } }" 
                                        class="btn btn-primary btn-sm"
                                    >
                                        <i class="fas fa-edit mr-1"></i>
                                        Editar Club
                                    </router-link>
                                    <router-link 
                                        :to="{ 
                                            name: 'pilots', 
                                            query: { 
                                                club: clubId, 
                                                from: 'club',
                                                clubId: clubId 
                                            } 
                                        }" 
                                        class="btn btn-outline-primary btn-sm"
                                    >
                                        <i class="fas fa-users mr-1"></i>
                                        Ver Pilotos
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de pilotos (si existen) -->
                <div v-if="club.pilots && club.pilots.length > 0" class="mt-5">
                    <h5 class="mb-3">
                        <i class="fas fa-users mr-2"></i>
                        Pilotos del Club ({{ club.pilots.length }})
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Puntos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pilot in club.pilots" :key="pilot.id">
                                    <td>{{ pilot.first_name }} {{ pilot.last_name }}</td>
                                    <td>{{ pilot.category?.name || 'Sin categoría' }}</td>
                                    <td>{{ pilot.ranking_points || 0 }}</td>
                                    <td>
                                        <span :class="getPilotStatusClass(pilot.status)">
                                            {{ getPilotStatusLabel(pilot.status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <router-link 
                                            :to="{ 
                                                name: 'pilots.show', 
                                                params: { id: pilot.id },
                                                query: { 
                                                    from: 'club', 
                                                    clubId: clubId 
                                                }
                                            }" 
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
                <h4 class="text-muted">Club no encontrado</h4>
                <p class="text-muted">El club que buscas no existe o ha sido eliminado.</p>
                <router-link :to="{ name: 'clubs' }" class="btn btn-primary">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver a Clubes
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ClubDetail',
    data() {
        return {
            club: null,
            loading: false
        };
    },
    computed: {
        clubId() {
            return this.$route.params.id;
        },
        canEdit() {
            return window.Laravel?.user?.authenticated || false;
        },
        hasSocialMedia() {
            return this.club && (this.club.facebook || this.club.instagram || this.club.twitter);
        }
    },
    mounted() {
        this.loadClub();
    },
    methods: {
        async loadClub() {
            this.loading = true;
            try {
                // Primero verificar si hay datos iniciales del servidor
                if (window.Laravel && window.Laravel.initialData && window.Laravel.initialData.page === 'club-detail') {
                    this.club = window.Laravel.initialData.club;
                } else {
                    // Si no hay datos iniciales, cargar desde API
                    const response = await fetch(`/api/clubs/${this.clubId}`);
                    if (!response.ok) {
                        throw new Error('Club no encontrado');
                    }
                    const data = await response.json();
                    this.club = data.success ? data.data : data;
                }
            } catch (error) {
                console.error('Error loading club:', error);
                this.club = null;
            } finally {
                this.loading = false;
            }
        },

        getStatusClass(status) {
            const classes = {
                'active': 'badge badge-success',
                'inactive': 'badge badge-secondary',
                'suspended': 'badge badge-warning'
            };
            return classes[status] || 'badge badge-secondary';
        },

        getStatusLabel(status) {
            const labels = {
                'active': 'Activo',
                'inactive': 'Inactivo',
                'suspended': 'Suspendido'
            };
            return labels[status] || 'Desconocido';
        },

        getPilotStatusClass(status) {
            const classes = {
                'active': 'badge badge-success',
                'inactive': 'badge badge-secondary',
                'suspended': 'badge badge-warning'
            };
            return classes[status] || 'badge badge-secondary';
        },

        getPilotStatusLabel(status) {
            const labels = {
                'active': 'Activo',
                'inactive': 'Inactivo',
                'suspended': 'Suspendido'
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
.club-detail {
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
