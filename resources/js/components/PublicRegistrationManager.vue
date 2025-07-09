<template>
  <div id="public-registration-app">
    <!-- Sistema de notificaciones -->
    <notification-system 
      ref="notifications"
      :notifications="notifications"
      @remove="removeNotification">
    </notification-system>

    <!-- Header de la aplicación -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">
              <i class="fas fa-calendar-plus text-primary"></i>
              Registro de Jornadas BMX
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">{{ currentView === 'index' ? 'Jornadas Disponibles' : 'Registro' }}</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Contenido principal -->
    <section class="content">
      <div class="container-fluid">
        
        <!-- Vista de Lista de Jornadas -->
        <div v-if="currentView === 'index'" class="matchdays-index">
          <div class="row">
            <div class="col-12">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-calendar-check"></i>
                    Jornadas Disponibles para Registro
                  </h3>
                  <div class="card-tools">
                    <button @click="refreshMatchdays" class="btn btn-tool" :disabled="loading">
                      <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  
                  <!-- Estado de carga -->
                  <loading-spinner v-if="loading" message="Cargando jornadas disponibles..."></loading-spinner>
                  
                  <!-- Lista de jornadas -->
                  <div v-else>
                    <div v-if="matchdays.length > 0" class="row">
                      <div 
                        v-for="matchday in matchdays" 
                        :key="matchday.id" 
                        class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        
                        <div class="card card-outline card-primary h-100 matchday-card">
                          <div class="card-header bg-gradient-primary">
                            <h5 class="card-title text-white mb-0">
                              <i class="fas fa-flag-checkered"></i>
                              {{ matchday.full_name || matchday.name }}
                            </h5>
                          </div>
                          <div class="card-body d-flex flex-column">
                            <div class="matchday-info mb-3">
                              <div class="info-item mb-2">
                                <i class="fas fa-trophy text-warning"></i>
                                <strong>Campeonato:</strong>
                                <div class="text-muted">{{ matchday.championship.name }}</div>
                              </div>
                              
                              <div class="info-item mb-2">
                                <i class="fas fa-calendar text-info"></i>
                                <strong>Fecha:</strong>
                                <div class="text-muted">{{ formatDate(matchday.date) }}</div>
                              </div>
                              
                              <div v-if="matchday.start_time" class="info-item mb-2">
                                <i class="fas fa-clock text-success"></i>
                                <strong>Hora:</strong>
                                <div class="text-muted">{{ formatTime(matchday.start_time) }}</div>
                              </div>
                              
                              <div v-if="matchday.venue" class="info-item mb-2">
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                <strong>Lugar:</strong>
                                <div class="text-muted">{{ matchday.venue }}</div>
                              </div>
                              
                              <div class="info-item mb-3">
                                <i class="fas fa-users text-info"></i>
                                <strong>Participantes:</strong>
                                <div class="text-muted">
                                  <span class="badge badge-info badge-lg">
                                    {{ matchday.participants_count }} inscritos
                                  </span>
                                </div>
                              </div>
                              
                              <div class="info-item mb-3">
                                <i class="fas fa-dollar-sign text-warning"></i>
                                <strong>Inscripción:</strong>
                                <div class="text-success h5 mb-0">
                                  ${{ formatCurrency(matchday.entry_fee || 5000) }} CLP
                                </div>
                              </div>
                            </div>
                            
                            <div class="mt-auto">
                              <div class="btn-group-vertical w-100" role="group">
                                <button 
                                  @click="showRegistrationForm(matchday)" 
                                  class="btn btn-primary mb-2"
                                  :disabled="!matchday.is_registration_open">
                                  <i class="fas fa-user-plus"></i>
                                  {{ matchday.is_registration_open ? 'Registrarse' : 'Registro Cerrado' }}
                                </button>
                                <button 
                                  @click="showParticipants(matchday)" 
                                  class="btn btn-outline-info btn-sm">
                                  <i class="fas fa-users"></i>
                                  Ver Participantes ({{ matchday.participants_count }})
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- No hay jornadas -->
                    <div v-else class="text-center py-5">
                      <div class="mb-4">
                        <i class="fas fa-calendar-times fa-5x text-muted"></i>
                      </div>
                      <h4 class="text-muted">No hay jornadas disponibles</h4>
                      <p class="text-muted">
                        Actualmente no hay jornadas abiertas para registro público.<br>
                        Vuelve a revisar pronto para nuevas fechas.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Información adicional -->
          <div class="row">
            <div class="col-12">
              <div class="card card-info">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Información del Registro
                  </h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <h5><i class="fas fa-clipboard-list text-primary"></i> Requisitos para participar:</h5>
                      <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> Estar registrado como piloto</li>
                        <li><i class="fas fa-check text-success"></i> Tener categoría asignada</li>
                        <li><i class="fas fa-check text-success"></i> Realizar el pago de inscripción</li>
                      </ul>
                    </div>
                    <div class="col-md-6">
                      <h5><i class="fas fa-credit-card text-warning"></i> Métodos de pago:</h5>
                      <ul class="list-unstyled">
                        <li><i class="fas fa-credit-card text-info"></i> Tarjeta de crédito/débito</li>
                        <li><i class="fas fa-university text-info"></i> Transferencia bancaria</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Vista de Formulario de Registro -->
        <div v-else-if="currentView === 'register'" class="registration-form">
          <div class="row">
            <div class="col-lg-8 mx-auto">
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-user-plus"></i>
                    Registro para {{ selectedMatchday?.name }}
                  </h3>
                  <div class="card-tools">
                    <button @click="backToIndex" class="btn btn-tool">
                      <i class="fas fa-arrow-left"></i>
                    </button>
                  </div>
                </div>
                
                <form @submit.prevent="submitRegistration">
                  <div class="card-body">
                    
                    <!-- Información de la jornada -->
                    <div class="alert alert-info">
                      <h5><i class="fas fa-info-circle"></i> Información de la Jornada</h5>
                      <div class="row">
                        <div class="col-md-6">
                          <strong>Campeonato:</strong> {{ selectedMatchday?.championship?.name }}<br>
                          <strong>Fecha:</strong> {{ formatDate(selectedMatchday?.date) }}<br>
                          <strong>Hora:</strong> {{ formatTime(selectedMatchday?.start_time) }}
                        </div>
                        <div class="col-md-6">
                          <strong>Lugar:</strong> {{ selectedMatchday?.venue }}<br>
                          <strong>Inscripción:</strong> ${{ formatCurrency(selectedMatchday?.entry_fee || 5000) }} CLP
                        </div>
                      </div>
                    </div>

                    <!-- Búsqueda de piloto -->
                    <div class="form-group">
                      <label for="pilot-search">
                        <i class="fas fa-search"></i> Buscar Piloto
                      </label>
                      <div class="input-group">
                        <input 
                          v-model="pilotSearchTerm"
                          @input="searchPilots"
                          type="text" 
                          id="pilot-search"
                          class="form-control" 
                          placeholder="Buscar por nombre, apellido o RUT..."
                          :disabled="searchingPilots">
                        <div class="input-group-append">
                          <span class="input-group-text">
                            <i class="fas fa-search" v-if="!searchingPilots"></i>
                            <i class="fas fa-spinner fa-spin" v-else></i>
                          </span>
                        </div>
                      </div>
                      
                      <!-- Resultados de búsqueda -->
                      <div v-if="searchResults.length > 0" class="search-results mt-2">
                        <div class="list-group">
                          <button 
                            v-for="pilot in searchResults" 
                            :key="pilot.id"
                            @click="selectPilot(pilot)"
                            type="button"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                              <strong>{{ pilot.first_name }} {{ pilot.last_name }}</strong><br>
                              <small class="text-muted">
                                RUT: {{ pilot.rut }} | 
                                Categoría: {{ pilot.category?.name || 'Sin categoría' }} |
                                Club: {{ pilot.club?.name || 'Sin club' }}
                              </small>
                            </div>
                            <i class="fas fa-user-plus text-primary"></i>
                          </button>
                        </div>
                      </div>
                      
                      <!-- Piloto seleccionado -->
                      <div v-if="selectedPilot" class="selected-pilot mt-3">
                        <div class="alert alert-success">
                          <h6><i class="fas fa-user-check"></i> Piloto Seleccionado</h6>
                          <div class="row">
                            <div class="col-md-6">
                              <strong>Nombre:</strong> {{ selectedPilot.first_name }} {{ selectedPilot.last_name }}<br>
                              <strong>RUT:</strong> {{ selectedPilot.rut }}
                            </div>
                            <div class="col-md-6">
                              <strong>Categoría:</strong> {{ selectedPilot.category?.name || 'Sin categoría' }}<br>
                              <strong>Club:</strong> {{ selectedPilot.club?.name || 'Sin club' }}
                            </div>
                          </div>
                          <button @click="clearSelectedPilot" type="button" class="btn btn-sm btn-outline-secondary mt-2">
                            <i class="fas fa-times"></i> Cambiar piloto
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="form-group">
                      <label for="observations">
                        <i class="fas fa-comment"></i> Observaciones (opcional)
                      </label>
                      <textarea 
                        v-model="registrationForm.observations"
                        id="observations"
                        class="form-control" 
                        rows="3" 
                        placeholder="Comentarios adicionales...">
                      </textarea>
                    </div>

                    <!-- Términos y condiciones -->
                    <div class="form-group">
                      <div class="custom-control custom-checkbox">
                        <input 
                          v-model="registrationForm.acceptTerms"
                          type="checkbox" 
                          class="custom-control-input" 
                          id="accept-terms">
                        <label class="custom-control-label" for="accept-terms">
                          Acepto los términos y condiciones del evento
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  <div class="card-footer">
                    <div class="row">
                      <div class="col-md-6">
                        <button @click="backToIndex" type="button" class="btn btn-secondary">
                          <i class="fas fa-arrow-left"></i> Volver
                        </button>
                      </div>
                      <div class="col-md-6 text-right">
                        <button 
                          type="submit" 
                          class="btn btn-primary"
                          :disabled="!canSubmitRegistration || submittingRegistration">
                          <i class="fas fa-spinner fa-spin" v-if="submittingRegistration"></i>
                          <i class="fas fa-credit-card" v-else></i>
                          {{ submittingRegistration ? 'Procesando...' : 'Proceder al Pago' }}
                        </button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Vista de Participantes -->
        <div v-else-if="currentView === 'participants'" class="participants-view">
          <div class="row">
            <div class="col-12">
              <div class="card card-info">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-users"></i>
                    Participantes de {{ selectedMatchday?.name }}
                  </h3>
                  <div class="card-tools">
                    <button @click="backToIndex" class="btn btn-tool">
                      <i class="fas fa-arrow-left"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  
                  <!-- Estado de carga -->
                  <loading-spinner v-if="loadingParticipants" message="Cargando participantes..."></loading-spinner>
                  
                  <!-- Lista de participantes -->
                  <div v-else>
                    <div v-if="participants.length > 0">
                      <!-- Filtros -->
                      <div class="row mb-3">
                        <div class="col-md-6">
                          <label>Filtrar por categoría:</label>
                          <select v-model="participantCategoryFilter" class="form-control">
                            <option value="">Todas las categorías</option>
                            <option v-for="category in participantCategories" :key="category" :value="category">
                              {{ category }}
                            </option>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label>Filtrar por club:</label>
                          <select v-model="participantClubFilter" class="form-control">
                            <option value="">Todos los clubes</option>
                            <option v-for="club in participantClubs" :key="club" :value="club">
                              {{ club }}
                            </option>
                          </select>
                        </div>
                      </div>

                      <!-- Tabla de participantes -->
                      <data-table
                        :headers="participantHeaders"
                        :data="filteredParticipants"
                        :loading="false"
                        :per-page="20">
                        
                        <template #pilot="{ item }">
                          <div>
                            <strong>{{ item.pilot.first_name }} {{ item.pilot.last_name }}</strong><br>
                            <small class="text-muted">{{ item.pilot.rut }}</small>
                          </div>
                        </template>
                        
                        <template #category="{ item }">
                          <span class="badge badge-primary">
                            {{ item.pilot.category?.name || 'Sin categoría' }}
                          </span>
                        </template>
                        
                        <template #club="{ item }">
                          <span class="badge badge-info">
                            {{ item.pilot.club?.name || 'Sin club' }}
                          </span>
                        </template>
                        
                        <template #status="{ item }">
                          <status-badge 
                            :status="item.payment_status" 
                            :status-config="paymentStatusConfig">
                          </status-badge>
                        </template>
                        
                        <template #registration_date="{ item }">
                          {{ formatDateTime(item.created_at) }}
                        </template>
                      </data-table>
                    </div>
                    
                    <!-- No hay participantes -->
                    <div v-else class="text-center py-5">
                      <div class="mb-4">
                        <i class="fas fa-users-slash fa-4x text-muted"></i>
                      </div>
                      <h4 class="text-muted">Aún no hay participantes registrados</h4>
                      <p class="text-muted">
                        Sé el primero en registrarte para esta jornada.
                      </p>
                      <button @click="showRegistrationForm(selectedMatchday)" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Registrarse
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'PublicRegistrationManager',
  
  data() {
    return {
      // Estado general
      currentView: 'index', // 'index', 'register', 'participants'
      loading: false,
      notifications: [],
      
      // Datos principales
      matchdays: [],
      selectedMatchday: null,
      participants: [],
      loadingParticipants: false,
      
      // Búsqueda de pilotos
      pilotSearchTerm: '',
      searchResults: [],
      selectedPilot: null,
      searchingPilots: false,
      searchTimeout: null,
      
      // Formulario de registro
      registrationForm: {
        observations: '',
        acceptTerms: false
      },
      submittingRegistration: false,
      
      // Filtros de participantes
      participantCategoryFilter: '',
      participantClubFilter: '',
      
      // Configuraciones
      paymentStatusConfig: {
        'pending': { label: 'Pendiente', class: 'warning' },
        'paid': { label: 'Pagado', class: 'success' },
        'failed': { label: 'Fallido', class: 'danger' },
        'cancelled': { label: 'Cancelado', class: 'secondary' }
      },
      
      // Headers para tabla de participantes
      participantHeaders: [
        { key: 'pilot', label: 'Piloto', sortable: true },
        { key: 'category', label: 'Categoría', sortable: true },
        { key: 'club', label: 'Club', sortable: true },
        { key: 'status', label: 'Estado Pago', sortable: true },
        { key: 'registration_date', label: 'Fecha Registro', sortable: true }
      ]
    };
  },
  
  computed: {
    canSubmitRegistration() {
      return this.selectedPilot && this.registrationForm.acceptTerms;
    },
    
    filteredParticipants() {
      let filtered = this.participants;
      
      if (this.participantCategoryFilter) {
        filtered = filtered.filter(p => 
          p.pilot.category?.name === this.participantCategoryFilter
        );
      }
      
      if (this.participantClubFilter) {
        filtered = filtered.filter(p => 
          p.pilot.club?.name === this.participantClubFilter
        );
      }
      
      return filtered;
    },
    
    participantCategories() {
      const categories = this.participants
        .map(p => p.pilot.category?.name)
        .filter(Boolean);
      return [...new Set(categories)];
    },
    
    participantClubs() {
      const clubs = this.participants
        .map(p => p.pilot.club?.name)
        .filter(Boolean);
      return [...new Set(clubs)];
    }
  },
  
  mounted() {
    this.loadMatchdays();
  },
  
  methods: {
    // ===== GESTIÓN DE DATOS =====
    async loadMatchdays() {
      this.loading = true;
      try {
        const response = await axios.get('/api/public/matchdays');
        if (response.data.success) {
          this.matchdays = response.data.data;
        } else {
          this.showNotification('Error al cargar jornadas', 'error');
        }
      } catch (error) {
        console.error('Error loading matchdays:', error);
        this.showNotification('Error de conexión al cargar jornadas', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    async refreshMatchdays() {
      await this.loadMatchdays();
      this.showNotification('Jornadas actualizadas', 'success');
    },
    
    async loadParticipants(matchdayId) {
      this.loadingParticipants = true;
      try {
        const response = await axios.get(`/api/public/matchdays/${matchdayId}/participants`);
        if (response.data.success) {
          this.participants = response.data.data;
        } else {
          this.showNotification('Error al cargar participantes', 'error');
        }
      } catch (error) {
        console.error('Error loading participants:', error);
        this.showNotification('Error de conexión al cargar participantes', 'error');
      } finally {
        this.loadingParticipants = false;
      }
    },
    
    // ===== BÚSQUEDA DE PILOTOS =====
    searchPilots() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }
      
      this.searchTimeout = setTimeout(() => {
        if (this.pilotSearchTerm.length >= 3) {
          this.performPilotSearch();
        } else {
          this.searchResults = [];
        }
      }, 300);
    },
    
    async performPilotSearch() {
      this.searchingPilots = true;
      try {
        const response = await axios.post('/api/public/pilot/search', {
          term: this.pilotSearchTerm
        });
        
        if (response.data.success) {
          this.searchResults = response.data.data;
        } else {
          this.searchResults = [];
          this.showNotification('No se encontraron pilotos', 'info');
        }
      } catch (error) {
        console.error('Error searching pilots:', error);
        this.showNotification('Error en la búsqueda de pilotos', 'error');
        this.searchResults = [];
      } finally {
        this.searchingPilots = false;
      }
    },
    
    selectPilot(pilot) {
      this.selectedPilot = pilot;
      this.searchResults = [];
      this.pilotSearchTerm = `${pilot.first_name} ${pilot.last_name}`;
    },
    
    clearSelectedPilot() {
      this.selectedPilot = null;
      this.pilotSearchTerm = '';
      this.searchResults = [];
    },
    
    // ===== NAVEGACIÓN =====
    showRegistrationForm(matchday) {
      if (!matchday.is_registration_open) {
        this.showNotification('El registro para esta jornada está cerrado', 'warning');
        return;
      }
      
      this.selectedMatchday = matchday;
      this.currentView = 'register';
      this.resetRegistrationForm();
    },
    
    showParticipants(matchday) {
      this.selectedMatchday = matchday;
      this.currentView = 'participants';
      this.loadParticipants(matchday.id);
    },
    
    backToIndex() {
      this.currentView = 'index';
      this.selectedMatchday = null;
      this.resetRegistrationForm();
      this.participants = [];
    },
    
    resetRegistrationForm() {
      this.registrationForm = {
        observations: '',
        acceptTerms: false
      };
      this.clearSelectedPilot();
    },
    
    // ===== REGISTRO =====
    async submitRegistration() {
      if (!this.canSubmitRegistration) {
        this.showNotification('Complete todos los campos requeridos', 'warning');
        return;
      }
      
      this.submittingRegistration = true;
      try {
        const response = await axios.post(`/api/public/matchdays/${this.selectedMatchday.id}/register`, {
          pilot_id: this.selectedPilot.id,
          observations: this.registrationForm.observations
        });
        
        if (response.data.success) {
          // Redirigir a página de pago
          if (response.data.payment_url) {
            window.location.href = response.data.payment_url;
          } else {
            this.showNotification('Registro completado exitosamente', 'success');
            this.backToIndex();
          }
        } else {
          this.showNotification(response.data.message || 'Error en el registro', 'error');
        }
      } catch (error) {
        console.error('Error submitting registration:', error);
        this.showNotification(error.response?.data?.message || 'Error de conexión en el registro', 'error');
      } finally {
        this.submittingRegistration = false;
      }
    },
    
    // ===== UTILIDADES =====
    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('es-CL');
    },
    
    formatTime(time) {
      if (!time) return '';
      return time.substring(0, 5); // HH:MM
    },
    
    formatDateTime(datetime) {
      if (!datetime) return '';
      return new Date(datetime).toLocaleString('es-CL');
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('es-CL').format(amount);
    },
    
    // ===== NOTIFICACIONES =====
    showNotification(message, type = 'info') {
      const notification = {
        id: Date.now(),
        message,
        type,
        timestamp: new Date()
      };
      
      this.notifications.push(notification);
      
      // Auto-dismiss después de 5 segundos
      setTimeout(() => {
        this.removeNotification(notification.id);
      }, 5000);
    },
    
    removeNotification(id) {
      const index = this.notifications.findIndex(n => n.id === id);
      if (index > -1) {
        this.notifications.splice(index, 1);
      }
    }
  }
};
</script>

<style scoped>
.matchday-card {
  transition: transform 0.2s ease-in-out;
}

.matchday-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.info-item {
  display: flex;
  align-items: flex-start;
}

.info-item i {
  margin-right: 8px;
  margin-top: 2px;
  width: 16px;
}

.search-results {
  max-height: 300px;
  overflow-y: auto;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.selected-pilot {
  border-left: 4px solid #28a745;
}

.participants-view .form-control {
  margin-bottom: 10px;
}

@media (max-width: 768px) {
  .card-body .row {
    margin: 0;
  }
  
  .card-body .col-md-6 {
    padding: 0;
    margin-bottom: 10px;
  }
}
</style>
