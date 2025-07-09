<template>
  <div class="container-fluid">
    <!-- Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>
              Jornadas
              <span v-if="selectedChampionship"> - {{ selectedChampionship.name }} ({{ selectedChampionship.year }})</span>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="/admin/championships">Campeonatos</a></li>
              <li v-if="selectedChampionship" class="breadcrumb-item">
                <a :href="`/admin/championships/${selectedChampionship.id}`">{{ selectedChampionship.name }}</a>
              </li>
              <li class="breadcrumb-item active">Jornadas</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Lista de Jornadas</h3>
                <div class="card-tools">
                  <!-- Toggle de vista -->
                  <div class="btn-group mr-2" role="group">
                    <button 
                      type="button" 
                      class="btn btn-outline-primary btn-sm"
                      :class="{ active: viewMode === 'cards' }"
                      @click="viewMode = 'cards'"
                    >
                      <i class="fas fa-th-large"></i> Cards
                    </button>
                    <button 
                      type="button" 
                      class="btn btn-outline-primary btn-sm"
                      :class="{ active: viewMode === 'list' }"
                      @click="viewMode = 'list'"
                    >
                      <i class="fas fa-list"></i> Lista
                    </button>
                  </div>
                  
                  <button v-if="championshipFilter" 
                          :href="`/admin/matchdays/create?championship=${championshipFilter}`" 
                          class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nueva Jornada
                  </button>
                  <a v-else href="/admin/matchdays/create" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nueva Jornada
                  </a>
                </div>
              </div>
              
              <div class="card-body">
                <!-- Notificaciones -->
                <notification-system ref="notifications"></notification-system>

                <!-- Estadísticas -->
                <div class="row mb-4" v-if="!loading">
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <h3>{{ stats.total }}</h3>
                        <p>Total Jornadas</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-calendar"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <h3>{{ stats.completed }}</h3>
                        <p>Completadas</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-check-circle"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                      <div class="inner">
                        <h3>{{ stats.scheduled }}</h3>
                        <p>Programadas</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-clock"></i>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                      <div class="inner">
                        <h3>{{ stats.totalParticipants }}</h3>
                        <p>Total Participantes</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-users"></i>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Filtros -->
                <search-filter
                  :filters="filters"
                  @filter-changed="onFilterChanged"
                  @search="onSearch"
                  @export="onExport"
                  @clear="onClearFilters"
                >
                  <template #custom-filters>
                    <div class="col-md-4">
                      <label>Filtrar por Campeonato:</label>
                      <select class="form-control" v-model="championshipFilter" @change="onFilterChanged">
                        <option value="">Todos los campeonatos</option>
                        <option v-for="championship in championships" 
                                :key="championship.id" 
                                :value="championship.id">
                          {{ championship.name }} ({{ championship.year }})
                        </option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label>Estado:</label>
                      <select class="form-control" v-model="statusFilter" @change="onFilterChanged">
                        <option value="">Todos los estados</option>
                        <option value="scheduled">Programada</option>
                        <option value="ongoing">En curso</option>
                        <option value="completed">Completada</option>
                        <option value="cancelled">Cancelada</option>
                        <option value="postponed">Postergada</option>
                      </select>
                    </div>
                  </template>
                </search-filter>

                <!-- Loading -->
                <loading-spinner v-if="loading"></loading-spinner>

                <!-- Vista Cards -->
                <div v-if="viewMode === 'cards' && !loading" class="row">
                  <div v-for="matchday in paginatedMatchdays" 
                       :key="matchday.id" 
                       class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card h-100 matchday-card" 
                         :class="getCardClass(matchday.status)">
                      <div class="card-header" :class="getHeaderClass(matchday.status)">
                        <h5 class="card-title text-white mb-0">
                          <i class="fas fa-flag-checkered"></i>
                          Jornada {{ matchday.number }}
                        </h5>
                        <div class="card-tools">
                          <status-badge :status="matchday.status" :type="'matchday'"></status-badge>
                        </div>
                      </div>
                      <div class="card-body d-flex flex-column">
                        <div class="matchday-info mb-3">
                          <div class="info-item mb-2">
                            <i class="fas fa-trophy text-warning"></i>
                            <strong>Campeonato:</strong>
                            <div class="text-muted">
                              <a :href="`/admin/championships/${matchday.championship.id}`" 
                                 class="text-primary">
                                {{ matchday.championship.name }}
                              </a>
                              <small class="text-muted">({{ matchday.championship.year }})</small>
                            </div>
                          </div>
                          
                          <div class="info-item mb-2">
                            <i class="fas fa-calendar text-info"></i>
                            <strong>Fecha:</strong>
                            <div class="text-muted">{{ formatDate(matchday.date) || 'Por definir' }}</div>
                          </div>
                          
                          <div v-if="matchday.start_time" class="info-item mb-2">
                            <i class="fas fa-clock text-success"></i>
                            <strong>Hora:</strong>
                            <div class="text-muted">{{ matchday.start_time }}</div>
                          </div>
                          
                          <div class="info-item mb-2">
                            <i class="fas fa-users text-info"></i>
                            <strong>Participantes:</strong>
                            <div class="text-muted">
                              <span class="badge badge-info badge-lg">
                                {{ matchday.participants_count }} inscritos
                              </span>
                            </div>
                          </div>
                          
                          <div v-if="matchday.venue" class="info-item mb-2">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            <strong>Lugar:</strong>
                            <div class="text-muted">{{ matchday.venue }}</div>
                          </div>
                          
                          <div class="info-item mb-3">
                            <i class="fas fa-building text-secondary"></i>
                            <strong>Organizador:</strong>
                            <div class="text-muted">
                              <span v-if="matchday.organizer_club" class="badge badge-info">
                                {{ matchday.organizer_club.name }}
                              </span>
                              <span v-else class="badge badge-success">AMBMX</span>
                            </div>
                          </div>
                        </div>
                        
                        <div class="mt-auto">
                          <div class="btn-group w-100" role="group">
                            <a :href="`/admin/matchdays/${matchday.id}`" 
                               class="btn btn-info btn-sm" title="Ver">
                              <i class="fas fa-eye"></i>
                            </a>
                            <a :href="`/admin/matchdays/${matchday.id}/participants`" 
                               class="btn btn-outline-info btn-sm" title="Participantes">
                              <i class="fas fa-users"></i> {{ matchday.participants_count }}
                            </a>
                            <a :href="`/admin/matchdays/${matchday.id}/payments`" 
                               class="btn btn-outline-success btn-sm" title="Pagos">
                              <i class="fas fa-money-check-alt"></i>
                            </a>
                            <a :href="`/admin/matchdays/${matchday.id}/edit`" 
                               class="btn btn-warning btn-sm" title="Editar">
                              <i class="fas fa-edit"></i>
                            </a>
                            <button @click="confirmDelete(matchday)" 
                                    class="btn btn-danger btn-sm" title="Eliminar">
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Vista Lista -->
                <data-table
                  v-if="viewMode === 'list' && !loading"
                  :columns="tableColumns"
                  :data="paginatedMatchdays"
                  :sortBy="sortBy"
                  :sortDirection="sortDirection"
                  @sort="onSort"
                  @action="onTableAction"
                />

                <!-- Estado vacío -->
                <div v-if="!loading && matchdays.length === 0" class="text-center py-4">
                  <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                  <h4 class="text-muted">No hay jornadas registradas</h4>
                  <p class="text-muted">Comience creando la primera jornada</p>
                  <a v-if="championshipFilter" 
                     :href="`/admin/matchdays/create?championship=${championshipFilter}`" 
                     class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Jornada
                  </a>
                  <a v-else href="/admin/matchdays/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Jornada
                  </a>
                </div>

                <!-- Paginación -->
                <data-pagination
                  v-if="!loading && matchdays.length > 0"
                  :current-page="currentPage"
                  :per-page="perPage"
                  :total="filteredMatchdays.length"
                  @page-changed="onPageChanged"
                  @per-page-changed="onPerPageChanged"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirmar Eliminación</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p v-if="matchdayToDelete">
              ¿Está seguro de eliminar la Jornada {{ matchdayToDelete.number }}?
            </p>
            <p class="text-warning">
              <i class="fas fa-exclamation-triangle"></i>
              Esta acción no se puede deshacer.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" @click="deleteMatchday" :disabled="deleting">
              <i v-if="deleting" class="fas fa-spinner fa-spin"></i>
              {{ deleting ? 'Eliminando...' : 'Eliminar' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MatchdayManager',
  data() {
    return {
      // Datos principales
      matchdays: [],
      championships: [],
      loading: false,
      deleting: false,
      
      // Filtros y búsqueda
      searchTerm: '',
      championshipFilter: '',
      statusFilter: '',
      currentPage: 1,
      perPage: 12,
      sortBy: 'date',
      sortDirection: 'desc',
      
      // Vista
      viewMode: 'cards', // 'cards' o 'list'
      
      // Modal de eliminación
      matchdayToDelete: null,
      
      // Estadísticas
      stats: {
        total: 0,
        completed: 0,
        scheduled: 0,
        totalParticipants: 0
      },
      
      // Configuración de filtros
      filters: [
        { key: 'search', label: 'Buscar jornadas...', type: 'text' }
      ],
      
      // Configuración de tabla
      tableColumns: [
        { key: 'number', label: 'Nº', sortable: true },
        { key: 'championship', label: 'Campeonato', sortable: true },
        { key: 'date', label: 'Fecha', sortable: true },
        { key: 'venue', label: 'Lugar', sortable: false },
        { key: 'participants_count', label: 'Participantes', sortable: true },
        { key: 'status', label: 'Estado', sortable: true },
        { key: 'actions', label: 'Acciones', sortable: false }
      ]
    }
  },
  computed: {
    filteredMatchdays() {
      let filtered = [...this.matchdays];
      
      // Filtro por búsqueda
      if (this.searchTerm) {
        const search = this.searchTerm.toLowerCase();
        filtered = filtered.filter(matchday => 
          matchday.championship.name.toLowerCase().includes(search) ||
          matchday.venue.toLowerCase().includes(search) ||
          matchday.number.toString().includes(search)
        );
      }
      
      // Filtro por campeonato
      if (this.championshipFilter) {
        filtered = filtered.filter(matchday => 
          matchday.championship_id == this.championshipFilter
        );
      }
      
      // Filtro por estado
      if (this.statusFilter) {
        filtered = filtered.filter(matchday => 
          matchday.status === this.statusFilter
        );
      }
      
      // Ordenamiento
      filtered.sort((a, b) => {
        let aVal = this.getSortValue(a, this.sortBy);
        let bVal = this.getSortValue(b, this.sortBy);
        
        if (this.sortDirection === 'asc') {
          return aVal > bVal ? 1 : -1;
        } else {
          return aVal < bVal ? 1 : -1;
        }
      });
      
      return filtered;
    },
    
    paginatedMatchdays() {
      const start = (this.currentPage - 1) * this.perPage;
      const end = start + this.perPage;
      return this.filteredMatchdays.slice(start, end);
    },
    
    selectedChampionship() {
      if (!this.championshipFilter) return null;
      return this.championships.find(c => c.id == this.championshipFilter);
    }
  },
  
  mounted() {
    console.log('MatchdayManager mounted, checking for initial data...');
    
    this.loadChampionships();
    
    // Obtener filtros de URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('championship')) {
      this.championshipFilter = urlParams.get('championship');
    }
    if (urlParams.get('status')) {
      this.statusFilter = urlParams.get('status');
    }
    
    // Check if we have initial data from server
    if (window.Laravel && window.Laravel.initialData && window.Laravel.initialData.page === 'matchdays-list') {
      console.log('Using initial matchdays data from server:', window.Laravel.initialData);
      const data = window.Laravel.initialData.matchdays;
      
      if (data.data) {
        this.matchdays = data.data;
        this.pagination = {
          current_page: data.current_page || 1,
          last_page: data.last_page || 1,
          total: data.total || 0,
          per_page: data.per_page || 10,
          from: data.from || 0,
          to: data.to || 0
        };
      } else {
        this.matchdays = Array.isArray(data) ? data : [];
        this.pagination.total = this.matchdays.length;
      }
      
      this.loading = false;
    } else {
      console.log('No initial data found, loading from API...');
      this.loadMatchdays();
    }
  },
  
  methods: {
    async loadMatchdays() {
      this.loading = true;
      try {
        const response = await fetch('/api/matchdays');
        if (response.ok) {
          const data = await response.json();
          this.matchdays = data.data;
          this.calculateStats();
        } else {
          this.$refs.notifications.show('Error al cargar las jornadas', 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        this.$refs.notifications.show('Error de conexión', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    async loadChampionships() {
      try {
        const response = await fetch('/api/championships');
        if (response.ok) {
          const data = await response.json();
          this.championships = data.data;
        }
      } catch (error) {
        console.error('Error loading championships:', error);
      }
    },
    
    calculateStats() {
      this.stats = {
        total: this.matchdays.length,
        completed: this.matchdays.filter(m => m.status === 'completed').length,
        scheduled: this.matchdays.filter(m => m.status === 'scheduled').length,
        totalParticipants: this.matchdays.reduce((sum, m) => sum + (m.participants_count || 0), 0)
      };
    },
    
    formatDate(date) {
      if (!date) return null;
      return new Date(date).toLocaleDateString('es-ES');
    },
    
    getCardClass(status) {
      const classes = {
        'completed': 'card-outline-success',
        'cancelled': 'card-outline-danger',
        'default': 'card-outline-primary'
      };
      return classes[status] || classes.default;
    },
    
    getHeaderClass(status) {
      const classes = {
        'completed': 'bg-gradient-success',
        'cancelled': 'bg-gradient-danger',
        'default': 'bg-gradient-primary'
      };
      return classes[status] || classes.default;
    },
    
    getSortValue(item, sortBy) {
      switch (sortBy) {
        case 'championship':
          return item.championship.name;
        case 'date':
          return item.date || '';
        default:
          return item[sortBy] || '';
      }
    },
    
    // Eventos de componentes
    onSearch(term) {
      this.searchTerm = term;
      this.currentPage = 1;
    },
    
    onFilterChanged() {
      this.currentPage = 1;
      this.updateURL();
    },
    
    onClearFilters() {
      this.searchTerm = '';
      this.championshipFilter = '';
      this.statusFilter = '';
      this.currentPage = 1;
      this.updateURL();
    },
    
    onSort({ column, direction }) {
      this.sortBy = column;
      this.sortDirection = direction;
    },
    
    onPageChanged(page) {
      this.currentPage = page;
    },
    
    onPerPageChanged(perPage) {
      this.perPage = perPage;
      this.currentPage = 1;
    },
    
    onTableAction({ action, item }) {
      switch (action) {
        case 'view':
          window.location.href = `/admin/matchdays/${item.id}`;
          break;
        case 'edit':
          window.location.href = `/admin/matchdays/${item.id}/edit`;
          break;
        case 'delete':
          this.confirmDelete(item);
          break;
        case 'participants':
          window.location.href = `/admin/matchdays/${item.id}/participants`;
          break;
        case 'payments':
          window.location.href = `/admin/matchdays/${item.id}/payments`;
          break;
      }
    },
    
    async onExport() {
      try {
        let url = '/api/matchdays/export?';
        const params = [];
        
        if (this.searchTerm) params.push(`search=${encodeURIComponent(this.searchTerm)}`);
        if (this.championshipFilter) params.push(`championship=${this.championshipFilter}`);
        if (this.statusFilter) params.push(`status=${this.statusFilter}`);
        
        url += params.join('&');
        
        window.open(url, '_blank');
        this.$refs.notifications.show('Exportación iniciada', 'success');
      } catch (error) {
        this.$refs.notifications.show('Error al exportar', 'error');
      }
    },
    
    confirmDelete(matchday) {
      this.matchdayToDelete = matchday;
      $('#deleteModal').modal('show');
    },
    
    async deleteMatchday() {
      if (!this.matchdayToDelete) return;
      
      this.deleting = true;
      try {
        const response = await fetch(`/api/matchdays/${this.matchdayToDelete.id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
          }
        });
        
        if (response.ok) {
          this.matchdays = this.matchdays.filter(m => m.id !== this.matchdayToDelete.id);
          this.calculateStats();
          this.$refs.notifications.show('Jornada eliminada correctamente', 'success');
          $('#deleteModal').modal('hide');
        } else {
          const error = await response.json();
          this.$refs.notifications.show(error.message || 'Error al eliminar', 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        this.$refs.notifications.show('Error de conexión', 'error');
      } finally {
        this.deleting = false;
        this.matchdayToDelete = null;
      }
    },
    
    updateURL() {
      const params = new URLSearchParams();
      if (this.championshipFilter) params.set('championship', this.championshipFilter);
      if (this.statusFilter) params.set('status', this.statusFilter);
      
      const newURL = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
      window.history.replaceState({}, '', newURL);
    }
  }
}
</script>

<style scoped>
.matchday-card {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  border: none;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.matchday-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 8px;
}

.info-item i {
  margin-top: 2px;
  width: 16px;
  flex-shrink: 0;
}

.badge-lg {
  padding: 0.5rem 0.75rem;
  font-size: 0.95rem;
  font-weight: 600;
}

.bg-gradient-primary {
  background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
}

.bg-gradient-success {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-gradient-danger {
  background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
}

@media (max-width: 768px) {
  .matchday-card {
    margin-bottom: 1.5rem;
  }
  
  .info-item {
    flex-direction: column;
    gap: 4px;
  }
  
  .info-item i {
    margin-top: 0;
  }
  
  .btn-group {
    flex-direction: column;
  }
  
  .btn-group .btn {
    border-radius: 0.25rem !important;
    margin-bottom: 0.25rem;
  }
  
  .btn-group .btn:last-child {
    margin-bottom: 0;
  }
}
</style>
