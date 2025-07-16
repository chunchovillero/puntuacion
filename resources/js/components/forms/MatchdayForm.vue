<template>
    <div class="matchday-form">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar mr-2"></i>
                    {{ isEditing ? 'Editar Jornada' : 'Crear Nueva Jornada' }}
                </h3>
                <div class="card-tools">
                    <router-link :to="getBackUrl()" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        {{ getBackText() }}
                    </router-link>
                </div>
            </div>

            <div v-if="loading" class="card-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-3">{{ isEditing ? 'Cargando datos de la jornada...' : 'Preparando formulario...' }}</p>
            </div>

            <div v-else class="card-body">
                <form @submit.prevent="submitForm">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Información básica -->
                            <div class="form-group">
                                <label for="name">Nombre de la Jornada</label>
                                <input 
                                    id="name"
                                    v-model="form.name" 
                                    type="text" 
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.name }"
                                    placeholder="Ej: Jornada 1 - Clasificatoria"
                                >
                                <small class="form-text text-muted">Si se deja vacío, se generará automáticamente</small>
                                <div v-if="errors.name" class="invalid-feedback">
                                    {{ errors.name[0] }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="championship_id">Campeonato *</label>
                                        <select 
                                            id="championship_id"
                                            v-model="form.championship_id" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.championship_id }"
                                            required
                                        >
                                            <option value="">Seleccionar campeonato</option>
                                            <option 
                                                v-for="championship in championships" 
                                                :key="championship.id" 
                                                :value="championship.id"
                                            >
                                                {{ championship.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.championship_id" class="invalid-feedback">
                                            {{ errors.championship_id[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="number">Número de Jornada</label>
                                        <input 
                                            id="number"
                                            v-model.number="form.number" 
                                            type="number" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.number }"
                                            min="1"
                                            placeholder="Ej: 1"
                                        >
                                        <small class="form-text text-muted">Se asignará automáticamente si se deja vacío</small>
                                        <div v-if="errors.number" class="invalid-feedback">
                                            {{ errors.number[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea 
                                    id="description"
                                    v-model="form.description"
                                    class="form-control"
                                    rows="3"
                                    :class="{ 'is-invalid': errors.description }"
                                    placeholder="Descripción opcional de la jornada..."
                                ></textarea>
                                <div v-if="errors.description" class="invalid-feedback">
                                    {{ errors.description[0] }}
                                </div>
                            </div>

                            <!-- Fecha y horarios -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-clock mr-2"></i>
                                        Fecha y Horarios
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date">Fecha de la Jornada</label>
                                                <input 
                                                    id="date"
                                                    v-model="form.date" 
                                                    type="date" 
                                                    class="form-control"
                                                    :class="{ 'is-invalid': errors.date }"
                                                >
                                                <div v-if="errors.date" class="invalid-feedback">
                                                    {{ errors.date[0] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="start_time">Hora de Inicio</label>
                                                <input 
                                                    id="start_time"
                                                    v-model="form.start_time" 
                                                    type="time" 
                                                    class="form-control"
                                                    :class="{ 'is-invalid': errors.start_time }"
                                                >
                                                <div v-if="errors.start_time" class="invalid-feedback">
                                                    {{ errors.start_time[0] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="end_time">Hora de Fin</label>
                                                <input 
                                                    id="end_time"
                                                    v-model="form.end_time" 
                                                    type="time" 
                                                    class="form-control"
                                                    :class="{ 'is-invalid': errors.end_time }"
                                                >
                                                <div v-if="errors.end_time" class="invalid-feedback">
                                                    {{ errors.end_time[0] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ubicación -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        Ubicación
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="venue">Lugar/Pista</label>
                                        <input 
                                            id="venue"
                                            v-model="form.venue" 
                                            type="text" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.venue }"
                                            placeholder="Ej: Pista Nacional BMX"
                                        >
                                        <div v-if="errors.venue" class="invalid-feedback">
                                            {{ errors.venue[0] }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Dirección</label>
                                        <textarea 
                                            id="address"
                                            v-model="form.address"
                                            class="form-control"
                                            rows="2"
                                            :class="{ 'is-invalid': errors.address }"
                                            placeholder="Dirección completa del lugar..."
                                        ></textarea>
                                        <div v-if="errors.address" class="invalid-feedback">
                                            {{ errors.address[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Panel lateral -->
                        <div class="col-md-4">
                            <!-- Estado -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-cog mr-1"></i>
                                        Estado y Configuración
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="status">Estado</label>
                                        <select 
                                            id="status"
                                            v-model="form.status" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.status }"
                                        >
                                            <option value="draft">Borrador</option>
                                            <option value="active">Activa</option>
                                            <option value="completed">Completada</option>
                                            <option value="cancelled">Cancelada</option>
                                            <option value="postponed">Pospuesta</option>
                                        </select>
                                        <div v-if="errors.status" class="invalid-feedback">
                                            {{ errors.status[0] }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="entry_fee">Costo de Inscripción</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input 
                                                id="entry_fee"
                                                v-model.number="form.entry_fee" 
                                                type="number" 
                                                step="0.01"
                                                min="0"
                                                class="form-control"
                                                :class="{ 'is-invalid': errors.entry_fee }"
                                                placeholder="0.00"
                                            >
                                        </div>
                                        <div v-if="errors.entry_fee" class="invalid-feedback">
                                            {{ errors.entry_fee[0] }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input 
                                                id="public_registration_enabled"
                                                v-model="form.public_registration_enabled"
                                                type="checkbox" 
                                                class="custom-control-input"
                                            >
                                            <label class="custom-control-label" for="public_registration_enabled">
                                                Permitir inscripción pública
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">Los pilotos pueden inscribirse por sí mismos</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Club organizador -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-users mr-1"></i>
                                        Organización
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="organizer_club_id">Club Organizador</label>
                                        <select 
                                            id="organizer_club_id"
                                            v-model="form.organizer_club_id" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.organizer_club_id }"
                                        >
                                            <option value="">Sin club organizador</option>
                                            <option 
                                                v-for="club in clubs" 
                                                :key="club.id" 
                                                :value="club.id"
                                            >
                                                {{ club.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.organizer_club_id" class="invalid-feedback">
                                            {{ errors.organizer_club_id[0] }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="organizer_name">Nombre del Organizador</label>
                                        <input 
                                            id="organizer_name"
                                            v-model="form.organizer_name" 
                                            type="text" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.organizer_name }"
                                            placeholder="Nombre completo"
                                        >
                                        <div v-if="errors.organizer_name" class="invalid-feedback">
                                            {{ errors.organizer_name[0] }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="organizer_contact">Email de Contacto</label>
                                        <input 
                                            id="organizer_contact"
                                            v-model="form.organizer_contact" 
                                            type="email" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.organizer_contact }"
                                            placeholder="email@ejemplo.com"
                                        >
                                        <div v-if="errors.organizer_contact" class="invalid-feedback">
                                            {{ errors.organizer_contact[0] }}
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="organizer_phone">Teléfono de Contacto</label>
                                        <input 
                                            id="organizer_phone"
                                            v-model="form.organizer_phone" 
                                            type="tel" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.organizer_phone }"
                                            placeholder="+1 555-0123"
                                        >
                                        <div v-if="errors.organizer_phone" class="invalid-feedback">
                                            {{ errors.organizer_phone[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <router-link :to="getBackUrl()" class="btn btn-outline-secondary">
                                    <i class="fas fa-times mr-1"></i>
                                    Cancelar
                                </router-link>
                                <button 
                                    type="submit" 
                                    class="btn btn-primary"
                                    :disabled="submitting"
                                >
                                    <i v-if="submitting" class="fas fa-spinner fa-spin mr-1"></i>
                                    <i v-else class="fas fa-save mr-1"></i>
                                    {{ submitting ? 'Guardando...' : (isEditing ? 'Actualizar Jornada' : 'Crear Jornada') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'MatchdayForm',
    data() {
        return {
            loading: false,
            submitting: false,
            championships: [],
            clubs: [],
            form: {
                championship_id: '',
                number: '',
                name: '',
                description: '',
                date: '',
                start_time: '',
                end_time: '',
                venue: '',
                address: '',
                organizer_club_id: '',
                organizer_name: '',
                organizer_contact: '',
                organizer_phone: '',
                entry_fee: 0,
                public_registration_enabled: false,
                status: 'draft'
            },
            errors: {}
        };
    },
    computed: {
        matchdayId() {
            return this.$route.params.id;
        },
        isEditing() {
            return !!this.matchdayId;
        },
        // Detectar desde dónde viene el usuario
        fromChampionship() {
            return this.$route.query.from === 'championship' && this.$route.query.championshipId;
        },
        fromChampionshipId() {
            return this.$route.query.championshipId;
        }
    },
    async mounted() {
        await this.loadFormData();
        if (this.isEditing) {
            await this.loadMatchday();
        } else if (this.fromChampionship) {
            // Pre-seleccionar el campeonato si viene desde ahí
            this.form.championship_id = this.fromChampionshipId;
        }
    },
    methods: {
        async loadFormData() {
            this.loading = true;
            try {
                // Cargar campeonatos
                const championshipsResponse = await fetch('/api/championships');
                if (championshipsResponse.ok) {
                    const championshipsData = await championshipsResponse.json();
                    this.championships = championshipsData.data || championshipsData;
                }

                // Cargar clubes
                const clubsResponse = await fetch('/api/clubs');
                if (clubsResponse.ok) {
                    const clubsData = await clubsResponse.json();
                    this.clubs = clubsData.data || clubsData;
                }
            } catch (error) {
                console.error('Error loading form data:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadMatchday() {
            if (!this.matchdayId) return;
            
            this.loading = true;
            try {
                const response = await fetch(`/api/matchdays/${this.matchdayId}`);
                if (!response.ok) {
                    throw new Error('Jornada no encontrada');
                }
                
                const data = await response.json();
                const matchday = data.success ? data.data : data;
                
                // Llenar el formulario con los datos existentes
                this.form = {
                    championship_id: matchday.championship_id || '',
                    number: matchday.number || '',
                    name: matchday.name || '',
                    description: matchday.description || '',
                    date: matchday.date || '',
                    start_time: matchday.start_time || '',
                    end_time: matchday.end_time || '',
                    venue: matchday.venue || '',
                    address: matchday.address || '',
                    organizer_club_id: matchday.organizer_club_id || '',
                    organizer_name: matchday.organizer_name || '',
                    organizer_contact: matchday.organizer_contact || '',
                    organizer_phone: matchday.organizer_phone || '',
                    entry_fee: matchday.entry_fee || 0,
                    public_registration_enabled: matchday.public_registration_enabled || false,
                    status: matchday.status || 'draft'
                };
            } catch (error) {
                console.error('Error loading matchday:', error);
                alert('Error al cargar los datos de la jornada');
                this.$router.push({ name: 'matchdays' });
            } finally {
                this.loading = false;
            }
        },

        async submitForm() {
            this.submitting = true;
            this.errors = {};

            try {
                const url = this.isEditing 
                    ? `/api/matchdays/${this.matchdayId}` 
                    : '/api/matchdays';
                    
                const method = this.isEditing ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (response.ok) {
                    alert(this.isEditing ? 'Jornada actualizada exitosamente' : 'Jornada creada exitosamente');
                    
                    // Redirigir a la vista de detalle
                    const matchdayId = this.isEditing ? this.matchdayId : data.data.id;
                    if (this.fromChampionship) {
                        this.$router.push({ 
                            name: 'matchdays.show', 
                            params: { id: matchdayId },
                            query: {
                                from: 'championship',
                                championshipId: this.fromChampionshipId
                            }
                        });
                    } else {
                        this.$router.push({ name: 'matchdays.show', params: { id: matchdayId } });
                    }
                } else {
                    if (data.errors) {
                        this.errors = data.errors;
                    } else {
                        alert('Error: ' + (data.message || 'No se pudo guardar la jornada'));
                    }
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Error de conexión. Por favor, inténtalo de nuevo.');
            } finally {
                this.submitting = false;
            }
        },

        // Métodos para navegación inteligente
        getBackUrl() {
            if (this.isEditing) {
                // Si está editando, volver al detalle de la jornada
                if (this.fromChampionship) {
                    return { 
                        name: 'matchdays.show', 
                        params: { id: this.matchdayId },
                        query: {
                            from: 'championship',
                            championshipId: this.fromChampionshipId
                        }
                    };
                } else {
                    return { name: 'matchdays.show', params: { id: this.matchdayId } };
                }
            } else {
                // Si está creando, volver a la lista
                if (this.fromChampionship) {
                    return { name: 'championships.show', params: { id: this.fromChampionshipId } };
                }
                return { name: 'matchdays' };
            }
        },
        
        getBackText() {
            if (this.isEditing) {
                return 'Ver Jornada';
            } else if (this.fromChampionship) {
                return 'Campeonato';
            }
            return 'Jornadas';
        }
    },
    
    async mounted() {
        // Verificar si hay datos iniciales del servidor
        if (window.Laravel && window.Laravel.initialData && window.Laravel.initialData.page === 'matchday-edit') {
            // Usar datos iniciales del servidor
            const { matchday, championships, clubs } = window.Laravel.initialData;
            
            this.championships = championships || [];
            this.clubs = clubs || [];
            
            if (matchday) {
                this.form = {
                    championship_id: matchday.championship_id || '',
                    number: matchday.number || '',
                    name: matchday.name || '',
                    description: matchday.description || '',
                    date: matchday.date || '',
                    start_time: matchday.start_time || '',
                    end_time: matchday.end_time || '',
                    venue: matchday.venue || '',
                    address: matchday.address || '',
                    organizer_club_id: matchday.organizer_club_id || '',
                    organizer_name: matchday.organizer_name || '',
                    organizer_contact: matchday.organizer_contact || '',
                    organizer_phone: matchday.organizer_phone || '',
                    entry_fee: matchday.entry_fee || 0,
                    public_registration_enabled: matchday.public_registration_enabled || false,
                    status: matchday.status || 'draft'
                };
            }
        } else {
            // Si no hay datos iniciales, cargar desde API
            await this.loadFormData();
            if (this.isEditing) {
                await this.loadMatchday();
            }
        }
        
        // Pre-seleccionar el campeonato si viene desde ahí
        if (this.fromChampionship && !this.form.championship_id) {
            this.form.championship_id = this.fromChampionshipId;
        }
    }
};
</script>

<style scoped>
.matchday-form {
    max-width: 1200px;
    margin: 0 auto;
}

.card {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.form-group label {
    font-weight: 600;
    color: #495057;
}

.card-header h5,
.card-header h6 {
    color: #495057;
}

@media (max-width: 768px) {
    .card-tools {
        flex-direction: column;
        align-items: stretch;
    }
    
    .card-tools .btn {
        margin-bottom: 0.25rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>
