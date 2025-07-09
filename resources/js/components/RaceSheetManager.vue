<template>
  <div class="race-sheet-manager">
    <!-- Indicador de carga global -->
    <div v-if="loading" class="loading-overlay">
      <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
      </div>
    </div>

    <!-- Sistema de pestañas para diferentes categorías -->
    <div class="card">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
          <li v-for="category in categories" :key="category.id" class="nav-item">
            <a 
              :class="['nav-link', { active: activeCategory === category.id }]"
              @click="setActiveCategory(category.id)"
              href="#"
              role="tab"
            >
              {{ category.name }}
              <span class="badge badge-primary ml-1">{{ getSeriesCount(category.id) }}</span>
            </a>
          </li>
        </ul>
      </div>

      <div class="card-body">
        <!-- Contenido de la categoría activa -->
        <div v-for="category in categories" :key="`content-${category.id}`">
          <div v-if="activeCategory === category.id">
            
            <!-- Botones de acción -->
            <div class="mb-3">
              <button 
                @click="showCreateSeriesModal(category.id)" 
                class="btn btn-primary mr-2"
              >
                <i class="fas fa-plus"></i> Nueva Ronda
              </button>
              
              <button 
                @click="generateAutomaticSeries(category.id)" 
                class="btn btn-success mr-2"
                :disabled="loading"
              >
                <i class="fas fa-magic"></i> Generar Automático
              </button>
              
              <button 
                @click="exportCategory(category.id)" 
                class="btn btn-info"
              >
                <i class="fas fa-download"></i> Exportar
              </button>
            </div>

            <!-- Series/Rondas de la categoría -->
            <div class="row">
              <div 
                v-for="series in getSeriesByCategory(category.id)" 
                :key="series.id"
                class="col-md-6 mb-4"
              >
                <div class="card border-left-primary">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                      {{ series.name }}
                      <small class="text-muted">
                        ({{ getTransferDescription(series) }})
                      </small>
                    </h5>
                    
                    <div class="btn-group">
                      <button 
                        @click="editSeries(series)" 
                        class="btn btn-sm btn-outline-primary"
                      >
                        <i class="fas fa-edit"></i>
                      </button>
                      <button 
                        @click="deleteSeries(series.id)" 
                        class="btn btn-sm btn-outline-danger"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>

                  <div class="card-body">
                    <!-- Mangas de la serie -->
                    <div v-for="heat in series.heats" :key="heat.id" class="mb-3">
                      <h6>{{ heat.name }}</h6>
                      
                      <!-- Lista de pilotos -->
                      <div 
                        class="pilot-list sortable-pilots"
                        :data-heat-id="heat.id"
                        @drop="handleDrop"
                        @dragover.prevent
                      >
                        <div 
                          v-for="lineup in heat.lineups" 
                          :key="lineup.id"
                          class="pilot-item d-flex justify-content-between align-items-center p-2 mb-1 bg-light rounded"
                          draggable="true"
                          @dragstart="handleDragStart($event, lineup)"
                        >
                          <div>
                            <span class="gate-position badge badge-secondary mr-2">
                              {{ lineup.gate_position }}
                            </span>
                            <strong>{{ lineup.pilot.name }}</strong>
                            <small class="text-muted ml-2">
                              {{ lineup.pilot.club.name }} - #{{ lineup.pilot.bib_number }}
                            </small>
                          </div>
                          
                          <div>
                            <!-- Controles de posición y tiempo -->
                            <input 
                              v-model="lineup.finish_position"
                              type="number"
                              class="form-control form-control-sm d-inline-block"
                              style="width: 60px;"
                              placeholder="Pos"
                              @change="updateLineup(lineup)"
                            >
                            <input 
                              v-model="lineup.lap_time"
                              type="text"
                              class="form-control form-control-sm d-inline-block ml-1"
                              style="width: 80px;"
                              placeholder="Tiempo"
                              @change="updateLineup(lineup)"
                            >
                            <button 
                              @click="removePilotFromHeat(lineup.id)"
                              class="btn btn-sm btn-outline-danger ml-1"
                            >
                              <i class="fas fa-times"></i>
                            </button>
                          </div>
                        </div>
                        
                        <!-- Mensaje si no hay pilotos -->
                        <div v-if="!heat.lineups || heat.lineups.length === 0" class="text-center text-muted p-3">
                          Arrastra pilotos aquí o 
                          <button @click="showPilotSelector(heat.id)" class="btn btn-sm btn-link p-0">
                            selecciona pilotos
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pilotos disponibles -->
            <div class="card mt-4">
              <div class="card-header">
                <h5>Pilotos Disponibles - {{ category.name }}</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div 
                    v-for="pilot in getAvailablePilots(category.id)" 
                    :key="pilot.id"
                    class="col-md-3 mb-2"
                  >
                    <div 
                      class="pilot-card p-2 border rounded cursor-pointer"
                      draggable="true"
                      @dragstart="handleDragStart($event, { pilot })"
                      @click="selectPilot(pilot)"
                    >
                      <strong>{{ pilot.name }}</strong><br>
                      <small>{{ pilot.club.name }} - #{{ pilot.bib_number }}</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para crear nueva serie -->
    <div v-if="showCreateModal" class="modal d-block" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nueva Ronda</h5>
            <button @click="showCreateModal = false" type="button" class="close">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="createSeries">
              <div class="form-group">
                <label>Nombre de la Ronda</label>
                <input v-model="newSeries.name" type="text" class="form-control" required>
              </div>
              
              <div class="form-group">
                <label>Máximo de Pilotos</label>
                <select v-model="newSeries.max_pilots" class="form-control">
                  <option value="4">4 pilotos</option>
                  <option value="6">6 pilotos</option>
                  <option value="8" selected>8 pilotos</option>
                </select>
              </div>
              
              <div class="row">
                <div class="col-md-4">
                  <label>Avanzan a Final</label>
                  <input v-model="newSeries.transfer_to_final" type="number" class="form-control" min="0" max="8">
                </div>
                <div class="col-md-4">
                  <label>Avanzan a Semifinal</label>
                  <input v-model="newSeries.transfer_to_semifinal" type="number" class="form-control" min="0" max="8">
                </div>
                <div class="col-md-4">
                  <label>Avanzan a Cuartos</label>
                  <input v-model="newSeries.transfer_to_quarterfinal" type="number" class="form-control" min="0" max="8">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button @click="showCreateModal = false" type="button" class="btn btn-secondary">Cancelar</button>
            <button @click="createSeries" type="button" class="btn btn-primary">Crear Ronda</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para editar serie -->
    <div v-if="showEditModal" class="modal d-block" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Ronda</h5>
            <button @click="showEditModal = false" type="button" class="close">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateSeries">
              <div class="form-group">
                <label>Nombre de la Ronda</label>
                <input v-model="editingSeries.name" type="text" class="form-control" required>
              </div>
              
              <div class="form-group">
                <label>Máximo de Pilotos</label>
                <select v-model="editingSeries.max_pilots" class="form-control">
                  <option value="4">4 pilotos</option>
                  <option value="6">6 pilotos</option>
                  <option value="8">8 pilotos</option>
                </select>
              </div>
              
              <div class="row">
                <div class="col-md-4">
                  <label>Avanzan a Final</label>
                  <input v-model="editingSeries.transfer_to_final" type="number" class="form-control" min="0" max="8">
                </div>
                <div class="col-md-4">
                  <label>Avanzan a Semifinal</label>
                  <input v-model="editingSeries.transfer_to_semifinal" type="number" class="form-control" min="0" max="8">
                </div>
                <div class="col-md-4">
                  <label>Avanzan a Cuartos</label>
                  <input v-model="editingSeries.transfer_to_quarterfinal" type="number" class="form-control" min="0" max="8">
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button @click="showEditModal = false" type="button" class="btn btn-secondary">Cancelar</button>
            <button @click="updateSeries" type="button" class="btn btn-primary">Actualizar Ronda</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para seleccionar pilotos -->
    <div v-if="showPilotSelectorModal" class="modal d-block" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Seleccionar Pilotos</h5>
            <button @click="showPilotSelectorModal = false" type="button" class="close">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div 
                v-for="pilot in getAvailablePilots(activeCategory)" 
                :key="pilot.id"
                class="col-md-6 mb-2"
              >
                <div 
                  class="pilot-selector-card p-2 border rounded cursor-pointer"
                  :class="{ 'border-primary bg-light': selectedPilots.includes(pilot) }"
                  @click="selectPilot(pilot)"
                >
                  <strong>{{ pilot.name }}</strong><br>
                  <small>{{ pilot.club.name }} - #{{ pilot.bib_number }}</small>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button @click="showPilotSelectorModal = false" type="button" class="btn btn-secondary">Cancelar</button>
            <button @click="assignSelectedPilots" type="button" class="btn btn-primary">
              Asignar Pilotos ({{ selectedPilots.length }})
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Sortable from 'sortablejs';

