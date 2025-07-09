<template>
    <div class="club-manager">
        <!-- Header -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    Lista de Clubes BMX
                </h3>
                <div class="card-tools">
                    <!-- Toggle de vista -->
                    <div class="btn-group" role="group">
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
                    
                    <button v-if="canCreate" @click="exportClubs" class="btn btn-success btn-sm mr-2">
                        <i class="fas fa-file-excel"></i> Exportar
                    </button>
                    
                    <a v-if="canCreate" :href="createRoute" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Club
                    </a>
                </div>
            </div>
            
            <!-- Filtros -->
            <div class="card-header">
                <SearchFilter
                    v-model="filters.search"
                    :status-options="statusOptions"
                    :selected-status="filters.status"
                    @search="handleSearch"
                    @status-change="handleStatusChange"
                    @clear="clearFilters"
                    placeholder="Buscar clubes..."
                />
            </div>

            <div class="card-body">
                <!-- Loading Spinner -->
                <LoadingSpinner v-if="loading" />

                <!-- Vista de Cards -->
                <div v-else-if="viewMode === 'cards'" id="cards-view">
                    <div v-if="clubs.length > 0" class="row">
                        <div v-for="club in clubs" :key="club.id" class="col-lg-4 col-md-6 col-sm-12 mb-4">
                            <div class="card card-widget widget-user club-card">
                                <!-- Header with background -->
                                <div class="widget-user-header text-white">
                                    <h3 class="widget-user-username text-right">{{ club.name }}</h3>
                                    <h5 class="widget-user-desc text-right">
                                        {{ club.city }}{{ club.city && club.state ? ', ' : '' }}{{ club.state }}
                                    </h5>
                                </div>
                                
                                <!-- Club logo -->
                                <div class="widget-user-image">
                                    <img 
                                        v-if="club.logo" 
                                        class="img-circle elevation-2" 
                                        :src="'/storage/' + club.logo" 
                                        :alt="'Logo de ' + club.name"
                                    >
                                    <div 
                                        v-else
                                        class="img-circle elevation-2 bg-secondary d-flex align-items-center justify-content-center" 
                                        style="width: 90px; height: 90px;"
                                    >
                                        <i class="fas fa-users fa-2x text-white"></i>
                                    </div>
                                </div>
                                
                                <!-- Card content -->
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-sm-4 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header text-success">
                                                    {{ club.pilots_count || 0 }}
                                                </h5>
                                                <span class="description-text">PILOTOS</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 border-right">
                                            <div class="description-block">
                                                <h5 class="description-header">
                                                    <StatusBadge :status="club.status" :type="'club'" />
                                                </h5>
                                                <span class="description-text">ESTADO</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="description-block">
                                                <h5 class="description-header text-info">
                                                    {{ club.founded_year || 'N/A' }}
                                                </h5>
                                                <span class="description-text">FUNDADO</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Additional info row -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <p v-if="club.website" class="text-muted mb-2">
                                                <i class="fas fa-globe mr-1"></i>
                                                <a :href="club.website" target="_blank" class="text-muted">
                                                    {{ truncateUrl(club.website, 30) }}
                                                </a>
                                            </p>
                                            
                                            <p v-if="club.country" class="text-muted mb-2">
                                                <i class="fas fa-flag mr-1"></i>
                                                {{ club.country }}
                                            </p>
                                            
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-user-friends mr-1"></i>
                                                {{ club.active_pilots_count || 0 }} pilotos activos
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Action buttons -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="btn-group d-flex" role="group">
                                                <router-link :to="{ name: 'clubs.show', params: { id: club.id } }" class="btn btn-info btn-sm flex-fill">
                                                    <i class="fas fa-eye mr-1"></i>
                                                    Ver
                                                </router-link>
                                                <router-link v-if="canEdit" :to="{ name: 'clubs.edit', params: { id: club.id } }" class="btn btn-warning btn-sm flex-fill">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Editar
                                                </router-link>
                                                <button 
                                                    v-if="canDelete && club.status === 'active'" 
                                                    type="button" 
                                                    class="btn btn-danger btn-sm flex-fill"
                                                    @click="confirmDeactivate(club.id, club.name)"
                                                >
                                                    <i class="fas fa-ban mr-1"></i>
                                                    Desactivar
                                                </button>
                                                <button 
                                                    v-if="canDelete && club.status === 'inactive'" 
                                                    type="button" 
                                                    class="btn btn-success btn-sm flex-fill"
                                                    @click="confirmReactivate(club.id, club.name)"
                                                >
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Reactivar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="row">
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No hay clubes registrados</h4>
                                <p v-if="canCreate" class="text-muted">Comienza agregando el primer club BMX</p>
                                <p v-else class="text-muted">Aún no hay clubes en el sistema</p>
                                <router-link v-if="canCreate" :to="{ name: 'clubs.create' }" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus mr-2"></i> Crear Primer Club
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vista de Lista -->
                <div v-else-if="viewMode === 'list'" id="list-view">
                    <div v-if="clubs.length > 0" class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>Logo</th>
                                    <th>Nombre</th>
                                    <th>Ciudad/Estado</th>
                                    <th>Pilotos</th>
                                    <th>Estado</th>
                                    <th>Fundación</th>
                                    <th>Website</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="club in clubs" :key="club.id">
                                    <td>
                                        <img 
                                            v-if="club.logo" 
                                            :src="'/storage/' + club.logo" 
                                            :alt="'Logo ' + club.name" 
                                            class="img-circle" 
                                            style="width: 40px; height: 40px;"
                                        >
                                        <div 
                                            v-else
                                            class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                            style="width: 40px; height: 40px;"
                                        >
                                            <i class="fas fa-users text-white"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ club.name }}</strong>
                                        <br v-if="club.description">
                                        <small v-if="club.description" class="text-muted">{{ truncateText(club.description, 50) }}</small>
                                    </td>
                                    <td>
                                        {{ club.city }}{{ club.city && club.state ? ', ' : '' }}{{ club.state }}
                                        <br v-if="club.country">
                                        <small v-if="club.country" class="text-muted">{{ club.country }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ club.active_pilots_count || 0 }}</span>
                                        <small class="text-muted">activos</small>
                                    </td>
                                    <td>
                                        <StatusBadge :status="club.status" :type="'club'" />
                                    </td>
                                    <td>{{ club.founded_year || 'N/A' }}</td>
                                    <td>
                                        <a 
                                            v-if="club.website" 
                                            :href="club.website" 
                                            target="_blank" 
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <span v-else class="text-muted">-</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <router-link :to="{ name: 'clubs.show', params: { id: club.id } }" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </router-link>
                                            <router-link v-if="canEdit" :to="{ name: 'clubs.edit', params: { id: club.id } }" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </router-link>
                                            <button 
                                                v-if="canDelete && club.status === 'active'" 
                                                type="button" 
                                                class="btn btn-sm btn-danger" 
                                                @click="confirmDeactivate(club.id, club.name)"
                                                title="Desactivar club"
                                            >
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            <button 
                                                v-if="canDelete && club.status === 'inactive'" 
                                                type="button" 
                                                class="btn btn-sm btn-success" 
                                                @click="confirmReactivate(club.id, club.name)"
                                                title="Reactivar club"
                                            >
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay clubes registrados</h4>
                        <p v-if="canCreate" class="text-muted">Comienza agregando el primer club BMX</p>
                        <p v-else class="text-muted">Aún no hay clubes en el sistema</p>
                        <router-link v-if="canCreate" :to="{ name: 'clubs.create' }" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus mr-2"></i> Crear Primer Club
                        </router-link>
                    </div>
                </div>
            </div>
            
            <!-- Paginación -->
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
</template>

