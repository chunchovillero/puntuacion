<template>
    <div class="pilot-detail">
        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-user-ninja mr-2"></i>
                            Detalle del Piloto
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item">
                                <router-link :to="getBackUrl()">{{ getBackText() }}</router-link>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ pilot.first_name }} {{ pilot.last_name }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <section class="content">
            <div class="container-fluid">
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-3">Cargando información del piloto...</p>
                </div>

                <div v-else-if="pilot.id" class="row">
                    <!-- Información Principal -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <img 
                                    v-if="pilot.photo" 
                                    :src="'/storage/' + pilot.photo" 
                                    :alt="'Foto de ' + pilot.first_name + ' ' + pilot.last_name"
                                    class="img-fluid rounded-circle mb-3"
                                    style="width: 150px; height: 150px; object-fit: cover;"
                                >
                                <div 
                                    v-else
                                    class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                    style="width: 150px; height: 150px;"
                                >
                                    <i class="fas fa-user fa-4x text-white"></i>
                                </div>
                                
                                <h3>{{ pilot.first_name }} {{ pilot.last_name }}</h3>
                                
                                <span :class="getStatusClass(pilot.status)">
                                    {{ getStatusLabel(pilot.status) }}
                                </span>

                                <div class="mt-3" v-if="canEdit">
                                    <router-link 
                                        :to="{ name: 'pilots.edit', params: { id: pilot.id } }" 
                                        class="btn btn-warning btn-sm mr-2"
                                    >
                                        <i class="fas fa-edit"></i> Editar
                                    </router-link>
                                    
                                    <!-- Mostrar botón de desactivar solo si el piloto está activo -->
                                    <button 
                                        @click="confirmDelete" 
                                        class="btn btn-outline-warning btn-sm mr-2"
                                        v-if="canDelete && pilot.status === 'active'"
                                    >
                                        <i class="fas fa-user-slash"></i> Desactivar
                                    </button>
                                    
                                    <!-- Mostrar botón de reactivar solo si el piloto está inactivo -->
                                    <button 
                                        @click="confirmReactivate" 
                                        class="btn btn-outline-success btn-sm mr-2"
                                        v-if="canDelete && pilot.status === 'inactive'"
                                    >
                                        <i class="fas fa-user-check"></i> Reactivar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información Detallada -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Información del Piloto</h3>
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-3">Nombre completo:</dt>
                                    <dd class="col-sm-9">{{ pilot.first_name }} {{ pilot.last_name }}</dd>

                                    <dt class="col-sm-3">Edad:</dt>
                                    <dd class="col-sm-9">{{ pilot.age || 'No especificada' }}</dd>

                                    <dt class="col-sm-3">Club:</dt>
                                    <dd class="col-sm-9">
                                        <span v-if="pilot.club">
                                            <i class="fas fa-flag mr-1"></i>
                                            {{ pilot.club.name }}
                                        </span>
                                        <span v-else class="text-muted">Sin club asignado</span>
                                    </dd>

                                    <dt class="col-sm-3">Categoría:</dt>
                                    <dd class="col-sm-9">
                                        <span v-if="pilot.category">
                                            <i class="fas fa-tags mr-1"></i>
                                            {{ pilot.category.name }}
                                        </span>
                                        <span v-else class="text-muted">Sin categoría asignada</span>
                                    </dd>

                                    <dt class="col-sm-3">Estado:</dt>
                                    <dd class="col-sm-9">
                                        <span :class="getStatusClass(pilot.status)">
                                            {{ getStatusLabel(pilot.status) }}
                                        </span>
                                    </dd>

                                    <dt class="col-sm-3">Fecha de registro:</dt>
                                    <dd class="col-sm-9">{{ formatDate(pilot.created_at) }}</dd>

                                    <dt class="col-sm-3">Última actualización:</dt>
                                    <dd class="col-sm-9">{{ formatDate(pilot.updated_at) }}</dd>
                                </dl>
                            </div>
                        </div>

                        <!-- Historial de Competiciones (si existe) -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h3 class="card-title">Historial de Competiciones</h3>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Esta funcionalidad estará disponible próximamente.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón de volver -->
                <div class="row mt-4">
                    <div class="col-12">
                        <router-link :to="getBackUrl()" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>
                            {{ getBackButtonText() }}
                        </router-link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    name: 'PilotDetail',
    data() {
        return {
            loading: false,
            pilot: {}
        };
    },
    computed: {
        pilotId() {
            return this.$route.params.id;
        },
        canEdit() {
            return window.Laravel?.user?.authenticated;
        },
        canDelete() {
            return window.Laravel?.user?.authenticated;
        },
        // Detectar desde dónde viene el usuario
        fromClub() {
            return this.$route.query.from === 'club' && this.$route.query.clubId;
        },
        fromClubId() {
            return this.$route.query.clubId;
        },
        fromMatchday() {
            return this.$route.query.from === 'matchday' && this.$route.query.matchdayId;
        },
        fromMatchdayId() {
            return this.$route.query.matchdayId;
        }
    },
    watch: {
        '$route': {
            handler(to, from) {
                console.log('PilotDetail: Route changed from', from?.fullPath, 'to', to.fullPath);
                console.log('PilotDetail: Query params:', to.query);
                
                // Si viene con parámetro updated, mostrar mensaje
                if (to.query.updated === 'true') {
                    console.log('PilotDetail: Detected updated parameter in route change');
                    // Usar nextTick para asegurar que el DOM esté actualizado
                    this.$nextTick(() => {
                        this.showSuccessMessage('Piloto actualizado exitosamente');
                    });
                    // Limpiar el parámetro de la URL
                    this.$router.replace({ path: to.path });
                }
                
                // Recargar datos cuando cambia la ruta (ej: al volver de edición)
                if (to.params.id !== from?.params.id) {
                    console.log('PilotDetail: Pilot ID changed, reloading data');
                    this.loadPilot();
                }
            },
            immediate: true
        }
    },
    async mounted() {
        console.log('PilotDetail mounted, pilotId:', this.pilotId);
        console.log('PilotDetail: Route query params:', this.$route.query);
        
        await this.loadPilot();
        
        // La detección de updated=true se maneja en el watcher con immediate:true
        // para evitar duplicaciones
    },
    methods: {
        async loadPilot() {
            this.loading = true;
            console.log('PilotDetail: Loading pilot with ID:', this.pilotId);
            try {
                const apiUrl = `/api/pilots/${this.pilotId}`;
                console.log('PilotDetail: Fetching from URL:', apiUrl);
                const response = await fetch(apiUrl);
                
                console.log('PilotDetail: Response status:', response.status);
                console.log('PilotDetail: Response ok:', response.ok);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('PilotDetail: API Response data:', data);
                    this.pilot = data.data || data;
                    console.log('PilotDetail: Pilot loaded:', this.pilot);
                } else {
                    console.log('PilotDetail: Response not ok, redirecting to pilots list');
                    this.$router.push('/pilotos');
                }
            } catch (error) {
                console.error('Error loading pilot:', error);
                this.$router.push('/pilotos');
            } finally {
                this.loading = false;
            }
        },

        async confirmDelete() {
            if (confirm(`¿Estás seguro de desactivar al piloto "${this.pilot.first_name} ${this.pilot.last_name}"? El piloto quedará inactivo pero no se eliminará de la base de datos.`)) {
                await this.deletePilot();
            }
        },

        async deletePilot() {
            try {
                const response = await fetch(`/api/pilots/${this.pilot.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                });

                if (response.ok) {
                    this.showSuccessMessage('Piloto desactivado exitosamente');
                    // Recargar los datos del piloto para actualizar el estado
                    await this.loadPilot();
                } else {
                    alert('Error al desactivar el piloto');
                }
            } catch (error) {
                console.error('Error deleting pilot:', error);
                alert('Error al eliminar el piloto');
            }
        },

        async confirmReactivate() {
            if (confirm(`¿Estás seguro de reactivar al piloto "${this.pilot.first_name} ${this.pilot.last_name}"? El piloto volverá a estar activo.`)) {
                await this.reactivatePilot();
            }
        },

        async reactivatePilot() {
            try {
                const response = await fetch(`/api/pilots/${this.pilot.id}/reactivate`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                });

                if (response.ok) {
                    this.showSuccessMessage('Piloto reactivado exitosamente');
                    // Recargar los datos del piloto para actualizar el estado
                    await this.loadPilot();
                } else {
                    alert('Error al reactivar el piloto');
                }
            } catch (error) {
                console.error('Error reactivating pilot:', error);
                alert('Error al reactivar el piloto');
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

        formatDate(dateString) {
            if (!dateString) return 'No disponible';
            
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        // Métodos para navegación inteligente
        getBackUrl() {
            if (this.fromClub) {
                return `/clubes/${this.fromClubId}`;
            }
            if (this.fromMatchday) {
                return `/jornadas/${this.fromMatchdayId}`;
            }
            return '/pilotos';
        },
        
        getBackText() {
            if (this.fromClub) {
                return 'Club';
            }
            if (this.fromMatchday) {
                return 'Jornada';
            }
            return 'Pilotos';
        },
        
        getBackButtonText() {
            if (this.fromClub) {
                return 'Volver al club';
            }
            if (this.fromMatchday) {
                return 'Volver a la jornada';
            }
            return 'Volver a la lista de pilotos';
        },

        showSuccessMessage(message) {
            console.log('PilotDetail: Showing success message:', message);
            
            // Remover cualquier notificación existente
            const existingNotifications = document.querySelectorAll('.pilot-success-notification');
            existingNotifications.forEach(notification => notification.remove());
            
            // Crear notificación temporal
            const notification = document.createElement('div');
            notification.className = 'alert alert-success alert-dismissible fade show position-fixed pilot-success-notification';
            notification.style.cssText = `
                top: 20px; 
                right: 20px; 
                z-index: 10000; 
                min-width: 300px; 
                max-width: 500px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                border: none;
                font-weight: 500;
            `;
            notification.innerHTML = `
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-check-circle" style="color: #28a745; margin-right: 8px; font-size: 18px;"></i>
                    <span>${message}</span>
                    <button type="button" class="close" onclick="this.parentElement.parentElement.remove()" style="margin-left: auto; padding: 0 8px;">
                        <span style="font-size: 18px;">&times;</span>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            console.log('PilotDetail: Notification added to DOM with improved styling');
            
            // Forzar un reflow para asegurar que se muestre
            notification.offsetHeight;
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                            console.log('PilotDetail: Notification auto-removed');
                        }
                    }, 300);
                }
            }, 5000);
        }
    }
};
</script>

<style scoped>
.pilot-detail {
    padding: 0;
}

.content-header {
    padding: 15px 0;
}

.card {
    border: none;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.dl-horizontal dt {
    text-align: left;
}
</style>
