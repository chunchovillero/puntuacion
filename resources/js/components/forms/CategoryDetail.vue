<template>
    <div class="category-detail">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tags mr-2"></i>
                    Detalles de la Categoría
                </h3>
                <div class="card-tools">
                    <router-link :to="{ name: 'categories' }" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Categorías
                    </router-link>
                    <router-link 
                        v-if="canEdit" 
                        :to="{ name: 'categories.edit', params: { id: categoryId } }" 
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
                <p class="mt-3">Cargando información de la categoría...</p>
            </div>

            <div v-else-if="category" class="card-body">
                <div class="row">
                    <!-- Información principal -->
                    <div class="col-md-8">
                        <h4 class="mb-3">{{ category.name }}</h4>
                        
                        <div v-if="category.description" class="mb-4">
                            <h6 class="text-muted mb-2">Descripción</h6>
                            <p class="text-justify">{{ category.description }}</p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Rango de Edad</h6>
                                <p class="mb-1">
                                    <i class="fas fa-birthday-cake text-primary mr-2"></i>
                                    <span v-if="category.min_age || category.max_age">
                                        {{ getAgeRange() }}
                                    </span>
                                    <span v-else class="text-muted">Sin restricción de edad</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Género</h6>
                                <p class="mb-1">
                                    <i class="fas fa-venus-mars text-primary mr-2"></i>
                                    {{ getGenderLabel() }}
                                </p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Estado</h6>
                                <span :class="getStatusClass(category.status)">
                                    {{ getStatusLabel(category.status) }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Tipo</h6>
                                <span :class="category.is_competitive ? 'badge badge-success' : 'badge badge-info'">
                                    <i :class="category.is_competitive ? 'fas fa-trophy' : 'fas fa-heart'" class="mr-1"></i>
                                    {{ category.is_competitive ? 'Competitiva' : 'Recreativa' }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Estadísticas</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box bg-primary">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-white">Total Pilotos</span>
                                            <span class="info-box-number text-white">{{ category.pilots_count || 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-success">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-white">Activos</span>
                                            <span class="info-box-number text-white">{{ category.active_pilots_count || 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-info">
                                        <div class="info-box-content">
                                            <span class="info-box-text text-white">En Competencias</span>
                                            <span class="info-box-number text-white">{{ category.competitions_count || 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fechas de registro -->
                        <div class="row text-muted small">
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-plus-circle mr-1"></i>
                                    Creada: {{ formatDate(category.created_at) }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1">
                                    <i class="fas fa-edit mr-1"></i>
                                    Última actualización: {{ formatDate(category.updated_at) }}
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
                                    Resumen de la Categoría
                                </h6>
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-5">Nombre:</dt>
                                    <dd class="col-sm-7">{{ category.name }}</dd>
                                    
                                    <dt class="col-sm-5">Edades:</dt>
                                    <dd class="col-sm-7">{{ getAgeRange() || 'Sin límite' }}</dd>
                                    
                                    <dt class="col-sm-5">Género:</dt>
                                    <dd class="col-sm-7">{{ getGenderLabel() }}</dd>
                                    
                                    <dt class="col-sm-5">Tipo:</dt>
                                    <dd class="col-sm-7">{{ category.is_competitive ? 'Competitiva' : 'Recreativa' }}</dd>
                                    
                                    <dt class="col-sm-5">Estado:</dt>
                                    <dd class="col-sm-7">
                                        <span :class="getStatusClass(category.status)">
                                            {{ getStatusLabel(category.status) }}
                                        </span>
                                    </dd>
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
                                        :to="{ name: 'categories.edit', params: { id: categoryId } }" 
                                        class="btn btn-primary btn-sm"
                                    >
                                        <i class="fas fa-edit mr-1"></i>
                                        Editar Categoría
                                    </router-link>
                                    <router-link 
                                        :to="{ name: 'pilots', query: { category: categoryId } }" 
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
                <div v-if="pilots && pilots.length > 0" class="mt-5">
                    <h5 class="mb-3">
                        <i class="fas fa-user-ninja mr-2"></i>
                        Pilotos en esta Categoría ({{ pilots.length }})
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Número</th>
                                    <th>Club</th>
                                    <th>Edad</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pilot in pilots" :key="pilot.id">
                                    <td>{{ pilot.first_name }} {{ pilot.last_name }}</td>
                                    <td>{{ pilot.number }}</td>
                                    <td>
                                        <span v-if="pilot.club">{{ pilot.club.name }}</span>
                                        <span v-else class="text-muted">Sin club</span>
                                    </td>
                                    <td>{{ pilot.age || 'N/A' }}</td>
                                    <td>
                                        <span :class="getPilotStatusClass(pilot.status)">
                                            {{ getPilotStatusLabel(pilot.status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <router-link 
                                            :to="{ name: 'pilots.show', params: { id: pilot.id } }" 
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
                <h4 class="text-muted">Categoría no encontrada</h4>
                <p class="text-muted">La categoría que buscas no existe o ha sido eliminada.</p>
                <router-link :to="{ name: 'categories' }" class="btn btn-primary">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Volver a Categorías
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'CategoryDetail',
    data() {
        return {
            category: null,
            pilots: [],
            loading: false
        };
    },
    computed: {
        categoryId() {
            return this.$route.params.id;
        },
        canEdit() {
            return window.Laravel?.user?.authenticated || false;
        }
    },
    mounted() {
        this.loadCategory();
        this.loadCategoryPilots();
    },
    methods: {
        async loadCategory() {
            this.loading = true;
            try {
                const response = await fetch(`/api/categories/${this.categoryId}`);
                if (!response.ok) {
                    throw new Error('Categoría no encontrada');
                }
                const data = await response.json();
                this.category = data;
            } catch (error) {
                console.error('Error loading category:', error);
                this.category = null;
            } finally {
                this.loading = false;
            }
        },

        async loadCategoryPilots() {
            try {
                const response = await fetch(`/api/categories/${this.categoryId}/pilots`);
                if (response.ok) {
                    const data = await response.json();
                    this.pilots = data.data || data;
                }
            } catch (error) {
                console.error('Error loading category pilots:', error);
            }
        },

        getAgeRange() {
            if (!this.category) return '';
            
            const min = this.category.min_age;
            const max = this.category.max_age;
            
            if (min && max) {
                return `${min} - ${max} años`;
            } else if (min) {
                return `${min}+ años`;
            } else if (max) {
                return `Hasta ${max} años`;
            }
            return '';
        },

        getGenderLabel() {
            if (!this.category) return 'Mixto';
            
            const labels = {
                'male': 'Masculino',
                'female': 'Femenino',
                '': 'Mixto'
            };
            return labels[this.category.gender] || 'Mixto';
        },

        getStatusClass(status) {
            const classes = {
                'active': 'badge badge-success',
                'inactive': 'badge badge-secondary'
            };
            return classes[status] || 'badge badge-secondary';
        },

        getStatusLabel(status) {
            const labels = {
                'active': 'Activa',
                'inactive': 'Inactiva'
            };
            return labels[status] || 'Desconocido';
        },

        getPilotStatusClass(status) {
            const classes = {
                'active': 'badge badge-success',
                'inactive': 'badge badge-secondary',
                'suspended': 'badge badge-danger'
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

        formatDate(dateString) {
            if (!dateString) return 'No disponible';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                return 'Fecha inválida';
            }
        }
    }
};
</script>

<style scoped>
.category-detail {
    max-width: 1000px;
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
    font-size: 0.875rem;
    font-weight: 600;
}

.info-box-number {
    font-size: 1.5rem;
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