<script>
export default {
    name: 'ClubManager',
    data() {
        return {
            clubs: [],
            loading: true,
            viewMode: 'cards', // 'cards' o 'list'
            filters: {
                search: '',
                status: ''
            },
            pagination: {
                current_page: 1,
                last_page: 1,
                total: 0,
                per_page: 10,
                from: 0,
                to: 0
            },
            statusOptions: [
                { value: '', label: 'Todos los estados' },
                { value: 'active', label: 'Activos' },
                { value: 'inactive', label: 'Inactivos' },
                { value: 'suspended', label: 'Suspendidos' }
            ],
            routes: {
                api: 'http://intranet.ambmx.com/api/clubs',
                show: 'http://intranet.ambmx.com/api/clubs/{id}',
                edit: '/gestionar/clubes/{id}/edit',
                create: '/gestionar/clubes/create',
                delete: 'http://intranet.ambmx.com/api/clubs/{id}',
                export: 'http://intranet.ambmx.com/api/clubs/export'
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
        console.log('ClubManager mounted, checking for initial data...');
        
        // Check if we have initial data from server
        if (window.Laravel && window.Laravel.initialData && window.Laravel.initialData.page === 'clubs-list') {
            console.log('Using initial clubs data from server:', window.Laravel.initialData);
            const data = window.Laravel.initialData.clubs;
            
            if (data.data) {
                this.clubs = data.data;
                this.pagination = {
                    current_page: data.current_page || 1,
                    last_page: data.last_page || 1,
                    total: data.total || 0,
                    per_page: data.per_page || 10,
                    from: data.from || 0,
                    to: data.to || 0
                };
            } else {
                this.clubs = Array.isArray(data) ? data : [];
                this.pagination.total = this.clubs.length;
            }
            
            this.loading = false;
            console.log('Loaded initial clubs:', this.clubs.length);
        } else {
            console.log('No initial data, loading from API...');
            this.loadClubs();
        }
        
        this.loadViewMode();
    },
    methods: {
        async loadClubs() {
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

                const url = `${this.routes.api}?${params.toString()}`;
                
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error('Error al cargar los clubes');
                }

                const data = await response.json();
                
                this.clubs = data.data;
                this.pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    total: data.total,
                    per_page: data.per_page,
                    from: data.from,
                    to: data.to
                };
                
            } catch (error) {
                console.error('Error loading clubs:', error);
                this.showNotification('Error al cargar los clubes', 'error');
            } finally {
                this.loading = false;
            }
        },

        handleSearch(searchTerm) {
            this.filters.search = searchTerm;
            this.pagination.current_page = 1;
            this.loadClubs();
        },

        handleStatusChange(status) {
            this.filters.status = status;
            this.pagination.current_page = 1;
            this.loadClubs();
        },

        clearFilters() {
            this.filters.search = '';
            this.filters.status = '';
            this.pagination.current_page = 1;
            this.loadClubs();
        },

        changePage(page) {
            this.pagination.current_page = page;
            this.loadClubs();
        },

        loadViewMode() {
            const savedView = localStorage.getItem('clubs_view');
            if (savedView && ['cards', 'list'].includes(savedView)) {
                this.viewMode = savedView;
            }
        },

        saveViewMode() {
            localStorage.setItem('clubs_view', this.viewMode);
        },

        getShowRoute(clubId) {
            return this.routes.show.replace('{id}', clubId);
        },

        getEditRoute(clubId) {
            return this.routes.edit.replace('{id}', clubId);
        },

        async confirmDeactivate(clubId, clubName) {
            if (confirm(`¿Estás seguro de desactivar el club "${clubName}"? El club ya no será visible para el público pero conservará todos sus datos.`)) {
                await this.deactivateClub(clubId);
            }
        },

        async confirmReactivate(clubId, clubName) {
            if (confirm(`¿Estás seguro de reactivar el club "${clubName}"? El club volverá a ser visible para el público.`)) {
                await this.reactivateClub(clubId);
            }
        },

        async deactivateClub(clubId) {
            try {
                const response = await fetch(this.routes.delete.replace('{id}', clubId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al desactivar el club');
                }

                this.showNotification(data.message || 'Club desactivado exitosamente', 'success');
                this.loadClubs();
            } catch (error) {
                console.error('Error deactivating club:', error);
                this.showNotification(error.message || 'Error al desactivar el club', 'error');
            }
        },

        async reactivateClub(clubId) {
            try {
                const response = await fetch(`/api/clubs/${clubId}/reactivate`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al reactivar el club');
                }

                this.showNotification(data.message || 'Club reactivado exitosamente', 'success');
                this.loadClubs();
            } catch (error) {
                console.error('Error reactivating club:', error);
                this.showNotification(error.message || 'Error al reactivar el club', 'error');
            }
        },

        async exportClubs() {
            try {
                const params = new URLSearchParams({
                    search: this.filters.search,
                    status: this.filters.status
                });

                const response = await fetch(`${this.routes.export}?${params}`);
                
                if (!response.ok) {
                    throw new Error('Error al exportar los clubes');
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `clubes_bmx_${new Date().toISOString().split('T')[0]}.xlsx`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                this.showNotification('Clubes exportados exitosamente', 'success');
            } catch (error) {
                console.error('Error exporting clubs:', error);
                this.showNotification('Error al exportar los clubes', 'error');
            }
        },

        truncateText(text, length) {
            if (!text) return '';
            return text.length > length ? text.substring(0, length) + '...' : text;
        },

        truncateUrl(url, length) {
            if (!url) return '';
            return url.length > length ? url.substring(0, length) + '...' : url;
        },

        showNotification(message, type) {
            // Emit event for notification system
            this.$root.$emit('show-notification', { message, type });
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
.club-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.club-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.widget-user-header {
    border-radius: 0.375rem 0.375rem 0 0;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.description-header {
    font-weight: 700;
    margin-bottom: 5px;
}

.description-text {
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.border-right:last-child {
    border-right: none !important;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin: 0 1px;
}

.btn-group.d-flex .btn {
    flex: 1;
}

.btn.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa;
}

.table-hover tbody tr:hover {
    background-color: rgba(40, 167, 69, 0.05);
}

.widget-user-image > div {
    margin: 0 auto;
    position: relative;
    top: -45px;
}

.widget-user-image img {
    width: 90px;
    height: 90px;
    border: 3px solid #fff;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem;
        border-radius: 0.25rem !important;
    }
}

@media (max-width: 576px) {
    .border-right {
        border-right: none !important;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
    }
    
    .border-right:last-child {
        border-bottom: none !important;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .btn-group.d-flex {
        flex-direction: column;
    }
    
    .btn-group.d-flex .btn {
        margin-bottom: 0.25rem;
        border-radius: 0.25rem !important;
    }
}
</style>
