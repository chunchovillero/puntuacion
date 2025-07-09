<template>
  <div class="container-fluid">
    <!-- Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>
              <i class="fas fa-cogs mr-2"></i>
              Configuración del Sistema
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
              <li class="breadcrumb-item active">Configuración</li>
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
            <!-- Header Card -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-cogs mr-2"></i>
                  Configuración del Sistema
                </h3>
                <div class="card-tools">
                  <div class="btn-group">
                    <button type="button" 
                            class="btn btn-tool dropdown-toggle" 
                            data-toggle="dropdown"
                            :disabled="loading">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" role="menu">
                      <button @click="exportSettings" class="dropdown-item" :disabled="loading">
                        <i class="fas fa-download mr-2"></i> Exportar Configuración
                      </button>
                      <button @click="showImportModal" class="dropdown-item" :disabled="loading">
                        <i class="fas fa-upload mr-2"></i> Importar Configuración
                      </button>
                      <div class="dropdown-divider"></div>
                      <button @click="showResetModal" 
                              class="dropdown-item text-danger" 
                              :disabled="loading">
                        <i class="fas fa-undo mr-2"></i> Restablecer Todo
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Notificaciones -->
            <notification-system ref="notifications"></notification-system>

            <!-- Loading -->
            <loading-spinner v-if="loading && !settingsLoaded"></loading-spinner>

            <!-- Settings Form -->
            <form @submit.prevent="saveSettings" v-if="!loading || settingsLoaded">
              <div v-for="(groupSettings, groupName) in settingsByGroup" 
                   :key="groupName" 
                   class="card card-outline card-secondary">
                <div class="card-header">
                  <h3 class="card-title">
                    <i :class="getGroupIcon(groupName)" class="mr-2"></i>
                    {{ getGroupTitle(groupName) }}
                  </h3>
                  <div class="card-tools">
                    <button type="button" 
                            class="btn btn-tool" 
                            @click="toggleGroup(groupName)"
                            :disabled="loading">
                      <i :class="groupCollapsed[groupName] ? 'fas fa-plus' : 'fas fa-minus'"></i>
                    </button>
                  </div>
                </div>
                <div v-show="!groupCollapsed[groupName]" class="card-body">
                  <div class="row">
                    <div v-for="setting in groupSettings" 
                         :key="setting.id" 
                         class="col-md-6 mb-3">
                      <div class="form-group">
                        <label :for="`setting_${setting.id}`">
                          {{ setting.description || setting.key }}
                          <span v-if="setting.type === 'password'" class="text-muted small">
                            (Dejar vacío para mantener actual)
                          </span>
                        </label>
                        
                        <!-- Boolean (Switch) -->
                        <div v-if="setting.type === 'boolean'" class="custom-control custom-switch">
                          <input type="checkbox" 
                                 class="custom-control-input" 
                                 :id="`setting_${setting.id}`" 
                                 v-model="settingValues[setting.key]"
                                 :true-value="true"
                                 :false-value="false"
                                 :disabled="loading">
                          <label class="custom-control-label" :for="`setting_${setting.id}`"></label>
                        </div>
                        
                        <!-- Select -->
                        <select v-else-if="setting.type === 'select'" 
                                class="form-control" 
                                :id="`setting_${setting.id}`" 
                                v-model="settingValues[setting.key]"
                                :disabled="loading">
                          <option v-for="option in setting.options" 
                                  :key="option" 
                                  :value="option">
                            {{ formatOptionLabel(option) }}
                          </option>
                        </select>
                        
                        <!-- Number -->
                        <input v-else-if="setting.type === 'number' || setting.type === 'integer'" 
                               type="number" 
                               class="form-control" 
                               :id="`setting_${setting.id}`" 
                               v-model="settingValues[setting.key]"
                               step="0.01"
                               :disabled="loading">
                        
                        <!-- Password -->
                        <input v-else-if="setting.type === 'password'" 
                               type="password" 
                               class="form-control" 
                               :id="`setting_${setting.id}`" 
                               v-model="settingValues[setting.key]"
                               placeholder="••••••••"
                               :disabled="loading">
                        
                        <!-- Textarea -->
                        <textarea v-else-if="setting.type === 'textarea'" 
                                  class="form-control" 
                                  :id="`setting_${setting.id}`" 
                                  v-model="settingValues[setting.key]"
                                  rows="3"
                                  :disabled="loading"></textarea>
                        
                        <!-- Email -->
                        <input v-else-if="setting.type === 'email'" 
                               type="email" 
                               class="form-control" 
                               :id="`setting_${setting.id}`" 
                               v-model="settingValues[setting.key]"
                               :disabled="loading">
                        
                        <!-- URL -->
                        <input v-else-if="setting.type === 'url'" 
                               type="url" 
                               class="form-control" 
                               :id="`setting_${setting.id}`" 
                               v-model="settingValues[setting.key]"
                               :disabled="loading">
                        
                        <!-- Text (default) -->
                        <input v-else 
                               type="text" 
                               class="form-control" 
                               :id="`setting_${setting.id}`" 
                               v-model="settingValues[setting.key]"
                               :disabled="loading">
                        
                        <!-- Error display -->
                        <div v-if="errors[setting.key]" class="invalid-feedback d-block">
                          {{ errors[setting.key] }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Save Button -->
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body text-center">
                      <button type="submit" 
                              class="btn btn-primary btn-lg" 
                              :disabled="loading">
                        <i v-if="loading" class="fas fa-spinner fa-spin mr-2"></i>
                        <i v-else class="fas fa-save mr-2"></i>
                        {{ loading ? 'Guardando...' : 'Guardar Configuración' }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form @submit.prevent="importSettings">
            <div class="modal-header">
              <h4 class="modal-title">
                <i class="fas fa-upload mr-2"></i>
                Importar Configuración
              </h4>
              <button type="button" class="close" data-dismiss="modal">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="import_file">Archivo JSON de Configuración</label>
                <div class="custom-file">
                  <input type="file" 
                         class="custom-file-input" 
                         id="import_file" 
                         ref="importFile"
                         accept=".json" 
                         @change="handleFileSelect"
                         :disabled="importing">
                  <label class="custom-file-label" for="import_file">
                    {{ selectedFileName || 'Seleccionar archivo...' }}
                  </label>
                </div>
                <small class="form-text text-muted">
                  Solo archivos JSON exportados desde este sistema.
                </small>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" 
                      class="btn btn-secondary" 
                      data-dismiss="modal"
                      :disabled="importing">
                Cancelar
              </button>
              <button type="submit" 
                      class="btn btn-primary" 
                      :disabled="!importFile || importing">
                <i v-if="importing" class="fas fa-spinner fa-spin mr-2"></i>
                <i v-else class="fas fa-upload mr-2"></i>
                {{ importing ? 'Importando...' : 'Importar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Reset Modal -->
    <div class="modal fade" id="resetModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-danger">
            <h4 class="modal-title text-white">
              <i class="fas fa-exclamation-triangle mr-2"></i>
              Restablecer Configuración
            </h4>
            <button type="button" class="close text-white" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p><strong>¿Estás seguro?</strong></p>
            <p>Esta acción restablecerá todas las configuraciones a sus valores predeterminados.</p>
            <p class="text-danger">
              <i class="fas fa-exclamation-triangle mr-1"></i>
              Esta acción no se puede deshacer.
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" 
                    class="btn btn-secondary" 
                    data-dismiss="modal"
                    :disabled="resetting">
              Cancelar
            </button>
            <button @click="resetSettings" 
                    type="button" 
                    class="btn btn-danger"
                    :disabled="resetting">
              <i v-if="resetting" class="fas fa-spinner fa-spin mr-2"></i>
              <i v-else class="fas fa-undo mr-2"></i>
              {{ resetting ? 'Restableciendo...' : 'Restablecer Todo' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SettingsManager',
  data() {
    return {
      // Estados principales
      loading: false,
      saving: false,
      importing: false,
      resetting: false,
      settingsLoaded: false,
      
      // Datos
      settingsByGroup: {},
      settingValues: {},
      originalValues: {},
      errors: {},
      
      // UI State
      groupCollapsed: {},
      
      // Import
      importFile: null,
      selectedFileName: '',
      
      // Mapeo de grupos
      groupIcons: {
        general: 'fas fa-home',
        system: 'fas fa-server',
        competition: 'fas fa-trophy',
        email: 'fas fa-envelope',
        notifications: 'fas fa-bell',
        payments: 'fas fa-credit-card'
      },
      
      groupTitles: {
        general: 'Configuración General',
        system: 'Sistema',
        competition: 'Competencias',
        email: 'Email',
        notifications: 'Notificaciones',
        payments: 'Pagos (Flow)'
      }
    }
  },
  
  mounted() {
    this.loadSettings();
  },
  
  methods: {
    async loadSettings() {
      this.loading = true;
      try {
        const response = await fetch('/api/settings');
        if (response.ok) {
          const data = await response.json();
          this.settingsByGroup = data.data;
          this.initializeSettingValues();
          this.initializeGroupStates();
          this.settingsLoaded = true;
        } else {
          this.$refs.notifications.show('Error al cargar configuraciones', 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        this.$refs.notifications.show('Error de conexión', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    initializeSettingValues() {
      this.settingValues = {};
      this.originalValues = {};
      
      Object.values(this.settingsByGroup).forEach(groupSettings => {
        groupSettings.forEach(setting => {
          let value = setting.value;
          
          // Convertir valores según el tipo
          if (setting.type === 'boolean') {
            value = value === '1' || value === 'true' || value === true;
          } else if (setting.type === 'number' || setting.type === 'integer') {
            value = value ? Number(value) : 0;
          }
          
          this.settingValues[setting.key] = value;
          this.originalValues[setting.key] = value;
        });
      });
    },
    
    initializeGroupStates() {
      this.groupCollapsed = {};
      Object.keys(this.settingsByGroup).forEach(groupName => {
        this.groupCollapsed[groupName] = false;
      });
    },
    
    getGroupIcon(groupName) {
      return this.groupIcons[groupName] || 'fas fa-cog';
    },
    
    getGroupTitle(groupName) {
      return this.groupTitles[groupName] || this.formatTitle(groupName);
    },
    
    formatTitle(str) {
      return str.charAt(0).toUpperCase() + str.slice(1);
    },
    
    formatOptionLabel(option) {
      return option.charAt(0).toUpperCase() + option.slice(1);
    },
    
    toggleGroup(groupName) {
      this.groupCollapsed[groupName] = !this.groupCollapsed[groupName];
    },
    
    async saveSettings() {
      this.loading = true;
      this.errors = {};
      
      try {
        // Preparar datos para envío
        const settingsData = {};
        Object.keys(this.settingValues).forEach(key => {
          let value = this.settingValues[key];
          
          // Convertir boolean a string para el backend
          if (typeof value === 'boolean') {
            value = value ? '1' : '0';
          }
          
          settingsData[key] = value;
        });
        
        const response = await fetch('/api/settings', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ settings: settingsData })
        });
        
        const result = await response.json();
        
        if (response.ok) {
          this.originalValues = { ...this.settingValues };
          this.$refs.notifications.show('Configuraciones guardadas correctamente', 'success');
        } else {
          if (result.errors) {
            this.errors = result.errors;
          }
          this.$refs.notifications.show(result.message || 'Error al guardar configuraciones', 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        this.$refs.notifications.show('Error de conexión', 'error');
      } finally {
        this.loading = false;
      }
    },
    
    async exportSettings() {
      try {
        const response = await fetch('/api/settings/export');
        if (response.ok) {
          const blob = await response.blob();
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = `configuraciones_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.json`;
          a.click();
          window.URL.revokeObjectURL(url);
          this.$refs.notifications.show('Configuraciones exportadas correctamente', 'success');
        } else {
          this.$refs.notifications.show('Error al exportar configuraciones', 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        this.$refs.notifications.show('Error de conexión', 'error');
      }
    },
    
    showImportModal() {
      $('#importModal').modal('show');
    },
    
    showResetModal() {
      $('#resetModal').modal('show');
    },
    
    handleFileSelect(event) {
      const file = event.target.files[0];
      if (file) {
        this.importFile = file;
        this.selectedFileName = file.name;
      } else {
        this.importFile = null;
        this.selectedFileName = '';
      }
    },
    
    async importSettings() {
      if (!this.importFile) return;
      
      this.importing = true;
      
      try {
        const formData = new FormData();
        formData.append('file', this.importFile);
        
        const response = await fetch('/api/settings/import', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: formData
        });
        
        const result = await response.json();
        
        if (response.ok) {
          $('#importModal').modal('hide');
          this.loadSettings(); // Recargar configuraciones
          this.$refs.notifications.show('Configuraciones importadas correctamente', 'success');
        } else {
          this.$refs.notifications.show(result.message || 'Error al importar configuraciones', 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        this.$refs.notifications.show('Error de conexión', 'error');
      } finally {
        this.importing = false;
        this.importFile = null;
        this.selectedFileName = '';
        if (this.$refs.importFile) {
          this.$refs.importFile.value = '';
        }
      }
    },
    
    async resetSettings() {
      this.resetting = true;
      
      try {
        const response = await fetch('/api/settings/reset', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        
        const result = await response.json();
        
        if (response.ok) {
          $('#resetModal').modal('hide');
          this.loadSettings(); // Recargar configuraciones
          this.$refs.notifications.show('Configuraciones restablecidas correctamente', 'success');
        } else {
          this.$refs.notifications.show(result.message || 'Error al restablecer configuraciones', 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        this.$refs.notifications.show('Error de conexión', 'error');
      } finally {
        this.resetting = false;
      }
    }
  }
}
</script>

<style scoped>
.card-tools .dropdown-menu {
  border: 1px solid #dee2e6;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-group label {
  font-weight: 600;
  color: #495057;
}

.custom-control-label::before {
  border-radius: 0.375rem;
}

.custom-control-input:checked ~ .custom-control-label::before {
  background-color: #007bff;
  border-color: #007bff;
}

.invalid-feedback {
  font-size: 0.875em;
}

.modal-header.bg-danger .close {
  opacity: 0.8;
}

.modal-header.bg-danger .close:hover {
  opacity: 1;
}

.custom-file-label.selected {
  color: #495057;
}

.dropdown-item:disabled {
  color: #6c757d;
  pointer-events: none;
  background-color: transparent;
}
</style>
