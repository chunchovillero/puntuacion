<template>
    <div class="pilot-manager">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-user-ninja mr-2"></i>
                            Lista de Pilotos BMX
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><router-link to="/">Inicio</router-link></li>
                            <li v-if="fromClub" class="breadcrumb-item">
                                <router-link :to="`/clubes/${fromClubId}`">Club</router-link>
                            </li>
                            <li class="breadcrumb-item active">Pilotos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="card-title">Gestión de Pilotos</h3>
                            </div>
                            <div class="col-md-6">
                                <div class="card-tools float-right">
                                    <!-- Toggle de vista -->
                                    <div class="btn-group mr-2" role="group">
                                        <button 
                                            type="button" 
                                            @click="viewMode = 'cards'" 
                                            :class="['btn', 'btn-outline-primary', 'btn-sm', { active: viewMode === 'cards' }]"
                                        >
                                            <i class="fas fa-th-large"></i> Cards
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="viewMode = 'list'" 
                                            :class="['btn', 'btn-outline-primary', 'btn-sm', { active: viewMode === 'list' }]"
                                        >
                                            <i class="fas fa-list"></i> Lista
                                        </button>
                                    </div>
                                    
                                    <button v-if="canCreate" @click="exportPilots" class="btn btn-success btn-sm mr-2">
                                        <i class="fas fa-file-excel"></i> Exportar
                                    </button>
                                    
                                    <router-link 
                                        v-if="canCreate" 
                                        :to="{ name: 'pilots.create' }" 
                                        class="btn btn-primary btn-sm"
                                    >
                                        <i class="fas fa-plus"></i> Nuevo Piloto
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón de regreso al club (si viene desde un club) -->
                    <div v-if="fromClub" class="card-header bg-light">
                        <div class="row">
                            <div class="col-12">
                                <router-link 
                                    :to="`/clubes/${fromClubId}`" 
                                    class="btn btn-outline-secondary btn-sm"
                                >
                                    <i class="fas fa-arrow-left mr-1"></i>
                                    Volver al club
                                </router-link>
                                <span class="ml-3 text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Mostrando pilotos del club
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filtros -->
                    <div class="card-header" v-if="!loading">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search">Buscar pilotos</label>
                                    <input 
                                        id="search"
                                        ref="searchInput"
                                        v-model="filters.search" 
                                        @input="handleSearch"
                                        @focus="isSearchFocused = true"
                                        @blur="isSearchFocused = false"
                                        type="text" 
                                        class="form-control" 
                                        placeholder="Buscar pilotos..."
                                    >
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">Estado</label>
                                    <select id="status" v-model="filters.status" @change="handleStatusChange" class="form-control">
                                        <option value="">Todos los estados</option>
                                        <option value="active">Activos</option>
                                        <option value="inactive">Inactivos</option>
                                        <option value="suspended">Suspendidos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="category">Categoría</label>
                                    <select id="category" v-model="filters.category" @change="handleCategoryChange" class="form-control">
                                        <option v-for="category in categoryOptions" :key="category.value" :value="category.value">
                                            {{ category.label }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="club">Club</label>
                                    <select id="club" v-model="filters.club" @change="handleClubChange" class="form-control">
                                        <option v-for="club in clubOptions" :key="club.value" :value="club.value">
                                            {{ club.label }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button @click="clearFilters" class="btn btn-outline-secondary btn-block">
                                        <i class="fas fa-filter mr-1"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
            
            <!-- Loading -->
            <div v-if="loading" class="card-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3">Cargando pilotos...</p>
            </div>
            
            <!-- Content -->
            <div v-else class="card-body">
                <!-- Vista de Cards -->
                <div v-if="viewMode === 'cards'" id="cards-view">
                    <div v-if="pilots.length > 0" class="row">
                        <div v-for="pilot in pilots" :key="pilot.id" class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <img 
                                            v-if="pilot.photo" 
                                            :src="'/storage/' + pilot.photo" 
                                            :alt="'Foto de ' + pilot.first_name + ' ' + pilot.last_name"
                                            class="rounded-circle mr-3" 
                                            style="width: 50px; height: 50px; object-fit: cover;"
                                        >
                                        <div 
                                            v-else
                                            class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mr-3" 
                                            style="width: 50px; height: 50px;"
                                        >
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="card-title mb-1">{{ pilot.first_name }} {{ pilot.last_name }}</h5>
                                            <small class="text-muted">ID {{ pilot.id }}</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <strong>Club:</strong> 
                                        <span v-if="pilot.club">{{ pilot.club.name }}</span>
                                        <span v-else class="text-muted">Sin club</span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <strong>Categoría:</strong> 
                                        <span v-if="pilot.category">{{ pilot.category.name }}</span>
                                        <span v-else class="text-muted">Sin categoría</span>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <strong>Edad:</strong> {{ pilot.age || 'No especificada' }}
                                    </div>
                                    
                                    <div class="mb-3">
                                        <span :class="getStatusClass(pilot.status)">
                                            {{ getStatusLabel(pilot.status) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="card-footer">
                                    <div class="btn-group w-100" role="group">
                                        <router-link 
                                            :to="{ 
                                                name: 'pilots.show', 
                                                params: { id: pilot.id },
                                                query: fromClub ? { 
                                                    from: 'club', 
                                                    clubId: fromClubId 
                                                } : {}
                                            }" 
                                            class="btn btn-outline-primary btn-sm"
                                        >
                                            <i class="fas fa-eye"></i>
                                            Ver
                                        </router-link>
                                        <router-link 
                                            v-if="canEdit" 
                                            :to="{ name: 'pilots.edit', params: { id: pilot.id } }" 
                                            class="btn btn-outline-warning btn-sm"
                                        >
                                            <i class="fas fa-edit"></i>
                                            Editar
                                        </router-link>
                                        
                                        <!-- Botón de desactivar solo para pilotos activos -->
                                        <button 
                                            v-if="canDelete && pilot.status === 'active'" 
                                            @click="confirmDelete(pilot.id, pilot.first_name + ' ' + pilot.last_name)" 
                                            class="btn btn-outline-warning btn-sm"
                                        >
                                            <i class="fas fa-user-slash"></i>
                                            Desactivar
                                        </button>
                                        
                                        <!-- Botón de reactivar solo para pilotos inactivos -->
                                        <button 
                                            v-if="canDelete && pilot.status === 'inactive'" 
                                            @click="confirmReactivate(pilot.id, pilot.first_name + ' ' + pilot.last_name)" 
                                            class="btn btn-outline-success btn-sm"
                                        >
                                            <i class="fas fa-user-check"></i>
                                            Reactivar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- No pilots message -->
                    <div v-else class="text-center py-5">
                        <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No se encontraron pilotos</h4>
                        <p class="text-muted">No hay pilotos que coincidan con los filtros aplicados.</p>
                        <button @click="clearFilters" class="btn btn-outline-primary">
                            <i class="fas fa-filter mr-1"></i>
                            Limpiar Filtros
                        </button>
                    </div>
                </div>

                <!-- Vista de Lista -->
                <div v-else-if="viewMode === 'list'" id="list-view">
                    <div v-if="pilots.length > 0" class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Foto</th>
                                    <th>Nombre</th>
                                    <th>ID</th>
                                    <th>Club</th>
                                    <th>Categoría</th>
                                    <th>Edad</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="pilot in pilots" :key="pilot.id">
                                    <td>
                                        <img 
                                            v-if="pilot.photo" 
                                            :src="'/storage/' + pilot.photo" 
                                            :alt="'Foto de ' + pilot.first_name + ' ' + pilot.last_name"
                                            class="img-circle" 
                                            style="width: 40px; height: 40px; object-fit: cover;"
                                        >
                                        <div 
                                            v-else
                                            class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                            style="width: 40px; height: 40px;"
                                        >
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                    </td>
                                    <td>{{ pilot.first_name }} {{ pilot.last_name }}</td>
                                    <td>{{ pilot.id }}</td>
                                    <td>
                                        <span v-if="pilot.club">{{ pilot.club.name }}</span>
                                        <span v-else class="text-muted">Sin club</span>
                                    </td>
                                    <td>
                                        <span v-if="pilot.category">{{ pilot.category.name }}</span>
                                        <span v-else class="text-muted">Sin categoría</span>
                                    </td>
                                    <td>{{ pilot.age || 'No especificada' }}</td>
                                    <td>
                                        <span :class="getStatusClass(pilot.status)">
                                            {{ getStatusLabel(pilot.status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <router-link 
                                                :to="{ 
                                                    name: 'pilots.show', 
                                                    params: { id: pilot.id },
                                                    query: fromClub ? { 
                                                        from: 'club', 
                                                        clubId: fromClubId 
                                                    } : {}
                                                }" 
                                                class="btn btn-outline-primary btn-sm"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </router-link>
                                            <router-link 
                                                v-if="canEdit" 
                                                :to="{ name: 'pilots.edit', params: { id: pilot.id } }" 
                                                class="btn btn-outline-warning btn-sm"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </router-link>
                                            
                                            <!-- Botón de desactivar solo para pilotos activos -->
                                            <button 
                                                v-if="canDelete && pilot.status === 'active'" 
                                                @click="confirmDelete(pilot.id, pilot.first_name + ' ' + pilot.last_name)" 
                                                class="btn btn-outline-warning btn-sm"
                                            >
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                            
                                            <!-- Botón de reactivar solo para pilotos inactivos -->
                                            <button 
                                                v-if="canDelete && pilot.status === 'inactive'" 
                                                @click="confirmReactivate(pilot.id, pilot.first_name + ' ' + pilot.last_name)" 
                                                class="btn btn-outline-success btn-sm"
                                            >
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- No pilots message -->
                    <div v-else class="text-center py-5">
                        <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No se encontraron pilotos</h4>
                        <p class="text-muted">No hay pilotos que coincidan con los filtros aplicados.</p>
                        <button @click="clearFilters" class="btn btn-outline-primary">
                            <i class="fas fa-filter mr-1"></i>
                            Limpiar Filtros
                        </button>
                    </div>
                </div>
            </div>
            
                    <!-- Pagination -->
                    <div v-if="pagination.total > 0" class="card-footer">
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
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import DataPagination from './DataPagination.vue';

export default {
    name: 'PilotManager',
    components: {
        DataPagination
    },
    data() {
        return {
            pilots: [],
            loading: false,
            viewMode: 'cards', // 'cards' or 'list'
            searchTimeout: null,
            isSearchFocused: false,
            filters: {
                search: '',
                status: '',
                category: '',
                club: ''
            },
            pagination: {
                current_page: 1,
                last_page: 1,
                total: 0,
                per_page: 12,
                from: 0,
                to: 0
            },
            categoryOptions: [
                { value: '', label: 'Todas las categorías' }
            ],
            clubOptions: [
                { value: '', label: 'Todos los clubes' }
            ],
            routes: {
                api: '/api/pilots',
                delete: '/api/pilots/{id}',
                export: '/api/pilots/export'
            },
            permissions: {
                canCreate: window.Laravel?.user?.authenticated || false,
                canEdit: window.Laravel?.user?.authenticated || false,
                canDelete: window.Laravel?.user?.authenticated || false
            },
            searchTimeout: null
        };
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
        },
        // Detectar si viene desde un club
        fromClub() {
            return this.$route.query.from === 'club' && this.$route.query.clubId;
        },
        fromClubId() {
            return this.$route.query.clubId;
        }
    },
    mounted() {
        console.log('PilotManager mounted, loading pilots...');
        this.loadPilots();
        this.loadCategories();
        this.loadClubs();
    },
    beforeDestroy() {
        // Limpiar timeout al destruir el componente
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
    },
    methods: {
        async loadPilots() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                params.append('page', this.pagination.current_page);
                
                if (this.filters.search) {
                    params.append('search', this.filters.search);
                }
                
                if (this.filters.status) {
                    params.append('status', this.filters.status);
                }

                if (this.filters.category) {
                    params.append('category', this.filters.category);
                }

                if (this.filters.club) {
                    params.append('club', this.filters.club);
                }

                const url = `${this.routes.api}?${params.toString()}`;
                console.log('Fetching pilots from:', url);
                
                const response = await fetch(url);
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                console.log('API Response:', data);
                
                if (data.data) {
                    this.pilots = data.data;
                    this.pagination = {
                        current_page: data.current_page || 1,
                        last_page: data.last_page || 1,
                        total: data.total || 0,
                        per_page: data.per_page || 12,
                        from: data.from || 0,
                        to: data.to || 0
                    };
                } else {
                    // If data structure is different, adapt accordingly
                    this.pilots = Array.isArray(data) ? data : [];
                    this.pagination.total = this.pilots.length;
                }
                
                console.log('Loaded pilots:', this.pilots.length);
                
            } catch (error) {
                console.error('Error loading pilots:', error);
                this.showNotification('Error al cargar los pilotos: ' + error.message, 'error');
                this.pilots = [];
            } finally {
                this.loading = false;
            }
        },

        async loadCategories() {
            try {
                const response = await fetch('http://intranet.ambmx.com/api/categories');
                if (response.ok) {
                    const data = await response.json();
                    const categories = data.data || data;
                    this.categoryOptions = [
                        { value: '', label: 'Todas las categorías' },
                        ...categories.map(category => ({
                            value: category.id,
                            label: category.name
                        }))
                    ];
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        },

        async loadClubs() {
            try {
                const response = await fetch('http://intranet.ambmx.com/api/clubs');
                if (response.ok) {
                    const data = await response.json();
                    const clubs = data.data || data;
                    this.clubOptions = [
                        { value: '', label: 'Todos los clubes' },
                        ...clubs.map(club => ({
                            value: club.id,
                            label: club.name
                        }))
                    ];
                }
            } catch (error) {
                console.error('Error loading clubs:', error);
            }
        },

        getStatusClass(status) {
            const classes = {
                'active': 'badge badge-success',
                'inactive': 'badge badge-secondary',
                'suspended': 'badge badge-danger'
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

        async confirmDelete(pilotId, pilotName) {
            if (confirm(`¿Estás seguro de desactivar al piloto "${pilotName}"? El piloto quedará inactivo pero no se eliminará de la base de datos.`)) {
                await this.deletePilot(pilotId);
            }
        },

        async deletePilot(pilotId) {
            try {
                const response = await fetch(this.routes.delete.replace('{id}', pilotId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                });

                if (response.ok) {
                    this.showNotification('Piloto desactivado exitosamente', 'success');
                    this.loadPilots();
                } else {
                    throw new Error('Error al desactivar el piloto');
                }
            } catch (error) {
                console.error('Error deleting pilot:', error);
                this.showNotification('Error al eliminar el piloto', 'error');
            }
        },

        async exportPilots() {
            try {
                const response = await fetch(this.routes.export);
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = 'pilotos.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    this.showNotification('Exportación completada', 'success');
                } else {
                    throw new Error('Error al exportar');
                }
            } catch (error) {
                console.error('Error exporting pilots:', error);
                this.showNotification('Error al exportar pilotos', 'error');
            }
        },

        handleSearch() {
            // Limpiar el timeout anterior si existe
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            
            // Establecer un nuevo timeout para hacer la búsqueda después de 500ms
            this.searchTimeout = setTimeout(() => {
                this.pagination.current_page = 1;
                this.loadPilots().then(() => {
                    // Preservar el foco solo si el usuario estaba escribiendo en el campo
                    this.$nextTick(() => {
                        if (this.isSearchFocused && this.$refs.searchInput) {
                            this.$refs.searchInput.focus();
                        }
                    });
                });
            }, 500);
        },

        handleStatusChange() {
            this.pagination.current_page = 1;
            this.loadPilots();
        },

        handleCategoryChange() {
            this.pagination.current_page = 1;
            this.loadPilots();
        },

        handleClubChange() {
            this.pagination.current_page = 1;
            this.loadPilots();
        },

        changePage(page) {
            this.pagination.current_page = page;
            this.loadPilots();
        },

        clearFilters() {
            // Limpiar timeout de búsqueda
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
            
            this.isSearchFocused = false;
            this.filters.search = '';
            this.filters.status = '';
            this.filters.category = '';
            this.filters.club = '';
            this.pagination.current_page = 1;
            this.loadPilots();
        },

        showNotification(message, type = 'info') {
            // Simple notification system
            console.log(`${type.toUpperCase()}: ${message}`);
            // You can implement a proper notification system here
            if (type === 'error') {
                alert('Error: ' + message);
            }
        },

        async confirmReactivate(pilotId, pilotName) {
            if (confirm(`¿Estás seguro de reactivar al piloto "${pilotName}"? El piloto volverá a estar activo.`)) {
                await this.reactivatePilot(pilotId);
            }
        },

        async reactivatePilot(pilotId) {
            try {
                const response = await fetch(`/api/pilots/${pilotId}/reactivate`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                });

                if (response.ok) {
                    this.showNotification('Piloto reactivado exitosamente', 'success');
                    this.loadPilots();
                } else {
                    throw new Error('Error al reactivar el piloto');
                }
            } catch (error) {
                console.error('Error reactivating pilot:', error);
                this.showNotification('Error al reactivar el piloto', 'error');
            }
        },
    },

    beforeDestroy() {
        // Limpiar el timeout cuando el componente se destruya
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
    }
};
</script>

<style scoped>
.pilot-manager {
    max-width: 100%;
}

.card {
    border: none;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group .btn {
    border-radius: 0.25rem;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}

@media (max-width: 768px) {
    .card-tools {
        flex-direction: column;
        align-items: stretch;
    }
    
    .card-tools .btn-group {
        margin-bottom: 0.5rem;
        margin-right: 0;
    }
}
</style>
