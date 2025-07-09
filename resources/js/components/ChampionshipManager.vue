<template>
    <div class="championship-manager">
        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ stats.total }}</h3>
                        <p>Total Campeonatos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ stats.active }}</h3>
                        <p>Activos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-play"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ stats.completed }}</h3>
                        <p>Completados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ stats.totalMatchdays }}</h3>
                        <p>Jornadas Totales</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card principal -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy mr-2"></i>
                    Campeonatos Disponibles
                </h3>
                <div class="card-tools">
                    <!-- Toggle de vista -->
                    <div class="btn-group mr-2" role="group">
                        <button 
                            type="button" 
                            @click="viewMode = 'cards'"
                            :class="['btn', 'btn-outline-primary', 'btn-sm', { 'active': viewMode === 'cards' }]">
                            <i class="fas fa-th-large"></i> Cards
                        </button>
                        <button 
                            type="button" 
                            @click="viewMode = 'list'"
                            :class="['btn', 'btn-outline-primary', 'btn-sm', { 'active': viewMode === 'list' }]">
                            <i class="fas fa-list"></i> Lista
                        </button>
                    </div>
                    
                    <div class="btn-group">
                        <button @click="exportChampionships" class="btn btn-success btn-sm" title="Exportar a CSV">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <router-link v-if="canCreate" :to="{ name: 'championships.create' }" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nuevo Campeonato
                        </router-link>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Loading Spinner -->
                <LoadingSpinner v-if="loading" />

                <template v-else>
                    <!-- Filtros -->
                    <form @submit.prevent="loadChampionships" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input 
                                    type="text" 
                                    v-model="filters.search"
                                    @input="handleSearchInput"
                                    class="form-control" 
                                    placeholder="Buscar campeonatos...">
                            </div>
                            <div class="col-md-2">
                                <select v-model="filters.year" @change="handleFilterChange" class="form-control">
                                    <option value="">Todos los años</option>
                                    <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select v-model="filters.status" @change="handleFilterChange" class="form-control">
                                    <option value="">Todos los estados</option>
                                    <option value="planned">Planeado</option>
                                    <option value="active">Activo</option>
                                    <option value="completed">Completado</option>
                                    <option value="cancelled">Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button type="button" @click="clearFilters" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Vista de Cards -->
                    <div v-if="viewMode === 'cards' && championships.length > 0" class="row">
                        <div v-for="championship in championships" :key="championship.id" class="col-lg-4 col-md-6 col-sm-12 mb-4">
                            <div class="card championship-card h-100 shadow-sm">
                                <div class="card-header bg-gradient-primary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-trophy mr-2"></i>
                                            {{ championship.name }}
                                        </h5>
                                        <span class="badge badge-light">{{ championship.year }}</span>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <strong>Estado:</strong><br>
                                            <StatusBadge :status="championship.status" :type="'championship'" />
                                        </div>
                                        <div class="col-6">
                                            <strong>Jornadas:</strong><br>
                                            <span class="badge badge-info">{{ championship.matchdays_count || 0 }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <strong>Período:</strong><br>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                {{ formatDateRange(championship.start_date, championship.end_date) }}
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div v-if="championship.description" class="mb-3">
                                        <strong>Descripción:</strong><br>
                                        <small class="text-muted">{{ limitText(championship.description, 80) }}</small>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-light">
                                    <div class="btn-group w-100" role="group">
                                        <router-link :to="{ name: 'championships.show', params: { id: championship.id } }" 
                                           class="btn btn-info btn-sm" title="Ver detalles">
                                            <i class="fas fa-eye"></i> Ver
                                        </router-link>
                                        <router-link :to="{ name: 'matchdays', query: { championship: championship.id } }" 
                                           class="btn btn-secondary btn-sm" title="Ver jornadas">
                                            <i class="fas fa-calendar"></i> Jornadas
                                        </router-link>
                                        <router-link v-if="canEdit" :to="{ name: 'championships.edit', params: { id: championship.id } }" 
                                           class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </router-link>
                                    </div>
                                    <div v-if="canDelete" class="mt-2">
                                        <button @click="confirmDelete(championship)" 
                                                class="btn btn-danger btn-sm w-100" title="Eliminar">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vista de Lista -->
                    <div v-if="viewMode === 'list' && championships.length > 0" class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th @click="sortBy('name')" class="sortable">
                                        Nombre
                                        <i :class="getSortIcon('name')"></i>
                                    </th>
                                    <th @click="sortBy('year')" class="sortable">
                                        Año
                                        <i :class="getSortIcon('year')"></i>
                                    </th>
                                    <th>Descripción</th>
                                    <th @click="sortBy('status')" class="sortable">
                                        Estado
                                        <i :class="getSortIcon('status')"></i>
                                    </th>
                                    <th>Fechas</th>
                                    <th>Jornadas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="championship in championships" :key="championship.id">
                                    <td>
                                        <strong>{{ championship.name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ championship.year }}</span>
                                    </td>
                                    <td>
                                        {{ limitText(championship.description || 'Sin descripción', 50) }}
                                    </td>
                                    <td>
                                        <StatusBadge :status="championship.status" :type="'championship'" />
                                    </td>
                                    <td>
                                        <small v-if="championship.start_date">
                                            {{ formatDate(championship.start_date) }}
                                            <br v-if="championship.end_date">
                                            <span v-if="championship.end_date">{{ formatDate(championship.end_date) }}</span>
                                        </small>
                                        <span v-else class="text-muted">Sin fechas</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ championship.matchdays_count || 0 }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <router-link :to="{ name: 'championships.show', params: { id: championship.id } }" class="btn btn-sm btn-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </router-link>
                                            <router-link :to="{ name: 'matchdays', query: { championship: championship.id } }" class="btn btn-sm btn-secondary" title="Jornadas">
                                                <i class="fas fa-calendar"></i>
                                            </router-link>
                                            <router-link v-if="canEdit" :to="{ name: 'championships.edit', params: { id: championship.id } }" class="btn btn-sm btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </router-link>
                                            <button v-if="canDelete" @click="confirmDelete(championship)" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Estado vacío -->
                    <div v-if="championships.length === 0 && !loading" class="text-center py-5">
                        <i class="fas fa-trophy fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No hay campeonatos registrados</h4>
                        <p class="text-muted">Comience creando su primer campeonato para organizar las competencias</p>
                        <router-link v-if="canCreate" :to="{ name: 'championships.create' }" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus"></i> Crear Primer Campeonato
                        </router-link>
                    </div>

                    <!-- Paginación -->
                    <DataPagination
                        v-if="pagination && pagination.last_page > 1"
                        :pagination="pagination"
                        @page-changed="changePage"
                    />
                </template>
            </div>
        </div>

        <!-- Modal de confirmación de eliminación -->
        <div v-if="showDeleteModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Eliminación</h5>
                        <button type="button" class="close" @click="showDeleteModal = false">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar el campeonato <strong>{{ championshipToDelete?.name }}</strong>?</p>
                        <p class="text-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Esta acción no se puede deshacer y eliminará todas las jornadas asociadas.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showDeleteModal = false">Cancelar</button>
                        <button type="button" class="btn btn-danger" @click="deleteChampionship" :disabled="deleteLoading">
                            <i v-if="deleteLoading" class="fas fa-spinner fa-spin"></i>
                            {{ deleteLoading ? 'Eliminando...' : 'Eliminar' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sistema de notificaciones -->
        <NotificationSystem :notifications="notifications" @close="removeNotification" />
    </div>
</template>

<script>
import NotificationSystem from './NotificationSystem.vue';

export default {
    name: 'ChampionshipManager',
    components: {
        NotificationSystem
    },
    data() {
        return {
            loading: false,
            deleteLoading: false,
            championships: [],
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
                completed: 0,
                totalMatchdays: 0
            },
            routes: {
                api: '/api/championships',
                show: '/campeonatos/{id}',
                edit: '/gestionar/campeonatos/{id}/editar',
                create: '/gestionar/campeonatos/crear',
                delete: '/api/championships/{id}',
                export: '/api/championships/export'
            },
            permissions: {
                canCreate: window.Laravel?.user?.authenticated || false,
                canEdit: window.Laravel?.user?.authenticated || false,
                canDelete: window.Laravel?.user?.authenticated || false
            },
            filters: {
                search: '',
                year: '',
                status: '',
                page: 1
            },
            sortField: 'year',
            sortDirection: 'desc',
            viewMode: 'cards', // 'cards' o 'list'
            searchTimeout: null,
            notifications: [],
            showDeleteModal: false,
            championshipToDelete: null,
            availableYears: []
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
        console.log('ChampionshipManager mounted, checking for initial data...');
        
        this.loadViewMode();
        this.generateAvailableYears();
        
        // Check if we have initial data from server
        if (window.Laravel && window.Laravel.initialData && window.Laravel.initialData.page === 'championships-list') {
            console.log('Using initial championships data from server:', window.Laravel.initialData);
            const data = window.Laravel.initialData.championships;
            
            if (data.data) {
                this.championships = data.data;
                this.pagination = {
                    current_page: data.current_page || 1,
                    last_page: data.last_page || 1,
                    total: data.total || 0,
                    per_page: data.per_page || 10,
                    from: data.from || 0,
                    to: data.to || 0
                };
            } else {
                this.championships = Array.isArray(data) ? data : [];
                this.pagination.total = this.championships.length;
            }
            
            this.loading = false;
        } else {
            console.log('No initial data found, loading from API...');
            this.loadChampionships();
        }
    },
    methods: {
        async loadChampionships() {
            this.loading = true;
            console.log('ChampionshipManager: loadChampionships called, loading from API...');
            try {
                const params = new URLSearchParams({
                    ...this.filters,
                    sort_field: this.sortField,
                    sort_direction: this.sortDirection
                });

                const apiUrl = `${this.routes.api}?${params}`;
                console.log('ChampionshipManager: Fetching from URL:', apiUrl);
                const response = await axios.get(apiUrl);
                
                console.log('ChampionshipManager: Response status:', response.status);
                console.log('ChampionshipManager: Response data:', response.data);
                
                if (response.data.success) {
                    this.championships = response.data.data;
                    this.pagination = response.data.pagination;
                    this.stats = response.data.stats;
                    this.availableYears = response.data.available_years || this.availableYears;
                    console.log('ChampionshipManager: Championships loaded:', this.championships.length);
                } else {
                    console.log('ChampionshipManager: API returned success=false');
                    this.showNotification('Error al cargar campeonatos', 'error');
                }
            } catch (error) {
                console.error('Error loading championships:', error);
                this.showNotification('Error de conexión al cargar campeonatos', 'error');
            } finally {
                this.loading = false;
            }
        },

        handleSearchInput() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.filters.page = 1;
                this.loadChampionships();
            }, 500);
        },

        handleFilterChange() {
            this.filters.page = 1;
            this.loadChampionships();
        },

        clearFilters() {
            this.filters = {
                search: '',
                year: '',
                status: '',
                page: 1
            };
            this.loadChampionships();
        },

        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'asc';
            }
            this.loadChampionships();
        },

        getSortIcon(field) {
            if (this.sortField !== field) {
                return 'fas fa-sort text-muted';
            }
            return this.sortDirection === 'asc' ? 'fas fa-sort-up text-primary' : 'fas fa-sort-down text-primary';
        },

        changePage(page) {
            this.filters.page = page;
            this.loadChampionships();
        },

        async exportChampionships() {
            try {
                const params = new URLSearchParams(this.filters);
                const response = await axios.get(`${this.routes.export}?${params}`, {
                    responseType: 'blob'
                });
                
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `campeonatos_${new Date().toISOString().split('T')[0]}.csv`);
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);
                
                this.showNotification('Campeonatos exportados exitosamente', 'success');
            } catch (error) {
                console.error('Error exporting championships:', error);
                this.showNotification('Error al exportar campeonatos', 'error');
            }
        },

        confirmDelete(championship) {
            this.championshipToDelete = championship;
            this.showDeleteModal = true;
        },

        async deleteChampionship() {
            if (!this.championshipToDelete) return;
            
            this.deleteLoading = true;
            try {
                const response = await axios.delete(`${this.routes.destroy.replace(':id', this.championshipToDelete.id)}`);
                
                if (response.data.success) {
                    this.showNotification('Campeonato eliminado exitosamente', 'success');
                    this.loadChampionships();
                } else {
                    this.showNotification(response.data.message || 'Error al eliminar campeonato', 'error');
                }
            } catch (error) {
                console.error('Error deleting championship:', error);
                this.showNotification('Error al eliminar campeonato', 'error');
            } finally {
                this.deleteLoading = false;
                this.showDeleteModal = false;
                this.championshipToDelete = null;
            }
        },

        // Métodos de utilidad
        formatDate(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleDateString('es-ES');
        },

        formatDateRange(startDate, endDate) {
            if (!startDate && !endDate) return 'Sin fechas';
            if (!endDate) return this.formatDate(startDate);
            return `${this.formatDate(startDate)} - ${this.formatDate(endDate)}`;
        },

        limitText(text, limit) {
            if (!text) return '';
            return text.length > limit ? text.substring(0, limit) + '...' : text;
        },

        generateAvailableYears() {
            const currentYear = new Date().getFullYear();
            const years = [];
            for (let year = currentYear + 2; year >= currentYear - 10; year--) {
                years.push(year);
            }
            this.availableYears = years;
        },

        loadViewMode() {
            const savedView = localStorage.getItem('championships_view_mode');
            if (savedView && ['cards', 'list'].includes(savedView)) {
                this.viewMode = savedView;
            }
        },

        saveViewMode() {
            localStorage.setItem('championships_view_mode', this.viewMode);
        },

        showNotification(message, type = 'info') {
            const notification = {
                id: Date.now(),
                message,
                type,
                duration: type === 'error' ? 5000 : 3000
            };
            this.notifications.push(notification);
        },

        removeNotification(notificationId) {
            const index = this.notifications.findIndex(n => n.id === notificationId);
            if (index > -1) {
                this.notifications.splice(index, 1);
            }
        }
    },

    watch: {
        viewMode() {
            this.saveViewMode();
        }
    }
}
</script>

<style scoped>
.championship-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.championship-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn-group .btn.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.table th.sortable {
    cursor: pointer;
    user-select: none;
}

.table th.sortable:hover {
    background-color: #e9ecef;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa;
}

.table-hover tbody tr:hover {
    background-color: rgba(255, 193, 7, 0.05);
}

.modal.show {
    display: block;
}
</style>
