<template>
    <div class="category-manager">
        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ stats.total }}</h3>
                        <p>Total Categorías</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tags"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ stats.active }}</h3>
                        <p>Categorías Activas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ stats.championships }}</h3>
                        <p>Campeonatos Activos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ stats.totalPilots }}</h3>
                        <p>Total Pilotos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card principal -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Categorías</h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <button @click="exportCategories" class="btn btn-success btn-sm" title="Exportar a CSV">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <router-link v-if="canCreate" :to="{ name: 'categories.create' }" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nueva Categoría
                        </router-link>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Loading Spinner -->
                <LoadingSpinner v-if="loading" />

                <template v-else>
                    <!-- Filtros -->
                    <form @submit.prevent="loadCategories" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <input 
                                    type="text" 
                                    v-model="filters.search"
                                    @input="handleSearchInput"
                                    class="form-control" 
                                    placeholder="Buscar categorías...">
                            </div>
                            <div class="col-md-2">
                                <select v-model="filters.type" @change="handleFilterChange" class="form-control">
                                    <option value="">Todos los tipos</option>
                                    <option value="escuela">Escuela</option>
                                    <option value="novicios">Novicios</option>
                                    <option value="juvenil">Juvenil</option>
                                    <option value="adulto">Adulto</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select v-model="filters.gender" @change="handleFilterChange" class="form-control">
                                    <option value="">Todos los géneros</option>
                                    <option value="varones">Varones</option>
                                    <option value="mujeres">Mujeres</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select v-model="filters.status" @change="handleFilterChange" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="active">Activa</option>
                                    <option value="inactive">Inactiva</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <button type="button" @click="clearFilters" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Nota explicativa -->
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Diferencias entre conteos:</strong>
                        <br>
                        • <strong>Columnas de campeonatos:</strong> Número de pilotos <em>registrados</em> en ese campeonato específico para esta categoría
                        <br>
                        • <strong>Pilotos Asignados:</strong> Total de pilotos que tienen asignada esta categoría en su perfil general
                        <br>
                        • <strong>¿Por qué son diferentes?</strong> Un piloto puede tener una categoría asignada en su perfil pero no estar inscrito en ningún campeonato, o puede participar en campeonatos con una categoría diferente a la de su perfil
                    </div>

                    <!-- Tabla de categorías -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover categories-table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="min-width: 200px;">Nombre</th>
                                    <th style="min-width: 100px;">Tipo</th>
                                    <th style="min-width: 100px;">Género</th>
                                    <th style="min-width: 130px;">Rango de Edad</th>
                                    <th 
                                        v-for="championship in championships" 
                                        :key="championship.id"
                                        class="text-center championship-column"
                                        :title="`Pilotos registrados específicamente en ${championship.name} (${championship.year}) para esta categoría`"
                                    >
                                        <div class="font-weight-bold">{{ truncateText(championship.name, 15) }}</div>
                                        <small class="text-muted d-block">{{ championship.year }}</small>
                                        <small class="text-info d-block">Registrados</small>
                                    </th>
                                    <th class="text-center" style="min-width: 80px;" title="Total de pilotos que tienen asignada esta categoría en su perfil (independiente de inscripciones a campeonatos)">
                                        <div class="font-weight-bold">Pilotos</div>
                                        <small class="text-muted d-block">Asignados</small>
                                    </th>
                                    <th class="text-center" style="min-width: 100px;">Estado</th>
                                    <th class="text-center" style="min-width: 120px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="category in categories" :key="category.id">
                                    <td>
                                        <strong>{{ category.name }}</strong>
                                        <br v-if="category.description">
                                        <small v-if="category.description" class="text-muted">
                                            {{ truncateText(category.description, 50) }}
                                        </small>
                                    </td>
                                    <td>
                                        <span :class="getTypeBadgeClass(category.type)">
                                            {{ capitalizeFirst(category.type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span v-if="category.gender" :class="getGenderBadgeClass(category.gender)">
                                            {{ capitalizeFirst(category.gender) }}
                                        </span>
                                        <span v-else class="text-muted">Mixto</span>
                                    </td>
                                    <td>{{ formatAgeRange(category) }}</td>
                                    <td 
                                        v-for="championship in championships" 
                                        :key="championship.id"
                                        class="text-center pilot-count-cell"
                                        :title="`${getChampionshipCount(category, championship.id)} pilotos registrados en ${championship.name} ${championship.year} para la categoría '${category.name}'`"
                                    >
                                        <span 
                                            v-if="getChampionshipCount(category, championship.id) > 0" 
                                            class="badge badge-success"
                                        >
                                            {{ getChampionshipCount(category, championship.id) }}
                                        </span>
                                        <span v-else class="zero-count">-</span>
                                    </td>
                                    <td class="text-center pilot-count-cell" :title="`${category.pilots_count || 0} pilotos tienen asignada la categoría '${category.name}' en su perfil`">
                                        <span class="badge badge-info">{{ category.pilots_count || 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <button 
                                            v-if="canEdit" 
                                            @click="toggleStatus(category)"
                                            :class="getStatusButtonClass(category.active)"
                                            :title="category.active ? 'Desactivar' : 'Activar'"
                                        >
                                            <i :class="category.active ? 'fas fa-check' : 'fas fa-times'"></i>
                                            {{ category.active ? 'Activa' : 'Inactiva' }}
                                        </button>
                                        <span v-else :class="getStatusBadgeClass(category.active)">
                                            <i :class="category.active ? 'fas fa-check' : 'fas fa-times'"></i>
                                            {{ category.active ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <router-link :to="{ name: 'categories.show', params: { id: category.id } }" class="btn btn-info btn-sm" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </router-link>
                                            <router-link v-if="canEdit" :to="{ name: 'categories.edit', params: { id: category.id } }" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </router-link>
                                            <button 
                                                v-if="canDelete" 
                                                @click="confirmDelete(category)"
                                                class="btn btn-danger btn-sm" 
                                                title="Eliminar"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="categories.length === 0">
                                    <td :colspan="7 + championships.length" class="text-center text-muted">
                                        <i class="fas fa-info-circle"></i> No hay categorías registradas
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div v-if="pagination.total > 0" class="mt-3">
                        <DataPagination
                            :current-page="pagination.current_page"
                            :last-page="pagination.last_page"
                            :total="pagination.total"
                            :per-page="pagination.per_page"
                            :from="pagination.from"
                            :to="pagination.to"
                            @page-change="changePage"
                        />
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingSpinner from './LoadingSpinner.vue';
import DataPagination from './DataPagination.vue';

export default {
    name: 'CategoryManager',
    components: {
        LoadingSpinner,
        DataPagination
    },
    data() {
        return {
            categories: [],
            championships: [],
            loading: true,
            searchTimeout: null,
            filters: {
                search: '',
                type: '',
                gender: '',
                status: ''
            },
            pagination: {
                current_page: 1,
                last_page: 1,
                total: 0,
                per_page: 15,
                from: 0,
                to: 0
            },
            stats: {
                total: 0,
                active: 0,
                championships: 0,
                totalPilots: 0
            },
            routes: {
                api: '/api/categories',
                show: '/categorias/{id}',
                edit: '/gestionar/categorias/{id}/editar',
                create: '/gestionar/categorias/crear',
                delete: '/api/categories/{id}',
                toggleStatus: '/gestionar/categorias/{id}/cambiar-estado',
                export: '/api/categories/export'
            },
            permissions: {
                canCreate: window.Laravel?.user?.authenticated || false,
                canEdit: window.Laravel?.user?.authenticated || false,
                canDelete: window.Laravel?.user?.authenticated || false
            }
        }
    },
    computed: {
        canCreate() {
            return this.permissions.canCreate;
        },
        canEdit() {
            return this.permissions.canEdit;
        },
        canDelete() {
            return this.permissions.canDelete;
        }
    },
    mounted() {
        console.log('CategoryManager mounted, checking for initial data...');
        console.log('window.Laravel:', window.Laravel);
        console.log('window.Laravel.initialData:', window.Laravel?.initialData);
        
        // Check if we have initial data from server
        if (window.Laravel && window.Laravel.initialData && window.Laravel.initialData.page === 'categories-list') {
            console.log('Using initial categories data from server:', window.Laravel.initialData);
            const data = window.Laravel.initialData.categories;
            
            if (data.data) {
                this.categories = data.data;
                this.pagination = {
                    current_page: data.current_page || 1,
                    last_page: data.last_page || 1,
                    total: data.total || 0,
                    per_page: data.per_page || 10,
                    from: data.from || 0,
                    to: data.to || 0
                };
            } else {
                this.categories = Array.isArray(data) ? data : [];
                this.pagination.total = this.categories.length;
            }
            
            // When using initial data, we need to load additional data from API
            this.loadAdditionalData();
            this.loading = false;
        } else {
            console.log('No initial data found, loading from API...');
            this.loadCategories();
        }
    },
    methods: {
        async loadAdditionalData() {
            try {
                console.log('CategoryManager: loadAdditionalData called, API URL:', this.routes.api);
                const response = await axios.get(this.routes.api);
                
                console.log('CategoryManager: Additional data response:', response.data);
                const data = response.data;
                this.championships = data.championships || [];
                this.stats = data.stats || this.calculateLocalStats();
            } catch (error) {
                console.error('Error loading additional data:', error);
                // Fallback to local stats calculation
                this.stats = this.calculateLocalStats();
            }
        },

        calculateLocalStats() {
            return {
                total: this.categories.length,
                active: this.categories.filter(cat => cat.active).length,
                championships: 0,
                totalPilots: this.categories.reduce((sum, cat) => sum + (cat.pilots_count || 0), 0)
            };
        },

        async loadCategories() {
            this.loading = true;
            console.log('CategoryManager: loadCategories called, loading from API...');
            console.log('CategoryManager: API URL:', this.routes.api);
            try {
                // Solo incluir parámetros que tienen valores
                const params = new URLSearchParams();
                params.append('page', this.pagination.current_page);
                
                if (this.filters.search && this.filters.search.trim() !== '') {
                    params.append('search', this.filters.search.trim());
                }
                if (this.filters.type && this.filters.type !== '') {
                    params.append('type', this.filters.type);
                }
                if (this.filters.gender && this.filters.gender !== '') {
                    params.append('gender', this.filters.gender);
                }
                if (this.filters.status && this.filters.status !== '') {
                    params.append('status', this.filters.status);
                }

                const apiUrl = `${this.routes.api}?${params}`;
                console.log('CategoryManager: Fetching from URL:', apiUrl);
                const response = await axios.get(apiUrl);
                
                console.log('CategoryManager: Response status:', response.status);
                console.log('CategoryManager: Response data:', response.data);

                const data = response.data;
                this.categories = data.data || [];
                this.championships = data.championships || [];
                this.stats = data.stats || this.calculateLocalStats();
                this.pagination = {
                    current_page: data.current_page || 1,
                    last_page: data.last_page || 1,
                    total: data.total || 0,
                    per_page: data.per_page || 15,
                    from: data.from || 0,
                    to: data.to || 0
                };
                
                console.log('CategoryManager: Categories loaded:', this.categories.length);
            } catch (error) {
                console.error('Error loading categories:', error);
                this.showNotification('Error al cargar las categorías', 'error');
                this.categories = [];
            } finally {
                this.loading = false;
            }
        },

        handleSearchInput() {
            // Auto-submit con delay
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            this.searchTimeout = setTimeout(() => {
                this.pagination.current_page = 1;
                this.loadCategories();
            }, 800);
        },

        handleFilterChange() {
            this.pagination.current_page = 1;
            this.loadCategories();
        },

        clearFilters() {
            this.filters.search = '';
            this.filters.type = '';
            this.filters.gender = '';
            this.filters.status = '';
            this.pagination.current_page = 1;
            this.loadCategories();
        },

        changePage(page) {
            this.pagination.current_page = page;
            this.loadCategories();
        },

        async toggleStatus(category) {
            try {
                const response = await fetch(this.routes.toggleStatus.replace('{id}', category.id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cambiar el estado');
                }

                const status = category.active ? 'desactivada' : 'activada';
                category.active = !category.active;
                this.showNotification(`Categoría ${status} exitosamente`, 'success');
                
                // Actualizar estadísticas
                this.updateStats();
            } catch (error) {
                console.error('Error toggling status:', error);
                this.showNotification('Error al cambiar el estado de la categoría', 'error');
            }
        },

        async confirmDelete(category) {
            if (confirm(`¿Está seguro de eliminar la categoría "${category.name}"? Esta acción no se puede deshacer.`)) {
                await this.deleteCategory(category);
            }
        },

        async deleteCategory(category) {
            try {
                const response = await fetch(this.routes.delete.replace('{id}', category.id), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error al eliminar la categoría');
                }

                this.showNotification('Categoría eliminada exitosamente', 'success');
                this.loadCategories();
            } catch (error) {
                console.error('Error deleting category:', error);
                this.showNotification(error.message || 'Error al eliminar la categoría', 'error');
            }
        },

        async exportCategories() {
            try {
                const params = new URLSearchParams({
                    search: this.filters.search,
                    type: this.filters.type,
                    gender: this.filters.gender,
                    status: this.filters.status
                });

                const response = await fetch(`${this.routes.export}?${params}`);
                
                if (!response.ok) {
                    throw new Error('Error al exportar las categorías');
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `categorias_bmx_${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                this.showNotification('Categorías exportadas exitosamente', 'success');
            } catch (error) {
                console.error('Error exporting categories:', error);
                this.showNotification('Error al exportar las categorías', 'error');
            }
        },

        getTypeBadgeClass(type) {
            const baseClass = 'badge';
            switch (type) {
                case 'novicios': return `${baseClass} badge-info`;
                case 'escuela': return `${baseClass} badge-primary`;
                default: return `${baseClass} badge-success`;
            }
        },

        getGenderBadgeClass(gender) {
            const baseClass = 'badge';
            return gender === 'varones' ? `${baseClass} badge-primary` : `${baseClass} badge-pink`;
        },

        getStatusButtonClass(active) {
            return `btn btn-sm ${active ? 'btn-success' : 'btn-secondary'}`;
        },

        getStatusBadgeClass(active) {
            return `badge ${active ? 'badge-success' : 'badge-secondary'}`;
        },

        formatAgeRange(category) {
            if (category.age_min && category.age_max) {
                return `${category.age_min}-${category.age_max} años`;
            } else if (category.age_min) {
                return `${category.age_min}+ años`;
            } else if (category.age_max) {
                return `hasta ${category.age_max} años`;
            }
            return 'Sin límite';
        },

        getChampionshipCount(category, championshipId) {
            return category.championship_counts?.[championshipId] || 0;
        },

        updateStats() {
            this.stats = this.calculateLocalStats();
        },

        capitalizeFirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        },

        truncateText(text, length) {
            if (!text) return '';
            return text.length > length ? text.substring(0, length) + '...' : text;
        },

        showNotification(message, type) {
            // Emit event for notification system
            this.$root.$emit('show-notification', { message, type });
        }
    }
}
</script>

<style scoped>
.categories-table {
    font-size: 0.9rem;
}

.championship-column {
    min-width: 120px;
    max-width: 120px;
}

.pilot-count-cell {
    vertical-align: middle;
}

.zero-count {
    color: #6c757d;
    font-weight: normal;
}

.badge-pink {
    color: #fff;
    background-color: #e83e8c;
}

.small-box {
    border-radius: 0.375rem;
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.small-box > .inner {
    padding: 10px;
}

.small-box .icon {
    transition: all 0.3s linear;
    position: absolute;
    top: -10px;
    right: 10px;
    z-index: 0;
    font-size: 90px;
    color: rgba(0,0,0,0.15);
}

.small-box h3 {
    font-size: 2.2rem;
    font-weight: bold;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}

.small-box p {
    font-size: 1rem;
}

.small-box h3, .small-box p {
    z-index: 5;
    position: relative;
}

.bg-info {
    background-color: #17a2b8 !important;
    color: #fff;
}

.bg-success {
    background-color: #28a745 !important;
    color: #fff;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #212529;
}

.bg-danger {
    background-color: #dc3545 !important;
    color: #fff;
}

.table-active {
    background-color: rgba(0,0,0,0.075);
}

@media (max-width: 768px) {
    .championship-column {
        min-width: 100px;
        max-width: 100px;
    }
    
    .small-box h3 {
        font-size: 1.8rem;
    }
    
    .small-box .icon {
        font-size: 60px;
    }
}
</style>
