<template>
  <div class="container-fluid">
    <!-- Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">
              <i class="fas fa-users text-primary"></i>
              Gestión de Usuarios
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
              <li class="breadcrumb-item active">Usuarios</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-users mr-2"></i>
                  Lista de Usuarios del Sistema
                </h3>
                <div class="card-tools">
                  <div class="btn-group mr-2" role="group">
                    <button 
                      type="button" 
                      class="btn btn-outline-primary btn-sm"
                      :class="{ active: viewMode === 'table' }"
                      @click="viewMode = 'table'"
                    >
                      <i class="fas fa-table"></i> Tabla
                    </button>
                    <button 
                      type="button" 
                      class="btn btn-outline-primary btn-sm"
                      :class="{ active: viewMode === 'cards' }"
                      @click="viewMode = 'cards'"
                    >
                      <i class="fas fa-th-large"></i> Cards
                    </button>
                  </div>
                  <button @click="showCreateModal" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                  </button>
                </div>
              </div>

              <!-- Search and Filters -->
              <div class="card-body border-bottom">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Buscar usuarios:</label>
                      <input 
                        v-model="searchQuery" 
                        @input="searchUsers"
                        type="text" 
                        class="form-control" 
                        placeholder="Nombre o email..."
                      >
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Estado de verificación:</label>
                      <select v-model="filters.verification" @change="loadUsers" class="form-control">
                        <option value="">Todos</option>
                        <option value="verified">Verificados</option>
                        <option value="unverified">No verificados</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Ordenar por:</label>
                      <select v-model="sortBy" @change="loadUsers" class="form-control">
                        <option value="name">Nombre</option>
                        <option value="email">Email</option>
                        <option value="created_at">Fecha de registro</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>Orden:</label>
                      <select v-model="sortOrder" @change="loadUsers" class="form-control">
                        <option value="asc">Ascendente</option>
                        <option value="desc">Descendente</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Loading State -->
              <div v-if="loading" class="card-body text-center">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-2">Cargando usuarios...</p>
              </div>

              <!-- Table View -->
              <div v-else-if="viewMode === 'table'" class="card-body table-responsive p-0">
                <table v-if="users.length > 0" class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Email</th>
                      <th>Verificado</th>
                      <th>Registrado</th>
                      <th>Último acceso</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="user in users" :key="user.id">
                      <td>{{ user.id }}</td>
                      <td>
                        <strong>{{ user.name }}</strong>
                        <span v-if="isCurrentUser(user.id)" class="badge badge-success ml-1">Tú</span>
                      </td>
                      <td>{{ user.email }}</td>
                      <td>
                        <span v-if="user.email_verified_at" class="badge badge-success">
                          <i class="fas fa-check"></i> Verificado
                        </span>
                        <span v-else class="badge badge-warning">
                          <i class="fas fa-clock"></i> Pendiente
                        </span>
                      </td>
                      <td>{{ formatDate(user.created_at) }}</td>
                      <td>{{ formatDate(user.last_login_at) || 'Nunca' }}</td>
                      <td>
                        <div class="btn-group">
                          <button @click="viewUser(user)" class="btn btn-info btn-sm" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                          </button>
                          <button @click="editUser(user)" class="btn btn-warning btn-sm" title="Editar">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button 
                            v-if="!isCurrentUser(user.id)" 
                            @click="deleteUser(user)" 
                            class="btn btn-danger btn-sm" 
                            title="Eliminar"
                          >
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div v-else class="text-center py-4">
                  <i class="fas fa-users fa-3x text-muted mb-3"></i>
                  <h5 class="text-muted">No se encontraron usuarios</h5>
                  <p class="text-muted">{{ searchQuery ? 'Intenta con otros términos de búsqueda' : 'Comienza creando el primer usuario del sistema' }}</p>
                </div>
              </div>

              <!-- Cards View -->
              <div v-else-if="viewMode === 'cards'" class="card-body">
                <div v-if="users.length > 0" class="row">
                  <div v-for="user in users" :key="user.id" class="col-md-6 col-lg-4 mb-3">
                    <div class="card card-outline" :class="isCurrentUser(user.id) ? 'card-success' : 'card-primary'">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                          <h5 class="card-title mb-0">
                            {{ user.name }}
                            <span v-if="isCurrentUser(user.id)" class="badge badge-success ml-1">Tú</span>
                          </h5>
                          <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                              <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                              <button @click="viewUser(user)" class="dropdown-item">
                                <i class="fas fa-eye mr-2"></i> Ver detalles
                              </button>
                              <button @click="editUser(user)" class="dropdown-item">
                                <i class="fas fa-edit mr-2"></i> Editar
                              </button>
                              <div v-if="!isCurrentUser(user.id)" class="dropdown-divider"></div>
                              <button 
                                v-if="!isCurrentUser(user.id)" 
                                @click="deleteUser(user)" 
                                class="dropdown-item text-danger"
                              >
                                <i class="fas fa-trash mr-2"></i> Eliminar
                              </button>
                            </div>
                          </div>
                        </div>
                        <p class="card-text">
                          <i class="fas fa-envelope mr-1"></i>{{ user.email }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                          <span v-if="user.email_verified_at" class="badge badge-success">
                            <i class="fas fa-check"></i> Verificado
                          </span>
                          <span v-else class="badge badge-warning">
                            <i class="fas fa-clock"></i> Pendiente
                          </span>
                          <small class="text-muted">{{ formatDate(user.created_at) }}</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-4">
                  <i class="fas fa-users fa-3x text-muted mb-3"></i>
                  <h5 class="text-muted">No se encontraron usuarios</h5>
                  <p class="text-muted">{{ searchQuery ? 'Intenta con otros términos de búsqueda' : 'Comienza creando el primer usuario del sistema' }}</p>
                </div>
              </div>

              <!-- Pagination -->
              <div v-if="pagination.total > pagination.per_page" class="card-footer">
                <nav>
                  <ul class="pagination justify-content-center mb-0">
                    <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                      <button @click="changePage(pagination.current_page - 1)" class="page-link" :disabled="pagination.current_page === 1">
                        Anterior
                      </button>
                    </li>
                    <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: page === pagination.current_page }">
                      <button @click="changePage(page)" class="page-link">{{ page }}</button>
                    </li>
                    <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                      <button @click="changePage(pagination.current_page + 1)" class="page-link" :disabled="pagination.current_page === pagination.last_page">
                        Siguiente
                      </button>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>

            <!-- Statistics Card -->
            <div class="row mt-3">
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Total Usuarios</span>
                    <span class="info-box-number">{{ statistics.total || 0 }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-success"><i class="fas fa-user-check"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Verificados</span>
                    <span class="info-box-number">{{ statistics.verified || 0 }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-warning"><i class="fas fa-user-clock"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Pendientes</span>
                    <span class="info-box-number">{{ statistics.unverified || 0 }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-primary"><i class="fas fa-user-plus"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Este mes</span>
                    <span class="info-box-number">{{ statistics.this_month || 0 }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Create/Edit User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ editingUser ? 'Editar Usuario' : 'Nuevo Usuario' }}
            </h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveUser">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nombre <span class="text-danger">*</span></label>
                    <input 
                      v-model="userForm.name" 
                      type="text" 
                      class="form-control" 
                      :class="{ 'is-invalid': errors.name }"
                      required
                    >
                    <div v-if="errors.name" class="invalid-feedback">{{ errors.name[0] }}</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <input 
                      v-model="userForm.email" 
                      type="email" 
                      class="form-control" 
                      :class="{ 'is-invalid': errors.email }"
                      required
                    >
                    <div v-if="errors.email" class="invalid-feedback">{{ errors.email[0] }}</div>
                  </div>
                </div>
              </div>
              <div class="row" v-if="!editingUser">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Contraseña <span class="text-danger">*</span></label>
                    <input 
                      v-model="userForm.password" 
                      type="password" 
                      class="form-control" 
                      :class="{ 'is-invalid': errors.password }"
                      :required="!editingUser"
                    >
                    <div v-if="errors.password" class="invalid-feedback">{{ errors.password[0] }}</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Confirmar Contraseña <span class="text-danger">*</span></label>
                    <input 
                      v-model="userForm.password_confirmation" 
                      type="password" 
                      class="form-control"
                      :required="!editingUser"
                    >
                  </div>
                </div>
              </div>
              <div class="row" v-if="editingUser">
                <div class="col-12">
                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input 
                        v-model="userForm.change_password" 
                        type="checkbox" 
                        class="custom-control-input" 
                        id="changePassword"
                      >
                      <label class="custom-control-label" for="changePassword">
                        Cambiar contraseña
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row" v-if="editingUser && userForm.change_password">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nueva Contraseña <span class="text-danger">*</span></label>
                    <input 
                      v-model="userForm.password" 
                      type="password" 
                      class="form-control" 
                      :class="{ 'is-invalid': errors.password }"
                    >
                    <div v-if="errors.password" class="invalid-feedback">{{ errors.password[0] }}</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                    <input 
                      v-model="userForm.password_confirmation" 
                      type="password" 
                      class="form-control"
                    >
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button @click="saveUser" type="button" class="btn btn-primary" :disabled="saving">
              <i v-if="saving" class="fas fa-spinner fa-spin mr-1"></i>
              {{ editingUser ? 'Actualizar' : 'Crear' }} Usuario
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detalles del Usuario</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body" v-if="selectedUser">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-borderless">
                  <tr>
                    <th>ID:</th>
                    <td>{{ selectedUser.id }}</td>
                  </tr>
                  <tr>
                    <th>Nombre:</th>
                    <td>{{ selectedUser.name }}</td>
                  </tr>
                  <tr>
                    <th>Email:</th>
                    <td>{{ selectedUser.email }}</td>
                  </tr>
                  <tr>
                    <th>Estado:</th>
                    <td>
                      <span v-if="selectedUser.email_verified_at" class="badge badge-success">
                        <i class="fas fa-check"></i> Verificado
                      </span>
                      <span v-else class="badge badge-warning">
                        <i class="fas fa-clock"></i> Pendiente
                      </span>
                    </td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-borderless">
                  <tr>
                    <th>Registrado:</th>
                    <td>{{ formatDate(selectedUser.created_at) }}</td>
                  </tr>
                  <tr>
                    <th>Actualizado:</th>
                    <td>{{ formatDate(selectedUser.updated_at) }}</td>
                  </tr>
                  <tr>
                    <th>Último acceso:</th>
                    <td>{{ formatDate(selectedUser.last_login_at) || 'Nunca' }}</td>
                  </tr>
                  <tr v-if="selectedUser.email_verified_at">
                    <th>Verificado:</th>
                    <td>{{ formatDate(selectedUser.email_verified_at) }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button @click="editUser(selectedUser)" type="button" class="btn btn-warning">
              <i class="fas fa-edit"></i> Editar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserManager',
  data() {
    return {
      users: [],
      loading: true,
      saving: false,
      searchQuery: '',
      viewMode: 'table',
      sortBy: 'name',
      sortOrder: 'asc',
      filters: {
        verification: ''
      },
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 20,
        total: 0
      },
      statistics: {},
      editingUser: null,
      selectedUser: null,
      userForm: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        change_password: false
      },
      errors: {},
      currentUserId: null,
      searchTimeout: null
    }
  },
  computed: {
    visiblePages() {
      const pages = [];
      const current = this.pagination.current_page;
      const last = this.pagination.last_page;
      
      for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
        pages.push(i);
      }
      
      return pages;
    }
  },
  async mounted() {
    await this.loadCurrentUser();
    await this.loadUsers();
    await this.loadStatistics();
  },
  methods: {
    async loadCurrentUser() {
      try {
        const response = await axios.get('/api/user');
        this.currentUserId = response.data.id;
      } catch (error) {
        console.error('Error loading current user:', error);
      }
    },

    async loadUsers(page = 1) {
      this.loading = true;
      try {
        const params = {
          page: page,
          search: this.searchQuery,
          sort_by: this.sortBy,
          sort_order: this.sortOrder,
          verification: this.filters.verification
        };

        const response = await axios.get('/gestionar/api/usuarios', { params });
        
        if (response.data.success) {
          this.users = response.data.data.data;
          this.pagination = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            per_page: response.data.data.per_page,
            total: response.data.data.total
          };
        }
      } catch (error) {
        console.error('Error loading users:', error);
        this.showNotification('Error al cargar usuarios', 'error');
      } finally {
        this.loading = false;
      }
    },

    async loadStatistics() {
      try {
        const response = await axios.get('/gestionar/api/usuarios/estadisticas');
        if (response.data.success) {
          this.statistics = response.data.data;
        }
      } catch (error) {
        console.error('Error loading statistics:', error);
      }
    },

    searchUsers() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.loadUsers(1);
      }, 300);
    },

    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.loadUsers(page);
      }
    },

    showCreateModal() {
      this.editingUser = null;
      this.userForm = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        change_password: false
      };
      this.errors = {};
      $('#userModal').modal('show');
    },

    editUser(user) {
      this.editingUser = user;
      this.userForm = {
        name: user.name,
        email: user.email,
        password: '',
        password_confirmation: '',
        change_password: false
      };
      this.errors = {};
      $('#viewUserModal').modal('hide');
      $('#userModal').modal('show');
    },

    viewUser(user) {
      this.selectedUser = user;
      $('#viewUserModal').modal('show');
    },

    async saveUser() {
      this.saving = true;
      this.errors = {};

      try {
        const url = this.editingUser 
          ? `/gestionar/api/usuarios/${this.editingUser.id}`
          : '/gestionar/api/usuarios';
        
        const method = this.editingUser ? 'put' : 'post';
        
        const formData = { ...this.userForm };
        if (this.editingUser && !formData.change_password) {
          delete formData.password;
          delete formData.password_confirmation;
        }
        delete formData.change_password;

        const response = await axios[method](url, formData);
        
        if (response.data.success) {
          this.showNotification(
            this.editingUser ? 'Usuario actualizado exitosamente' : 'Usuario creado exitosamente',
            'success'
          );
          $('#userModal').modal('hide');
          await this.loadUsers(this.pagination.current_page);
          await this.loadStatistics();
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          this.showNotification('Error al guardar usuario', 'error');
        }
      } finally {
        this.saving = false;
      }
    },

    async deleteUser(user) {
      if (!confirm(`¿Estás seguro de eliminar al usuario "${user.name}"?`)) {
        return;
      }

      try {
        const response = await axios.delete(`/gestionar/api/usuarios/${user.id}`);
        
        if (response.data.success) {
          this.showNotification('Usuario eliminado exitosamente', 'success');
          await this.loadUsers(this.pagination.current_page);
          await this.loadStatistics();
        }
      } catch (error) {
        this.showNotification('Error al eliminar usuario', 'error');
      }
    },

    isCurrentUser(userId) {
      return userId === this.currentUserId;
    },

    formatDate(date) {
      if (!date) return null;
      return new Date(date).toLocaleString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    showNotification(message, type = 'info') {
      // Implementar sistema de notificaciones
      const alertClass = type === 'error' ? 'danger' : type;
      const notification = $(`
        <div class="alert alert-${alertClass} alert-dismissible fade show" role="alert">
          ${message}
          <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      `);
      
      $('.content-header').after(notification);
      
      setTimeout(() => {
        notification.fadeOut();
      }, 5000);
    }
  }
}
</script>

<style scoped>
.info-box {
  margin-bottom: 15px;
}

.modal-lg {
  max-width: 800px;
}

.pagination {
  margin: 0;
}

.btn-group .btn {
  margin-right: 2px;
}

.btn-group .btn:last-child {
  margin-right: 0;
}

.card-outline {
  border-width: 2px;
}

.dropdown-menu {
  min-width: 150px;
}
</style>
