<template>
    <div class="club-form">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users mr-2"></i>
                    {{ isEditing ? 'Editar Club' : 'Crear Nuevo Club' }}
                </h3>
                <div class="card-tools">
                    <router-link :to="{ name: 'clubs' }" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Clubes
                    </router-link>
                </div>
            </div>

            <div class="card-body">
                <form @submit.prevent="submitForm">
                    <div class="row">
                        <!-- Información básica -->
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Nombre del Club *</label>
                                <input 
                                    id="name"
                                    v-model="form.name" 
                                    type="text" 
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.name }"
                                    required
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
                                    rows="3"
                                    :class="{ 'is-invalid': errors.description }"
                                ></textarea>
                                <div v-if="errors.description" class="invalid-feedback">
                                    {{ errors.description[0] }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">Ciudad</label>
                                        <input 
                                            id="city"
                                            v-model="form.city" 
                                            type="text" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.city }"
                                        >
                                        <div v-if="errors.city" class="invalid-feedback">
                                            {{ errors.city[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="state">Estado/Provincia</label>
                                        <input 
                                            id="state"
                                            v-model="form.state" 
                                            type="text" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.state }"
                                        >
                                        <div v-if="errors.state" class="invalid-feedback">
                                            {{ errors.state[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country">País</label>
                                        <input 
                                            id="country"
                                            v-model="form.country" 
                                            type="text" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.country }"
                                        >
                                        <div v-if="errors.country" class="invalid-feedback">
                                            {{ errors.country[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="founded_year">Año de Fundación</label>
                                        <input 
                                            id="founded_year"
                                            v-model="form.founded_year" 
                                            type="number" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.founded_year }"
                                            min="1900"
                                            :max="currentYear"
                                        >
                                        <div v-if="errors.founded_year" class="invalid-feedback">
                                            {{ errors.founded_year[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="website">Sitio Web</label>
                                <input 
                                    id="website"
                                    v-model="form.website" 
                                    type="url" 
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.website }"
                                    placeholder="https://ejemplo.com"
                                >
                                <div v-if="errors.website" class="invalid-feedback">
                                    {{ errors.website[0] }}
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
                                    <option value="active">Activo</option>
                                    <option value="inactive">Inactivo</option>
                                    <option value="suspended">Suspendido</option>
                                </select>
                                <div v-if="errors.status" class="invalid-feedback">
                                    {{ errors.status[0] }}
                                </div>
                            </div>
                        </div>

                        <!-- Logo -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Logo del Club</label>
                                <div class="text-center">
                                    <div v-if="logoPreview" class="mb-3">
                                        <img 
                                            :src="logoPreview" 
                                            alt="Logo preview" 
                                            class="img-fluid rounded"
                                            style="max-width: 200px; max-height: 200px; object-fit: cover;"
                                        >
                                    </div>
                                    <div v-else-if="form.logo" class="mb-3">
                                        <img 
                                            :src="'/storage/' + form.logo" 
                                            alt="Logo actual" 
                                            class="img-fluid rounded"
                                            style="max-width: 200px; max-height: 200px; object-fit: cover;"
                                        >
                                    </div>
                                    <div v-else class="mb-3">
                                        <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    </div>
                                    
                                    <input 
                                        ref="logoInput"
                                        type="file" 
                                        accept="image/*" 
                                        @change="handleLogoChange"
                                        class="d-none"
                                    >
                                    <button 
                                        type="button" 
                                        @click="$refs.logoInput.click()" 
                                        class="btn btn-outline-primary btn-sm"
                                    >
                                        <i class="fas fa-upload mr-1"></i>
                                        Subir Logo
                                    </button>
                                    <button 
                                        v-if="logoPreview || form.logo" 
                                        type="button" 
                                        @click="removeLogo" 
                                        class="btn btn-outline-danger btn-sm ml-2"
                                    >
                                        <i class="fas fa-trash mr-1"></i>
                                        Quitar
                                    </button>
                                </div>
                                <div v-if="errors.logo" class="invalid-feedback d-block">
                                    {{ errors.logo[0] }}
                                </div>
                                <small class="form-text text-muted">
                                    Formatos: JPG, PNG, GIF. Tamaño máximo: 2MB
                                </small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <router-link :to="{ name: 'clubs' }" class="btn btn-secondary">
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
                        {{ isEditing ? 'Actualizar Club' : 'Crear Club' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ClubForm',
    data() {
        return {
            loading: false,
            logoFile: null,
            logoPreview: null,
            form: {
                name: '',
                description: '',
                city: '',
                state: '',
                country: '',
                founded_year: null,
                website: '',
                status: 'active',
                logo: null
            },
            errors: {}
        };
    },
    computed: {
        isEditing() {
            return !!this.$route.params.id;
        },
        clubId() {
            return this.$route.params.id;
        },
        currentYear() {
            return new Date().getFullYear();
        }
    },
    mounted() {
        if (this.isEditing) {
            this.loadClub();
        }
    },
    methods: {
        async loadClub() {
            this.loading = true;
            try {
                const response = await fetch(`/api/clubs/${this.clubId}`);
                if (!response.ok) {
                    throw new Error('Error al cargar el club');
                }
                const data = await response.json();
                this.form = { ...this.form, ...data };
            } catch (error) {
                console.error('Error loading club:', error);
                this.showNotification('Error al cargar el club', 'error');
                this.$router.push({ name: 'clubs' });
            } finally {
                this.loading = false;
            }
        },

        handleLogoChange(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validar tipo de archivo
            if (!file.type.startsWith('image/')) {
                this.showNotification('Por favor selecciona un archivo de imagen válido', 'error');
                return;
            }

            // Validar tamaño (2MB)
            if (file.size > 2 * 1024 * 1024) {
                this.showNotification('El archivo debe ser menor a 2MB', 'error');
                return;
            }

            this.logoFile = file;

            // Crear preview
            const reader = new FileReader();
            reader.onload = (e) => {
                this.logoPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        removeLogo() {
            this.logoFile = null;
            this.logoPreview = null;
            this.form.logo = null;
            if (this.$refs.logoInput) {
                this.$refs.logoInput.value = '';
            }
        },

        async submitForm() {
            this.loading = true;
            this.errors = {};

            try {
                const formData = new FormData();
                
                // Agregar campos del formulario
                Object.keys(this.form).forEach(key => {
                    if (key !== 'logo' && this.form[key] !== null && this.form[key] !== '') {
                        formData.append(key, this.form[key]);
                    }
                });

                // Agregar logo si hay uno nuevo
                if (this.logoFile) {
                    formData.append('logo', this.logoFile);
                }

                const url = this.isEditing ? `/api/clubs/${this.clubId}` : '/api/clubs';
                const method = this.isEditing ? 'POST' : 'POST';
                
                if (this.isEditing) {
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    throw new Error(data.message || 'Error al guardar el club');
                }

                this.showNotification(
                    this.isEditing ? 'Club actualizado exitosamente' : 'Club creado exitosamente',
                    'success'
                );
                
                this.$router.push({ name: 'clubs' });

            } catch (error) {
                console.error('Error saving club:', error);
                this.showNotification(error.message || 'Error al guardar el club', 'error');
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
.club-form {
    max-width: 800px;
    margin: 0 auto;
}

.form-group label {
    font-weight: 600;
    color: #495057;
}

.invalid-feedback {
    display: block;
}

.btn-group .btn {
    border-radius: 0.25rem;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}
</style>
