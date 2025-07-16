<template>
    <div class="add-pilot-modal">
        <!-- Button trigger modal -->
        <button 
            type="button" 
            class="btn btn-success btn-sm" 
            @click="showModal = true"
        >
            <i class="fas fa-user-plus mr-1"></i>
            Agregar Piloto
        </button>

        <!-- Modal Vue puro -->
        <div v-if="showModal" class="vue-modal-overlay" @click.self="closeModal">
            <div class="modal-dialog modal-lg vue-modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus mr-2"></i>
                            Agregar Piloto a la Jornada
                        </h5>
                        <button 
                            type="button" 
                            class="close" 
                            @click="closeModal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">
                        <!-- Search form -->
                        <div class="form-group">
                            <label for="pilotSearch">Buscar Piloto</label>
                            <div class="input-group">
                                <input 
                                    id="pilotSearch"
                                    v-model="searchTerm" 
                                    type="text" 
                                    class="form-control"
                                    placeholder="Buscar por nombre o RUT..."
                                    @input="debouncedSearch"
                                    :disabled="loading"
                                >
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                                        <i v-else class="fas fa-search"></i>
                                    </span>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Solo se muestran pilotos activos registrados en el campeonato
                            </small>
                        </div>

                        <!-- Pilots list -->
                        <div v-if="pilots.length > 0" class="pilots-list">
                            <h6 class="mb-3">Pilotos Disponibles:</h6>
                            <div class="row">
                                <div 
                                    v-for="pilot in pilots" 
                                    :key="pilot.id" 
                                    class="col-md-6 mb-3"
                                >
                                    <div 
                                        class="card pilot-card h-100" 
                                        :class="{ 'selected': selectedPilot && selectedPilot.id === pilot.id }"
                                        @click="selectPilot(pilot)"
                                        style="cursor: pointer;"
                                    >
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="pilot-info flex-grow-1">
                                                    <h6 class="mb-1">
                                                        {{ pilot.first_name }} {{ pilot.last_name }}
                                                        <small v-if="pilot.nickname" class="text-muted">"{{ pilot.nickname }}"</small>
                                                    </h6>
                                                    <p class="mb-1 text-sm">
                                                        <strong>RUT:</strong> {{ pilot.rut }}
                                                    </p>
                                                    <p class="mb-1 text-sm">
                                                        <strong>Club:</strong> {{ pilot.club ? pilot.club.name : 'Sin club' }}
                                                    </p>
                                                    <p class="mb-0 text-sm">
                                                        <strong>Categoría:</strong> {{ pilot.category ? pilot.category.name : 'Sin categoría' }}
                                                    </p>
                                                </div>
                                                <div class="pilot-select">
                                                    <i 
                                                        v-if="selectedPilot && selectedPilot.id === pilot.id" 
                                                        class="fas fa-check-circle text-success"
                                                        style="font-size: 1.5rem;"
                                                    ></i>
                                                    <i 
                                                        v-else 
                                                        class="far fa-circle text-muted"
                                                        style="font-size: 1.5rem;"
                                                    ></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- No results -->
                        <div v-else-if="searchTerm && !loading" class="text-center py-4">
                            <i class="fas fa-search text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-muted">
                                No se encontraron pilotos con el término "{{ searchTerm }}"
                            </p>
                        </div>

                        <!-- Initial state -->
                        <div v-else-if="!searchTerm" class="text-center py-4">
                            <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-muted">
                                Escribe el nombre o RUT del piloto para comenzar la búsqueda
                            </p>
                        </div>

                        <!-- Selected pilot details -->
                        <div v-if="selectedPilot" class="selected-pilot-details mt-4">
                            <hr>
                            <h6>Piloto Seleccionado:</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6>{{ selectedPilot.first_name }} {{ selectedPilot.last_name }}</h6>
                                            <p class="mb-1"><strong>RUT:</strong> {{ selectedPilot.rut }}</p>
                                            <p class="mb-1"><strong>Club:</strong> {{ selectedPilot.club ? selectedPilot.club.name : 'Sin club' }}</p>
                                            <p class="mb-0"><strong>Categoría:</strong> {{ selectedPilot.category ? selectedPilot.category.name : 'Sin categoría' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label for="notes" class="small">Notas (opcional)</label>
                                                <textarea 
                                                    id="notes"
                                                    v-model="notes" 
                                                    class="form-control form-control-sm"
                                                    rows="3"
                                                    placeholder="Observaciones sobre la participación..."
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button 
                            type="button" 
                            class="btn btn-secondary" 
                            @click="closeModal"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="button" 
                            class="btn btn-success" 
                            @click="addPilot"
                            :disabled="!selectedPilot || submitting"
                        >
                            <i v-if="submitting" class="fas fa-spinner fa-spin mr-1"></i>
                            <i v-else class="fas fa-user-plus mr-1"></i>
                            {{ submitting ? 'Agregando...' : 'Agregar Piloto' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AddPilotToMatchday',
    props: {
        matchdayId: {
            type: [Number, String],
            required: true
        }
    },
    data() {
        return {
            showModal: false,
            searchTerm: '',
            pilots: [],
            selectedPilot: null,
            notes: '',
            loading: false,
            submitting: false,
            searchTimeout: null
        };
    },
    computed: {
        debouncedSearch() {
            return this.debounce(this.searchPilots, 300);
        }
    },
    methods: {
        debounce(func, wait) {
            return (...args) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => func.apply(this, args), wait);
            };
        },

        async searchPilots() {
            if (this.searchTerm.length < 2) {
                this.pilots = [];
                return;
            }

            this.loading = true;
            try {
                const response = await fetch(`/api/matchdays/${this.matchdayId}/available-pilots?search=${encodeURIComponent(this.searchTerm)}&limit=20`);
                
                if (!response.ok) {
                    throw new Error('Error al buscar pilotos');
                }
                
                const data = await response.json();
                this.pilots = data.success ? data.data : [];
                
            } catch (error) {
                console.error('Error searching pilots:', error);
                this.pilots = [];
                alert('Error al buscar pilotos. Por favor, intenta nuevamente.');
            } finally {
                this.loading = false;
            }
        },

        selectPilot(pilot) {
            this.selectedPilot = pilot;
        },

        async addPilot() {
            if (!this.selectedPilot) return;

            this.submitting = true;
            try {
                const response = await fetch(`/api/matchdays/${this.matchdayId}/add-pilot`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        pilot_id: this.selectedPilot.id,
                        category_id: this.selectedPilot.category ? this.selectedPilot.category.id : null,
                        notes: this.notes || null
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Error al agregar el piloto');
                }

                // Success
                alert(data.message || 'Piloto agregado exitosamente');
                
                // Emit event to parent component to refresh participant list
                this.$emit('pilot-added', data.data);
                
                // Cerrar modal y resetear formulario
                this.closeModal();

            } catch (error) {
                console.error('Error adding pilot:', error);
                alert(error.message || 'Error al agregar el piloto. Por favor, intenta nuevamente.');
            } finally {
                this.submitting = false;
            }
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
        },

        resetForm() {
            this.searchTerm = '';
            this.pilots = [];
            this.selectedPilot = null;
            this.notes = '';
            this.loading = false;
            this.submitting = false;
            if (this.searchTimeout) {
                clearTimeout(this.searchTimeout);
            }
        }
    }
};
</script>

<style scoped>
.pilot-card {
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.pilot-card:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.2);
}

.pilot-card.selected {
    border-color: #28a745;
    background-color: #f8fff9;
    box-shadow: 0 2px 8px rgba(40,167,69,0.2);
}

.text-sm {
    font-size: 0.875rem;
}

.pilots-list {
    max-height: 400px;
    overflow-y: auto;
}

.selected-pilot-details .card {
    border-left: 4px solid #28a745;
}

.modal-lg {
    max-width: 800px;
}

/* Modal Vue puro */
.vue-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.4);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
}
.vue-modal-dialog {
    margin: 0;
    max-width: 800px;
    width: 100%;
}
</style>