export default {
  name: 'RaceSheetManager',
  props: {
    matchdayId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      loading: false,
      categories: [],
      series: [],
      pilots: [],
      activeCategory: null,
      showCreateModal: false,
      showEditModal: false,
      showPilotSelectorModal: false,
      selectedHeatId: null,
      selectedPilots: [],
      editingSeries: {},
      newSeries: {
        name: '',
        max_pilots: 8,
        transfer_to_final: 4,
        transfer_to_semifinal: 0,
        transfer_to_quarterfinal: 0,
        category_id: null
      },
      draggedItem: null
    }
  },
  async mounted() {
    await this.loadData();
    this.initializeSortable();
  },
  methods: {
    async loadData() {
      this.loading = true;
      try {
        const response = await axios.get(`/api/jornadas/${this.matchdayId}/planilla`);
        
        if (response.data.success) {
          const data = response.data.data;
          
          // Transform the data structure from Laravel to match component expectations
          this.categories = [];
          this.series = [];
          this.pilots = [];
          
          // Extract categories and pilots from seriesByCategory and participantsByCategory
          for (const [categoryName, categorySeries] of Object.entries(data.seriesByCategory || {})) {
            if (categorySeries.length > 0) {
              const category = categorySeries[0].category;
              if (!this.categories.find(c => c.id === category.id)) {
                this.categories.push(category);
              }
              this.series.push(...categorySeries);
            }
          }
          
          for (const [categoryName, categoryParticipants] of Object.entries(data.participantsByCategory || {})) {
            for (const participant of categoryParticipants) {
              if (!this.pilots.find(p => p.id === participant.pilot.id)) {
                this.pilots.push({
                  ...participant.pilot,
                  category_id: participant.category.id
                });
              }
            }
          }
          
          if (this.categories.length > 0) {
            this.activeCategory = this.categories[0].id;
          }
        } else {
          throw new Error(response.data.message || 'Error al cargar datos');
        }
      } catch (error) {
        this.$parent.showNotification(error.response?.data?.message || 'Error al cargar datos', 'error');
      } finally {
        this.loading = false;
      }
    },

    setActiveCategory(categoryId) {
      this.activeCategory = categoryId;
    },

    getSeriesByCategory(categoryId) {
      return this.series.filter(s => s.category_id === categoryId);
    },

    getSeriesCount(categoryId) {
      return this.getSeriesByCategory(categoryId).length;
    },

    getAvailablePilots(categoryId) {
      // Pilotos que no están asignados a ninguna serie de esta categoría
      const assignedPilotIds = this.getSeriesByCategory(categoryId)
        .flatMap(s => s.heats)
        .flatMap(h => h.lineups)
        .map(l => l.pilot.id);
      
      return this.pilots.filter(p => 
        p.category_id === categoryId && 
        !assignedPilotIds.includes(p.id)
      );
    },

    getTransferDescription(series) {
      const transfers = [];
      
      if (series.transfer_to_final > 0) {
        transfers.push(`${series.transfer_to_final} a la final`);
      }
      
      if (series.transfer_to_semifinal > 0) {
        transfers.push(`${series.transfer_to_semifinal} a semifinal`);
      }
      
      if (series.transfer_to_quarterfinal > 0) {
        transfers.push(`${series.transfer_to_quarterfinal} a cuartos`);
      }
      
      if (transfers.length === 0) {
        return '0 avanzan';
      }
      
      if (transfers.length === 1) {
        return `avanzan ${transfers[0]}`;
      }
      
      const lastTransfer = transfers.pop();
      return `avanzan ${transfers.join(', ')} y ${lastTransfer}`;
    },

    showCreateSeriesModal(categoryId) {
      this.newSeries.category_id = categoryId;
      this.newSeries.name = `Ronda ${this.getSeriesCount(categoryId) + 1}`;
      this.showCreateModal = true;
    },

    async createSeries() {
      this.loading = true;
      try {
        const response = await axios.post(
          `/api/jornadas/${this.matchdayId}/planilla/series`,
          this.newSeries
        );
        
        if (response.data.success) {
          this.series.push(response.data.data);
          this.showCreateModal = false;
          this.$parent.showNotification(response.data.message || 'Ronda creada exitosamente', 'success');
          
          // Resetear formulario
          this.newSeries = {
            name: '',
            max_pilots: 8,
            transfer_to_final: 4,
            transfer_to_semifinal: 0,
            transfer_to_quarterfinal: 0,
            category_id: null
          };
        } else {
          throw new Error(response.data.message || 'Error al crear ronda');
        }
      } catch (error) {
        this.$parent.showNotification(error.response?.data?.message || 'Error al crear ronda', 'error');
      } finally {
        this.loading = false;
      }
    },

    async generateAutomaticSeries(categoryId) {
      if (!confirm('¿Generar series automáticamente? Esto eliminará las series existentes.')) {
        return;
      }

      this.loading = true;
      try {
        const response = await axios.post(
          `/admin/matchdays/${this.matchdayId}/race-sheets/generate`,
          { category_id: categoryId }
        );
        
        // Actualizar series
        this.series = this.series.filter(s => s.category_id !== categoryId);
        this.series.push(...response.data.series);
        
        this.$parent.showNotification('Series generadas automáticamente', 'success');
      } catch (error) {
        this.$parent.showNotification('Error al generar series', 'error');
      } finally {
        this.loading = false;
      }
    },

    handleDragStart(event, item) {
      this.draggedItem = item;
      event.dataTransfer.effectAllowed = 'move';
    },

    async handleDrop(event) {
      event.preventDefault();
      const heatId = event.currentTarget.dataset.heatId;
      
      if (this.draggedItem && this.draggedItem.pilot) {
        await this.assignPilotToHeat(this.draggedItem.pilot.id, heatId);
      }
      
      this.draggedItem = null;
    },

    async assignPilotToHeat(pilotId, heatId) {
      this.loading = true;
      try {
        const response = await axios.post(
          `/api/planilla/asignar-piloto`,
          { pilot_id: pilotId, heat_id: heatId }
        );
        
        if (response.data.success) {
          // Actualizar datos locales
          await this.loadData();
          this.$parent.showNotification(response.data.message || 'Piloto asignado exitosamente', 'success');
        } else {
          throw new Error(response.data.message || 'Error al asignar piloto');
        }
      } catch (error) {
        this.$parent.showNotification(error.response?.data?.message || 'Error al asignar piloto', 'error');
      } finally {
        this.loading = false;
      }
    },

    async removePilotFromHeat(lineupId) {
      if (!confirm('¿Estás seguro de querer remover este piloto?')) {
        return;
      }

      this.loading = true;
      try {
        const response = await axios.delete(`/api/planilla/lineups/${lineupId}`);
        
        if (response.data.success) {
          // Actualizar datos locales
          await this.loadData();
          this.$parent.showNotification(response.data.message || 'Piloto removido exitosamente', 'success');
        } else {
          throw new Error(response.data.message || 'Error al remover piloto');
        }
      } catch (error) {
        this.$parent.showNotification(error.response?.data?.message || 'Error al remover piloto', 'error');
      } finally {
        this.loading = false;
      }
    },

    selectPilot(pilot) {
      // Implementar selección múltiple de pilotos
      this.selectedPilots = this.selectedPilots || [];
      const index = this.selectedPilots.findIndex(p => p.id === pilot.id);
      
      if (index > -1) {
        this.selectedPilots.splice(index, 1);
      } else {
        this.selectedPilots.push(pilot);
      }
    },

    showPilotSelector(heatId) {
      // Mostrar modal para seleccionar pilotos para una manga específica
      this.selectedHeatId = heatId;
      this.showPilotSelectorModal = true;
    },

    async editSeries(series) {
      this.editingSeries = { ...series };
      this.showEditModal = true;
    },

    async exportCategory(categoryId) {
      this.loading = true;
      try {
        const response = await axios.get(
          `/admin/matchdays/${this.matchdayId}/race-sheets/export/${categoryId}`,
          { responseType: 'blob' }
        );
        
        // Crear y descargar archivo
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `planilla_categoria_${categoryId}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        
        this.$parent.showNotification('Exportación completada', 'success');
      } catch (error) {
        this.$parent.showNotification('Error al exportar', 'error');
      } finally {
        this.loading = false;
      }
    },

    async updateLineup(lineup) {
      try {
        await axios.put(
          `/admin/race-sheets/lineups/${lineup.id}`,
          {
            finish_position: lineup.finish_position,
            lap_time: lineup.lap_time
          }
        );
        
        this.$parent.showNotification('Datos actualizados', 'success', 1000);
      } catch (error) {
        this.$parent.showNotification('Error al actualizar', 'error');
      }
    },

    async deleteSeries(seriesId) {
      if (!confirm('¿Estás seguro de eliminar esta ronda?')) {
        return;
      }

      this.loading = true;
      try {
        const response = await axios.delete(`/api/jornadas/${this.matchdayId}/planilla/series/${seriesId}`);
        
        if (response.data.success) {
          this.series = this.series.filter(s => s.id !== seriesId);
          this.$parent.showNotification(response.data.message || 'Ronda eliminada', 'success');
        } else {
          throw new Error(response.data.message || 'Error al eliminar ronda');
        }
      } catch (error) {
        this.$parent.showNotification(error.response?.data?.message || 'Error al eliminar ronda', 'error');
      } finally {
        this.loading = false;
      }
    },

    initializeSortable() {
      this.$nextTick(() => {
        const sortableElements = document.querySelectorAll('.sortable-pilots');
        
        sortableElements.forEach(element => {
          new Sortable(element, {
            group: 'pilots',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: (evt) => {
              this.handleSortEnd(evt);
            }
          });
        });
      });
    },

    handleSortEnd(evt) {
      // Lógica para manejar el reordenamiento
      console.log('Elemento movido:', evt);
    },

    async updateSeries() {
      this.loading = true;
      try {
        const response = await axios.put(
          `/admin/race-sheets/series/${this.editingSeries.id}`,
          this.editingSeries
        );
        
        // Actualizar serie en el array local
        const index = this.series.findIndex(s => s.id === this.editingSeries.id);
        if (index > -1) {
          this.series.splice(index, 1, response.data.series);
        }
        
        this.showEditModal = false;
        this.$parent.showNotification('Ronda actualizada exitosamente', 'success');
      } catch (error) {
        this.$parent.showNotification('Error al actualizar ronda', 'error');
      } finally {
        this.loading = false;
      }
    },

    async assignSelectedPilots() {
      if (this.selectedPilots.length === 0) {
        this.$parent.showNotification('Selecciona al menos un piloto', 'warning');
        return;
      }

      this.loading = true;
      try {
        for (const pilot of this.selectedPilots) {
          await this.assignPilotToHeat(pilot.id, this.selectedHeatId);
        }
        
        this.selectedPilots = [];
        this.showPilotSelectorModal = false;
        this.$parent.showNotification('Pilotos asignados exitosamente', 'success');
      } catch (error) {
        this.$parent.showNotification('Error al asignar pilotos', 'error');
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.pilot-card {
  transition: all 0.2s;
  cursor: grab;
}

.pilot-card:hover {
  background-color: #f8f9fa;
  border-color: #007bff !important;
}

.pilot-card:active {
  cursor: grabbing;
}

.pilot-item {
  transition: all 0.2s;
  cursor: grab;
}

.pilot-item:hover {
  background-color: #e9ecef !important;
}

.sortable-ghost {
  opacity: 0.5;
  background: #c8ebfb;
}

.border-left-primary {
  border-left: 0.25rem solid #007bff !important;
}

.cursor-pointer {
  cursor: pointer;
}

.pilot-selector-card {
  transition: all 0.2s;
  cursor: pointer;
}

.pilot-selector-card:hover {
  background-color: #f8f9fa;
  border-color: #007bff !important;
}

.modal {
  background: rgba(0, 0, 0, 0.5);
}
</style>
