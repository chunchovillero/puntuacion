<template>
    <div class="pilot-form">
        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-user-ninja mr-2"></i>
                            {{ isEdit ? 'Editar' : 'Crear' }} Piloto
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item">
                                <router-link to="/pilotos">Pilotos</router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ isEdit ? 'Editar' : 'Crear' }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    {{ isEdit ? 'Editar' : 'Nuevo' }} Piloto
                                </h3>
                            </div>
                            
                            <form @submit.prevent="submitForm" class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">Nombre *</label>
                                            <input 
                                                v-model="form.first_name"
                                                type="text" 
                                                id="first_name"
                                                class="form-control"
                                                :class="{ 'is-invalid': errors.first_name }"
                                                required
                                            >
                                            <div v-if="errors.first_name" class="invalid-feedback">
                                                {{ errors.first_name[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Apellidos *</label>
                                            <input 
                                                v-model="form.last_name"
                                                type="text" 
                                                id="last_name"
                                                class="form-control"
                                                :class="{ 'is-invalid': errors.last_name }"
                                                required
                                            >
                                            <div v-if="errors.last_name" class="invalid-feedback">
                                                {{ errors.last_name[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rut">RUT *</label>
                                            <input 
                                                v-model="form.rut"
                                                type="text" 
                                                id="rut"
                                                class="form-control"
                                                :class="{ 'is-invalid': errors.rut || rutError }"
                                                required
                                                @blur="validateRut"
                                                placeholder="Ej: 12.345.678-9"
                                            >
                                            <div v-if="errors.rut" class="invalid-feedback">
                                                {{ errors.rut[0] }}
                                            </div>
                                            <div v-else-if="rutError" class="invalid-feedback">
                                                {{ rutError }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="age">Edad</label>
                                            <input 
                                                v-model="form.age"
                                                type="number" 
                                                id="age"
                                                class="form-control"
                                                :class="{ 'is-invalid': errors.age }"
                                            >
                                            <div v-if="errors.age" class="invalid-feedback">
                                                {{ errors.age[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Estado</label>
                                            <select 
                                                v-model="form.status"
                                                id="status"
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

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="club_id">Club</label>
                                            <select 
                                                v-model="form.club_id"
                                                id="club_id"
                                                class="form-control"
                                                :class="{ 'is-invalid': errors.club_id }"
                                            >
                                                <option value="">Seleccionar club...</option>
                                                <option v-for="club in clubs" :key="club.id" :value="club.id">
                                                    {{ club.name }}
                                                </option>
                                            </select>
                                            <div v-if="errors.club_id" class="invalid-feedback">
                                                {{ errors.club_id[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="photo">Foto del Piloto</label>
                                    <input 
                                        @change="handleFileUpload"
                                        type="file" 
                                        id="photo"
                                        class="form-control-file"
                                        accept="image/*"
                                    >
                                    <small class="form-text text-muted">
                                        Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB
                                    </small>
                                </div>

                                <div class="form-group">
                                    <div class="d-flex justify-content-between">
                                        <router-link to="/pilotos" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left mr-1"></i>
                                            Volver
                                        </router-link>
                                        
                                        <button 
                                            type="submit" 
                                            class="btn btn-primary"
                                            :disabled="loading"
                                        >
                                            <i v-if="loading" class="fas fa-spinner fa-spin mr-1"></i>
                                            <i v-else class="fas fa-save mr-1"></i>
                                            {{ isEdit ? 'Actualizar' : 'Crear' }} Piloto
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Vista Previa</h3>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <img 
                                        v-if="form.photo_preview" 
                                        :src="form.photo_preview" 
                                        alt="Vista previa"
                                        class="img-fluid rounded-circle"
                                        style="width: 120px; height: 120px; object-fit: cover;"
                                    >
                                    <div 
                                        v-else
                                        class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                        style="width: 120px; height: 120px;"
                                    >
                                        <i class="fas fa-user fa-3x text-white"></i>
                                    </div>
                                </div>
                                
                                <h4>{{ form.first_name || 'Nombre' }} {{ form.last_name || 'Apellidos' }}</h4>
                                <p class="text-muted">Edad: {{ form.age || 'No especificada' }}</p>
                                
                                <span :class="getStatusClass(form.status)">
                                    {{ getStatusLabel(form.status) }}
                                </span>
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
    name: 'PilotForm',
    data() {
        return {
            loading: false,
            clubs: [],
            form: {
                first_name: '',
                last_name: '',
                rut: '',
                age: '',
                status: 'active',
                club_id: '',
                photo: null,
                photo_preview: null
            },
            errors: {},
            rutError: ''
        };
    },
    computed: {
        isEdit() {
            return !!this.$route.params.id;
        },
        pilotId() {
            return this.$route.params.id;
        }
    },
    async mounted() {
        await this.loadFormData();
        if (this.isEdit) {
            await this.loadPilot();
        }
    },
    methods: {
        async loadFormData() {
            try {
                const [clubsResponse] = await Promise.all([
                    fetch('/api/clubs')
                ]);

                if (clubsResponse.ok) {
                    const clubsData = await clubsResponse.json();
                    this.clubs = clubsData.data || clubsData;
                } 
            } catch (error) {
                console.error('Error loading form data:', error);
            }
        },

        async loadPilot() {
            try {
                this.loading = true;
                const response = await fetch(`/api/pilots/${this.pilotId}`);
                
                if (response.ok) {
                    const data = await response.json();
                    const pilot = data.data || data;
                    
                    this.form = {
                        first_name: pilot.first_name || '',
                        last_name: pilot.last_name || '',
                        age: pilot.age || '',
                        status: pilot.status || 'active',
                        club_id: pilot.club_id || '',
                        photo: null,
                        photo_preview: pilot.photo ? `/storage/${pilot.photo}` : null
                    };
                } else {
                    this.$router.push('/pilotos');
                }
            } catch (error) {
                console.error('Error loading pilot:', error);
                this.$router.push('/pilotos');
            } finally {
                this.loading = false;
            }
        },

        validateRut() {
            if (!this.form.rut) {
                this.rutError = 'El RUT es obligatorio';
                return false;
            }
            if (!this.isValidRut(this.form.rut)) {
                this.rutError = 'El RUT ingresado no es válido';
                return false;
            }
            this.rutError = '';
            return true;
        },
        isValidRut(rut) {
            // Limpia formato
            rut = rut.replace(/[^0-9kK]/g, '').toUpperCase();
            if (rut.length < 8) return false;
            let cuerpo = rut.slice(0, -1);
            let dv = rut.slice(-1);
            let suma = 0, multiplo = 2;
            for (let i = cuerpo.length - 1; i >= 0; i--) {
                suma += parseInt(cuerpo.charAt(i)) * multiplo;
                multiplo = multiplo < 7 ? multiplo + 1 : 2;
            }
            let dvEsperado = 11 - (suma % 11);
            dvEsperado = dvEsperado === 11 ? '0' : dvEsperado === 10 ? 'K' : dvEsperado.toString();
            return dv === dvEsperado;
        },

        async submitForm() {
            this.loading = true;
            this.errors = {};
            if (!this.validateRut()) {
                this.loading = false;
                return;
            }

            try {
                const formData = new FormData();
                
                Object.keys(this.form).forEach(key => {
                    if (key !== 'photo_preview' && this.form[key] !== null && this.form[key] !== '') {
                        formData.append(key, this.form[key]);
                    }
                });

                const url = this.isEdit ? `/api/pilots/${this.pilotId}` : '/api/pilots';
                const method = this.isEdit ? 'PUT' : 'POST';

                // Para PUT con FormData, necesitamos usar POST con _method
                if (this.isEdit) {
                    formData.append('_method', 'PUT');
                }

                const response = await fetch(url, {
                    method: this.isEdit ? 'POST' : 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    console.log('PilotForm: Submit successful, isEdit:', this.isEdit);
                    if (this.isEdit) {
                        // Si es edición, regresar al detalle del piloto con parámetro de éxito
                        const redirectUrl = `/pilotos/${this.pilotId}?updated=true`;
                        console.log('PilotForm: Redirecting to:', redirectUrl);
                        this.$router.push(redirectUrl);
                    } else {
                        // Si es creación, ir a la lista de pilotos
                        console.log('PilotForm: Redirecting to pilots list');
                        this.$router.push('/pilotos');
                        this.showSuccessMessage('Piloto creado exitosamente');
                    }
                } else {
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                }
            } catch (error) {
                console.error('Error submitting form:', error);
            } finally {
                this.loading = false;
            }
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.form.photo = file;
                
                // Crear preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.form.photo_preview = e.target.result;
                };
                reader.readAsDataURL(file);
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

        showSuccessMessage(message) {
            // Crear notificación temporal
            const notification = document.createElement('div');
            notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <i class="fas fa-check-circle mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            `;
            
            document.body.appendChild(notification);
            
            // Auto-remover después de 4 segundos
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 4000);
        }
    }
};
</script>

<style scoped>
.pilot-form {
    padding: 0;
}

.content-header {
    padding: 15px 0;
}

.card {
    border: none;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.form-group label {
    font-weight: 600;
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
}
</style>
