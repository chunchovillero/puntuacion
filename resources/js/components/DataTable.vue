<template>
  <div class="data-table-wrapper">
    <div class="table-controls mb-3">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label>Mostrar:</label>
            <select v-model="pageSize" @change="updatePageSize" class="form-control form-control-sm d-inline-block ml-2" style="width: auto;">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <span class="ml-2">registros</span>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label>Buscar:</label>
            <input 
              v-model="searchTerm" 
              @input="search"
              type="text" 
              class="form-control form-control-sm d-inline-block ml-2" 
              style="width: 200px;"
              placeholder="Filtrar resultados..."
            >
          </div>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="thead-dark">
          <tr>
            <th 
              v-for="column in columns" 
              :key="column.key"
              @click="sort(column.key)"
              :class="{ 'sortable': column.sortable !== false }"
            >
              {{ column.label }}
              <i 
                v-if="column.sortable !== false"
                :class="getSortIcon(column.key)"
                class="ml-1"
              ></i>
            </th>
            <th v-if="actions.length > 0">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in paginatedData" :key="getItemKey(item, index)">
            <td v-for="column in columns" :key="column.key">
              <slot 
                :name="`column-${column.key}`" 
                :item="item" 
                :value="getNestedValue(item, column.key)"
              >
                {{ formatValue(getNestedValue(item, column.key), column.format) }}
              </slot>
            </td>
            <td v-if="actions.length > 0">
              <div class="btn-group btn-group-sm">
                <button
                  v-for="action in actions"
                  :key="action.name"
                  @click="executeAction(action, item)"
                  :class="['btn', `btn-${action.variant || 'primary'}`]"
                  :title="action.title"
                >
                  <i :class="action.icon"></i>
                  {{ action.label }}
                </button>
              </div>
            </td>
          </tr>
          
          <!-- Mensaje cuando no hay datos -->
          <tr v-if="filteredData.length === 0">
            <td :colspan="columns.length + (actions.length > 0 ? 1 : 0)" class="text-center text-muted py-4">
              {{ searchTerm ? 'No se encontraron resultados para la búsqueda' : 'No hay datos disponibles' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <div v-if="totalPages > 1" class="pagination-wrapper d-flex justify-content-between align-items-center">
      <div class="pagination-info">
        Mostrando {{ startRecord }} a {{ endRecord }} de {{ filteredData.length }} registros
        <span v-if="searchTerm">(filtrados de {{ data.length }} registros totales)</span>
      </div>
      
      <nav>
        <ul class="pagination pagination-sm mb-0">
          <li :class="['page-item', { disabled: currentPage === 1 }]">
            <button @click="goToPage(currentPage - 1)" class="page-link">
              <i class="fas fa-chevron-left"></i>
            </button>
          </li>
          
          <li 
            v-for="page in visiblePages" 
            :key="page"
            :class="['page-item', { active: page === currentPage }]"
          >
            <button @click="goToPage(page)" class="page-link">
              {{ page }}
            </button>
          </li>
          
          <li :class="['page-item', { disabled: currentPage === totalPages }]">
            <button @click="goToPage(currentPage + 1)" class="page-link">
              <i class="fas fa-chevron-right"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>

    <!-- Botones de exportación -->
    <div v-if="exportable" class="export-buttons mt-3">
      <button @click="exportData('csv')" class="btn btn-sm btn-outline-primary mr-2">
        <i class="fas fa-file-csv"></i> Exportar CSV
      </button>
      <button @click="exportData('excel')" class="btn btn-sm btn-outline-success mr-2">
        <i class="fas fa-file-excel"></i> Exportar Excel
      </button>
      <button @click="exportData('pdf')" class="btn btn-sm btn-outline-danger">
        <i class="fas fa-file-pdf"></i> Exportar PDF
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DataTable',
  props: {
    data: {
      type: Array,
      required: true
    },
    columns: {
      type: Array,
      required: true
    },
    actions: {
      type: Array,
      default: () => []
    },
    exportable: {
      type: Boolean,
      default: false
    },
    keyField: {
      type: String,
      default: 'id'
    }
  },
  data() {
    return {
      searchTerm: '',
      sortColumn: '',
      sortDirection: 'asc',
      currentPage: 1,
      pageSize: 25,
      filteredData: [],
      loading: false
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.filteredData.length / this.pageSize);
    },
    
    startRecord() {
      return (this.currentPage - 1) * this.pageSize + 1;
    },
    
    endRecord() {
      const end = this.currentPage * this.pageSize;
      return end > this.filteredData.length ? this.filteredData.length : end;
    },
    
    paginatedData() {
      const start = (this.currentPage - 1) * this.pageSize;
      const end = start + this.pageSize;
      return this.filteredData.slice(start, end);
    },
    
    visiblePages() {
      const pages = [];
      const start = Math.max(1, this.currentPage - 2);
      const end = Math.min(this.totalPages, this.currentPage + 2);
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    }
  },
  watch: {
    data: {
      handler() {
        this.filterAndSort();
      },
      immediate: true,
      deep: true
    }
  },
  methods: {
    search() {
      this.currentPage = 1;
      this.filterAndSort();
    },
    
    sort(column) {
      if (this.sortColumn === column) {
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortColumn = column;
        this.sortDirection = 'asc';
      }
      
      this.filterAndSort();
    },
    
    filterAndSort() {
      let filtered = [...this.data];
      
      // Filtrar
      if (this.searchTerm) {
        const term = this.searchTerm.toLowerCase();
        filtered = filtered.filter(item => {
          return this.columns.some(column => {
            const value = this.getNestedValue(item, column.key);
            return String(value).toLowerCase().includes(term);
          });
        });
      }
      
      // Ordenar
      if (this.sortColumn) {
        filtered.sort((a, b) => {
          const aVal = this.getNestedValue(a, this.sortColumn);
          const bVal = this.getNestedValue(b, this.sortColumn);
          
          let comparison = 0;
          if (aVal > bVal) comparison = 1;
          if (aVal < bVal) comparison = -1;
          
          return this.sortDirection === 'desc' ? -comparison : comparison;
        });
      }
      
      this.filteredData = filtered;
    },
    
    getNestedValue(obj, path) {
      return path.split('.').reduce((current, key) => current?.[key], obj);
    },
    
    formatValue(value, format) {
      if (!format) return value;
      
      switch (format) {
        case 'date':
          return new Date(value).toLocaleDateString();
        case 'datetime':
          return new Date(value).toLocaleString();
        case 'currency':
          return new Intl.NumberFormat('es-ES', { 
            style: 'currency', 
            currency: 'EUR' 
          }).format(value);
        default:
          return value;
      }
    },
    
    getSortIcon(column) {
      if (this.sortColumn !== column) {
        return 'fas fa-sort text-muted';
      }
      
      return this.sortDirection === 'asc' 
        ? 'fas fa-sort-up text-primary' 
        : 'fas fa-sort-down text-primary';
    },
    
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },
    
    updatePageSize() {
      this.currentPage = 1;
    },
    
    executeAction(action, item) {
      this.$emit('action', { action: action.name, item });
    },
    
    getItemKey(item, index) {
      return item[this.keyField] || index;
    },
    
    exportData(format) {
      this.$emit('export', { format, data: this.filteredData });
    }
  }
}
</script>

<style scoped>
.sortable {
  cursor: pointer;
  user-select: none;
}

.sortable:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.table-controls {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 20px;
}

.pagination-wrapper {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #dee2e6;
}

.export-buttons {
  padding-top: 15px;
  border-top: 1px solid #dee2e6;
}
</style>
