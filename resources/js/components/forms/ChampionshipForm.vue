<template>
    <div class="championship-form">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy mr-2"></i>
                    {{ isEditing ? 'Editar Campeonato' : 'Crear Nuevo Campeonato' }}
                </h3>
                <div class="card-tools">
                    <router-link :to="{ name: 'championships' }" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Campeonatos
                    </router-link>
                </div>
            </div>

            <div class="card-body">
                <form @submit.prevent="submitForm">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Nombre del Campeonato *</label>
                                <input 
                                    id="name"
                                    v-model="form.name" 
                                    type="text" 
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.name }"
                                    required
                                    placeholder="Ej: Campeonato Nacional BMX 2025"
                                >
                                <div v-if="errors.name" class="invalid-feedback">
                                    {{ errors.name[0] }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Descripción</label>
                                <textarea 
                                    id="description"
                                    v-model="form.description"
                                    class="form-control"
                                    rows="4"
                                    :class="{ 'is-invalid': errors.description }"
                                    placeholder="Describe el campeonato, sus características y objetivos..."
                                ></textarea>
                                <div v-if="errors.description" class="invalid-feedback">
                                    {{ errors.description[0] }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="year">Año *</label>
                                        <select 
                                            id="year"
                                            v-model.number="form.year" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.year }"
                                            required
                                        >
                                            <option value="">Seleccionar año</option>
                                            <option v-for="year in availableYears" :key="year" :value="year">
                                                {{ year }}
                                            </option>
                                        </select>
                                        <div v-if="errors.year" class="invalid-feedback">
                                            {{ errors.year[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="season">Temporada</label>
                                        <input 
                                            id="season"
                                            v-model="form.season" 
                                            type="text" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.season }"
                                            placeholder="Ej: Primavera, Verano, Anual"
                                        >
                                        <div v-if="errors.season" class="invalid-feedback">
                                            {{ errors.season[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="type">Tipo de Campeonato</label>
                                        <select 
                                            id="type"
                                            v-model="form.type" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.type }"
                                        >
                                            <option value="local">Local</option>
                                            <option value="regional">Regional</option>
                                            <option value="national">Nacional</option>
                                            <option value="international">Internacional</option>
                                        </select>
                                        <div v-if="errors.type" class="invalid-feedback">
                                            {{ errors.type[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Fecha de Inicio</label>
                                        <input 
                                            id="start_date"
                                            v-model="form.start_date" 
                                            type="date" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.start_date }"
                                        >
                                        <div v-if="errors.start_date" class="invalid-feedback">
                                            {{ errors.start_date[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date">Fecha de Finalización</label>
                                        <input 
                                            id="end_date"
                                            v-model="form.end_date" 
                                            type="date" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.end_date }"
                                        >
                                        <div v-if="errors.end_date" class="invalid-feedback">
                                            {{ errors.end_date[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="registration_start">Inicio de Inscripciones</label>
                                        <input 
                                            id="registration_start"
                                            v-model="form.registration_start" 
                                            type="date" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.registration_start }"
                                        >
                                        <div v-if="errors.registration_start" class="invalid-feedback">
                                            {{ errors.registration_start[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="registration_end">Fin de Inscripciones</label>
                                        <input 
                                            id="registration_end"
                                            v-model="form.registration_end" 
                                            type="date" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.registration_end }"
                                        >
                                        <div v-if="errors.registration_end" class="invalid-feedback">
                                            {{ errors.registration_end[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="status">Estado</label>
                                <select 
                                    id="status"
                                    v-model="form.status" 
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.status }"
                                >
                                    <option value="draft">Borrador</option>
                                    <option value="planned">Planificado</option>
                                    <option value="registration_open">Inscripciones Abiertas</option>
                                    <option value="registration_closed">Inscripciones Cerradas</option>
                                    <option value="active">En Curso</option>
                                    <option value="completed">Finalizado</option>
                                    <option value="cancelled">Cancelado</option>
                                </select>
                                <div v-if="errors.status" class="invalid-feedback">
                                    {{ errors.status[0] }}
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input 
                                        id="is_public"
                                        v-model="form.is_public"
                                        type="checkbox"
                                        class="custom-control-input"
                                    >
                                    <label class="custom-control-label" for="is_public">
                                        Campeonato público
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Los campeonatos públicos son visibles para todos los usuarios
                                </small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input 
                                        id="allow_external_registration"
                                        v-model="form.allow_external_registration"
                                        type="checkbox"
                                        class="custom-control-input"
                                    >
                                    <label class="custom-control-label" for="allow_external_registration">
                                        Permitir inscripción externa
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Permite que pilotos no registrados se inscriban directamente
                                </small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Estados del Campeonato
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <dl class="row small">
                                        <dt class="col-5">Borrador:</dt>
                                        <dd class="col-7">En preparación</dd>
                                        
                                        <dt class="col-5">Planificado:</dt>
                                        <dd class="col-7">Listo, pero sin inscripciones</dd>
                                        
                                        <dt class="col-5">Inscripciones Abiertas:</dt>
                                        <dd class="col-7">Aceptando participantes</dd>
                                        
                                        <dt class="col-5">Inscripciones Cerradas:</dt>
                                        <dd class="col-7">Sin nuevas inscripciones</dd>
                                        
                                        <dt class="col-5">En Curso:</dt>
                                        <dd class="col-7">Campeonato activo</dd>
                                        
                                        <dt class="col-5">Finalizado:</dt>
                                        <dd class="col-7">Campeonato completado</dd>
                                        
                                        <dt class="col-5">Cancelado:</dt>
                                        <dd class="col-7">Suspendido definitivamente</dd>
                                    </dl>
                                </div>
                            </div>

                            <div class="card bg-light mt-3">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-lightbulb mr-1"></i>
                                        Consejos
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled small text-muted">
                                        <li>• Define fechas claras y realistas</li>
                                        <li>• Configura el período de inscripciones adecuadamente</li>
                                        <li>• Usa un nombre descriptivo y único</li>
                                        <li>• La descripción ayuda a los participantes</li>
                                        <li>• Revisa el estado según el momento del campeonato</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <router-link :to="{ name: 'championships' }" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i>
                        Cancelar
                    </router-link>
                    <button 
                        type="submit" 
                        @click="submitForm"
                        :disabled="loading" 
                        class="btn btn-primary"
                    >
                        <i v-if="loading" class="fas fa-spinner fa-spin mr-1"></i>
                        <i v-else class="fas fa-save mr-1"></i>
                        {{ isEditing ? 'Actualizar Campeonato' : 'Crear Campeonato' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ChampionshipForm',
    data() {
        return {
            loading: false,
            form: {
                name: '',
                description: '',
                year: new Date().getFullYear(),
                season: '',
                type: 'local',
                start_date: '',
                end_date: '',
                registration_start: '',
                registration_end: '',
                status: 'draft',
                is_public: true,
                allow_external_registration: false
            },
            errors: {}
        };
    },
    computed: {
        isEditing() {
            return !!this.$route.params.id;
        },
        championshipId() {
            return this.$route.params.id;
        },
        availableYears() {
            const currentYear = new Date().getFullYear();
            const years = [];
            for (let year = currentYear + 2; year >= currentYear - 10; year--) {
                years.push(year);
            }
            return years;
        }
    },
    mounted() {
        if (this.isEditing) {
            this.loadChampionship();
        }
    },
    methods: {
        async loadChampionship() {
            this.loading = true;
            try {
                const response = await fetch(`/api/championships/${this.championshipId}`);
                if (!response.ok) {
                    throw new Error('Error al cargar el campeonato');
                }
                const data = await response.json();
                this.form = { ...this.form, ...data };
            } catch (error) {
                console.error('Error loading championship:', error);
                this.showNotification('Error al cargar el campeonato', 'error');
                this.$router.push({ name: 'championships' });
            } finally {
                this.loading = false;
            }
        },

        async submitForm() {
            this.loading = true;
            this.errors = {};

            try {
                const url = this.isEditing ? `/api/championships/${this.championshipId}` : '/api/championships';
                const method = this.isEditing ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    throw new Error(data.message || 'Error al guardar el campeonato');
                }

                this.showNotification(
                    this.isEditing ? 'Campeonato actualizado exitosamente' : 'Campeonato creado exitosamente',
                    'success'
                );
                
                this.$router.push({ name: 'championships' });

            } catch (error) {
                console.error('Error saving championship:', error);
                this.showNotification(error.message || 'Error al guardar el campeonato', 'error');
            } finally {
                this.loading = false;
            }
        },

        showNotification(message, type = 'info') {
            console.log(`${type.toUpperCase()}: ${message}`);
            if (type === 'error') {
                alert('Error: ' + message);
            }
        }
    }
};
</script>

<style scoped>
.championship-form {
    max-width: 1000px;
    margin: 0 auto;
}

.form-group label {
    font-weight: 600;
    color: #495057;
}

.invalid-feedback {
    display: block;
}

.custom-control-label {
    font-weight: 600;
}

.card-header h6 {
    font-weight: 600;
}

.list-unstyled li {
    margin-bottom: 0.25rem;
}

.small {
    font-size: 0.875rem;
}
</style>
