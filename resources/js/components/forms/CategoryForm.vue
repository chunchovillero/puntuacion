<template>
    <div class="category-form">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tags mr-2"></i>
                    {{ isEditing ? 'Editar Categoría' : 'Crear Nueva Categoría' }}
                </h3>
                <div class="card-tools">
                    <router-link :to="{ name: 'categories' }" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Volver a Categorías
                    </router-link>
                </div>
            </div>

            <div class="card-body">
                <form @submit.prevent="submitForm">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="name">Nombre de la Categoría *</label>
                                <input 
                                    id="name"
                                    v-model="form.name" 
                                    type="text" 
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.name }"
                                    required
                                    placeholder="Ej: Cruiser 40+, Elite Men, etc."
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
                                    placeholder="Describe los criterios y características de esta categoría..."
                                ></textarea>
                                <div v-if="errors.description" class="invalid-feedback">
                                    {{ errors.description[0] }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="min_age">Edad Mínima</label>
                                        <input 
                                            id="min_age"
                                            v-model.number="form.min_age" 
                                            type="number" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.min_age }"
                                            min="0"
                                            max="100"
                                        >
                                        <div v-if="errors.min_age" class="invalid-feedback">
                                            {{ errors.min_age[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="max_age">Edad Máxima</label>
                                        <input 
                                            id="max_age"
                                            v-model.number="form.max_age" 
                                            type="number" 
                                            class="form-control"
                                            :class="{ 'is-invalid': errors.max_age }"
                                            min="0"
                                            max="100"
                                        >
                                        <div v-if="errors.max_age" class="invalid-feedback">
                                            {{ errors.max_age[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="gender">Género</label>
                                <select 
                                    id="gender"
                                    v-model="form.gender" 
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.gender }"
                                >
                                    <option value="">Mixto</option>
                                    <option value="male">Masculino</option>
                                    <option value="female">Femenino</option>
                                </select>
                                <div v-if="errors.gender" class="invalid-feedback">
                                    {{ errors.gender[0] }}
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
                                    <option value="active">Activa</option>
                                    <option value="inactive">Inactiva</option>
                                </select>
                                <div v-if="errors.status" class="invalid-feedback">
                                    {{ errors.status[0] }}
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input 
                                        id="is_competitive"
                                        v-model="form.is_competitive"
                                        type="checkbox"
                                        class="custom-control-input"
                                    >
                                    <label class="custom-control-label" for="is_competitive">
                                        Categoría competitiva
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Las categorías competitivas participan en campeonatos oficiales
                                </small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Información de Ayuda
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <h6 class="text-primary">Ejemplos de Categorías BMX:</h6>
                                    <ul class="list-unstyled small">
                                        <li><strong>Strider:</strong> 2-4 años</li>
                                        <li><strong>Novice 5-6:</strong> 5-6 años, principiantes</li>
                                        <li><strong>Intermediate 7-8:</strong> 7-8 años, nivel medio</li>
                                        <li><strong>Expert 9-10:</strong> 9-10 años, avanzado</li>
                                        <li><strong>Cruiser 45+:</strong> 45+ años, bicicleta cruiser</li>
                                        <li><strong>Elite Women:</strong> Mujeres élite</li>
                                        <li><strong>Elite Men:</strong> Hombres élite</li>
                                    </ul>
                                    
                                    <hr>
                                    
                                    <h6 class="text-primary">Consejos:</h6>
                                    <ul class="list-unstyled small text-muted">
                                        <li>• Usa nombres claros y descriptivos</li>
                                        <li>• Define rangos de edad apropiados</li>
                                        <li>• Considera el nivel de habilidad</li>
                                        <li>• Marca como competitiva si es oficial</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <router-link :to="{ name: 'categories' }" class="btn btn-secondary">
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
                        {{ isEditing ? 'Actualizar Categoría' : 'Crear Categoría' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'CategoryForm',
    data() {
        return {
            loading: false,
            form: {
                name: '',
                description: '',
                min_age: null,
                max_age: null,
                gender: '',
                status: 'active',
                is_competitive: false
            },
            errors: {}
        };
    },
    computed: {
        isEditing() {
            return !!this.$route.params.id;
        },
        categoryId() {
            return this.$route.params.id;
        }
    },
    mounted() {
        if (this.isEditing) {
            this.loadCategory();
        }
    },
    methods: {
        async loadCategory() {
            this.loading = true;
            try {
                const response = await fetch(`/api/categories/${this.categoryId}`);
                if (!response.ok) {
                    throw new Error('Error al cargar la categoría');
                }
                const data = await response.json();
                this.form = { ...this.form, ...data };
            } catch (error) {
                console.error('Error loading category:', error);
                this.showNotification('Error al cargar la categoría', 'error');
                this.$router.push({ name: 'categories' });
            } finally {
                this.loading = false;
            }
        },

        async submitForm() {
            this.loading = true;
            this.errors = {};

            try {
                const url = this.isEditing ? `/api/categories/${this.categoryId}` : '/api/categories';
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
                    throw new Error(data.message || 'Error al guardar la categoría');
                }

                this.showNotification(
                    this.isEditing ? 'Categoría actualizada exitosamente' : 'Categoría creada exitosamente',
                    'success'
                );
                
                this.$router.push({ name: 'categories' });

            } catch (error) {
                console.error('Error saving category:', error);
                this.showNotification(error.message || 'Error al guardar la categoría', 'error');
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
.category-form {
    max-width: 900px;
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
</style>
