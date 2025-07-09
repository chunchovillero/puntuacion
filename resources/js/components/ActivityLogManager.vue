<template>
  <div class="activity-log-manager">
    <!-- Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">
              <i class="fas fa-history text-info"></i>
              Registro de Actividad
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <router-link to="/dashboard">Inicio</router-link>
              </li>
              <li class="breadcrumb-item active">Logs de Actividad</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Filters Card -->
        <div class="card card-secondary" :class="{ 'collapsed-card': !showFilters }">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-filter"></i>
              Filtros
            </h3>
            <div class="card-tools">
              <button 
                type="button" 
                class="btn btn-tool" 
                @click="showFilters = !showFilters"
              >
                <i :class="showFilters ? 'fas fa-minus' : 'fas fa-plus'"></i>
              </button>
            </div>
          </div>
          <div class="card-body" v-show="showFilters">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Usuario</label>
                  <select v-model="filters.user_id" class="form-control">
                    <option value="">Todos los usuarios</option>
                    <option 
                      v-for="user in users" 
                      :key="user.id" 
                      :value="user.id"
                    >
                      {{ user.name }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>Acción</label>
                  <select v-model="filters.action" class="form-control">
                    <option value="">Todas las acciones</option>
                    <option 
                      v-for="action in actions" 
                      :key="action" 
                      :value="action"
                    >
                      {{ capitalizeFirst(action) }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>Tipo de Modelo</label>
                  <select v-model="filters.model_type" class="form-control">
                    <option value="">Todos los tipos</option>
                    <option 
                      v-for="type in modelTypes" 
                      :key="type.value" 
                      :value="type.value"
                    >
                      {{ type.label }}
                    </option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>Fecha Desde</label>
                  <input 
                    type="date" 
                    v-model="filters.date_from" 
                    class="form-control"
                  >
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>Fecha Hasta</label>
                  <input 
                    type="date" 
                    v-model="filters.date_to" 
                    class="form-control"
                  >
                </div>
              </div>
              <div class="col-md-1">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <div class="btn-group-vertical d-block">
                    <button 
                      type="button" 
                      @click="applyFilters" 
                      class="btn btn-primary btn-sm"
                      :disabled="loading"
                    >
                      <i class="fas fa-search"></i>
                    </button>
                    <button 
                      type="button" 
                      @click="clearFilters" 
                      class="btn btn-secondary btn-sm"
                    >
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Logs List Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-list"></i>
              Actividad Reciente
              <small class="text-muted" v-if="pagination.total">({{ pagination.total }} registros)</small>
            </h3>
            <div class="card-tools">
              <button 
                @click="exportLogs" 
                class="btn btn-success btn-sm"
                :disabled="loading"
              >
                <i class="fas fa-download"></i> Exportar CSV
              </button>
            </div>
          </div>
          
          <div class="card-body p-0">
            <!-- Loading State -->
            <div v-if="loading" class="text-center p-4">
              <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
              <p class="text-muted mt-2">Cargando registros...</p>
            </div>

            <!-- Logs Table -->
            <div v-else-if="logs.length > 0" class="table-responsive">
              <table class="table table-sm table-hover">
                <thead>
                  <tr>
                    <th width="150">Fecha</th>
                    <th width="120">Usuario</th>
                    <th width="80">Acción</th>
                    <th width="100">Tipo</th>
                    <th>Descripción</th>
                    <th width="120">IP</th>
                    <th width="60">Ver</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="log in logs" :key="log.id">
                    <td>
                      <small>{{ formatDate(log.created_at) }}</small>
                    </td>
                    <td>
                      <div v-if="log.user" class="d-flex align-items-center">
                        <div 
                          class="user-avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mr-1"
                          style="width: 20px; height: 20px; font-size: 10px;"
                        >
                          {{ getUserInitial(log.user.name) }}
                        </div>
                        <small>{{ log.user.name }}</small>
                      </div>
                      <small v-else class="text-muted">Sistema</small>
                    </td>
                    <td>
                      <span :class="getActionBadgeClass(log.action)">
                        {{ capitalizeFirst(log.action) }}
                      </span>
                    </td>
                    <td>
                      <small>{{ getModelBaseName(log.model_type) }}</small>
                    </td>
                    <td>
                      <div 
                        class="text-truncate" 
                        style="max-width: 300px;" 
                        :title="log.description"
                      >
                        {{ log.description }}
                      </div>
                    </td>
                    <td>
                      <small class="text-muted">{{ log.ip_address }}</small>
                    </td>
                    <td>
                      <button 
                        @click="showLogDetails(log)" 
                        class="btn btn-info btn-xs" 
                        title="Ver detalles"
                      >
                        <i class="fas fa-eye"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center p-4">
              <i class="fas fa-history fa-3x text-muted mb-3"></i>
              <p class="text-muted">No se encontraron registros de actividad.</p>
            </div>
          </div>
          
          <!-- Pagination -->
          <div v-if="pagination.last_page > 1" class="card-footer">
            <nav>
              <ul class="pagination justify-content-center mb-0">
                <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                  <button 
                    class="page-link" 
                    @click="changePage(pagination.current_page - 1)"
                    :disabled="pagination.current_page === 1"
                  >
                    Anterior
                  </button>
                </li>
                
                <li 
                  v-for="page in getVisiblePages()"
                  :key="page"
                  class="page-item" 
                  :class="{ active: page === pagination.current_page }"
                >
                  <button class="page-link" @click="changePage(page)">
                    {{ page }}
                  </button>
                </li>
                
                <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                  <button 
                    class="page-link" 
                    @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page"
                  >
                    Siguiente
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </section>

    <!-- Log Details Modal -->
    <div 
      class="modal fade" 
      id="logDetailsModal" 
      tabindex="-1" 
      role="dialog"
      ref="logDetailsModal"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-info-circle"></i>
              Detalles del Registro de Actividad
            </h5>
            <button type="button" class="close" @click="closeLogDetails">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body" v-if="selectedLog">
            <div class="row">
              <div class="col-md-6">
                <strong>Fecha:</strong>
                <p>{{ formatDateTime(selectedLog.created_at) }}</p>
              </div>
              <div class="col-md-6">
                <strong>Usuario:</strong>
                <p>{{ selectedLog.user ? selectedLog.user.name : 'Sistema' }}</p>
              </div>
              <div class="col-md-6">
                <strong>Acción:</strong>
                <p>
                  <span :class="getActionBadgeClass(selectedLog.action)">
                    {{ capitalizeFirst(selectedLog.action) }}
                  </span>
                </p>
              </div>
              <div class="col-md-6">
                <strong>Tipo de Modelo:</strong>
                <p>{{ getModelBaseName(selectedLog.model_type) }}</p>
              </div>
              <div class="col-md-6">
                <strong>ID del Modelo:</strong>
                <p>{{ selectedLog.model_id }}</p>
              </div>
              <div class="col-md-6">
                <strong>IP:</strong>
                <p>{{ selectedLog.ip_address }}</p>
              </div>
              <div class="col-12">
                <strong>Descripción:</strong>
                <p>{{ selectedLog.description }}</p>
              </div>
              <div class="col-12" v-if="selectedLog.user_agent">
                <strong>User Agent:</strong>
                <p class="text-muted small">{{ selectedLog.user_agent }}</p>
              </div>
            </div>
            
            <!-- Old Values -->
            <div v-if="selectedLog.old_values && Object.keys(selectedLog.old_values).length > 0">
              <hr>
              <h6><i class="fas fa-history"></i> Valores Anteriores:</h6>
              <div class="bg-light p-3 rounded">
                <pre>{{ JSON.stringify(selectedLog.old_values, null, 2) }}</pre>
              </div>
            </div>
            
            <!-- New Values -->
            <div v-if="selectedLog.new_values && Object.keys(selectedLog.new_values).length > 0">
              <hr>
              <h6><i class="fas fa-plus-circle"></i> Valores Nuevos:</h6>
              <div class="bg-light p-3 rounded">
                <pre>{{ JSON.stringify(selectedLog.new_values, null, 2) }}</pre>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeLogDetails">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ActivityLogManager',
  
  data() {
    return {
      logs: [],
      users: [],
      actions: [],
      modelTypes: [],
      loading: false,
      showFilters: false,
      selectedLog: null,
      
      filters: {
        user_id: '',
        action: '',
        model_type: '',
        date_from: '',
        date_to: ''
      },
      
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 50,
        total: 0
      }
    }
  },
  
  mounted() {
    this.loadData();
  },
  
  methods: {
    async loadData() {
      this.loading = true;
      try {
        await Promise.all([
          this.loadLogs(),
          this.loadFilterData()
        ]);
      } catch (error) {
        console.error('Error loading data:', error);
        this.$toast.error('Error al cargar los datos');
      } finally {
        this.loading = false;
      }
    },
    
    async loadLogs(page = 1) {
      try {
        const params = new URLSearchParams({
          page: page,
          ...this.filters
        });
        
        // Remove empty filters
        for (const [key, value] of params.entries()) {
          if (!value || value === '') {
            params.delete(key);
          }
        }
        
        const response = await fetch(`/api/activity-logs?${params}`);
        if (!response.ok) {
          throw new Error('Failed to load logs');
        }
        
        const data = await response.json();
        this.logs = data.data;
        this.pagination = {
          current_page: data.current_page,
          last_page: data.last_page,
          per_page: data.per_page,
          total: data.total
        };
      } catch (error) {
        console.error('Error loading logs:', error);
        this.$toast.error('Error al cargar los registros de actividad');
      }
    },
    
    async loadFilterData() {
      try {
        const response = await fetch('/api/activity-logs/filter-data');
        if (!response.ok) {
          throw new Error('Failed to load filter data');
        }
        
        const data = await response.json();
        this.users = data.users;
        this.actions = data.actions;
        this.modelTypes = data.modelTypes;
      } catch (error) {
        console.error('Error loading filter data:', error);
      }
    },
    
    applyFilters() {
      this.pagination.current_page = 1;
      this.loadLogs(1);
    },
    
    clearFilters() {
      this.filters = {
        user_id: '',
        action: '',
        model_type: '',
        date_from: '',
        date_to: ''
      };
      this.applyFilters();
    },
    
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.loadLogs(page);
      }
    },
    
    getVisiblePages() {
      const current = this.pagination.current_page;
      const last = this.pagination.last_page;
      const delta = 2;
      const range = [];
      
      for (let i = Math.max(2, current - delta); 
           i <= Math.min(last - 1, current + delta); 
           i++) {
        range.push(i);
      }
      
      if (current - delta > 2) {
        range.unshift('...');
      }
      if (current + delta < last - 1) {
        range.push('...');
      }
      
      range.unshift(1);
      if (last !== 1) {
        range.push(last);
      }
      
      return range.filter((v, i, a) => a.indexOf(v) === i);
    },
    
    showLogDetails(log) {
      this.selectedLog = log;
      $(this.$refs.logDetailsModal).modal('show');
    },
    
    closeLogDetails() {
      $(this.$refs.logDetailsModal).modal('hide');
      this.selectedLog = null;
    },
    
    async exportLogs() {
      try {
        const params = new URLSearchParams(this.filters);
        
        // Remove empty filters
        for (const [key, value] of params.entries()) {
          if (!value || value === '') {
            params.delete(key);
          }
        }
        
        const url = `/api/activity-logs/export?${params}`;
        window.open(url, '_blank');
      } catch (error) {
        console.error('Error exporting logs:', error);
        this.$toast.error('Error al exportar los registros');
      }
    },
    
    formatDate(dateString) {
      return new Date(dateString).toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
    },
    
    formatDateTime(dateString) {
      return new Date(dateString).toLocaleString('es-ES', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
    },
    
    getUserInitial(name) {
      return name ? name.charAt(0).toUpperCase() : '?';
    },
    
    getActionBadgeClass(action) {
      const classes = {
        create: 'badge badge-success',
        update: 'badge badge-warning',
        delete: 'badge badge-danger'
      };
      return classes[action] || 'badge badge-info';
    },
    
    getModelBaseName(modelType) {
      return modelType ? modelType.split('\\').pop() : '';
    },
    
    capitalizeFirst(str) {
      return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
    }
  }
}
</script>

<style scoped>
.user-avatar {
  font-family: Arial, sans-serif;
  font-weight: bold;
}

.text-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

pre {
  font-size: 12px;
  max-height: 200px;
  overflow-y: auto;
}

.pagination .page-link {
  cursor: pointer;
}

.pagination .page-item.disabled .page-link {
  cursor: not-allowed;
}
</style>
