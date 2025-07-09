<template>
  <div class="data-pagination" v-if="shouldShowPagination">
    <div class="row align-items-center">
      <!-- Información de página -->
      <div class="col-md-6">
        <div class="pagination-info">
          <small class="text-muted">
            Mostrando {{ startItem }} al {{ endItem }} de {{ total }} registros
          </small>
        </div>
      </div>

      <!-- Controles de paginación -->
      <div class="col-md-6">
        <nav aria-label="Navegación de páginas">
          <ul class="pagination pagination-sm justify-content-end mb-0">
            <!-- Botón Anterior -->
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <button 
                class="page-link" 
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
              >
                <i class="fas fa-chevron-left"></i>
                Anterior
              </button>
            </li>

            <!-- Primera página -->
            <li class="page-item" v-if="showFirstPage">
              <button class="page-link" @click="goToPage(1)">1</button>
            </li>
            
            <!-- Separador inicial -->
            <li class="page-item disabled" v-if="showFirstSeparator">
              <span class="page-link">...</span>
            </li>

            <!-- Páginas visibles -->
            <li 
              class="page-item" 
              v-for="page in visiblePages" 
              :key="page"
              :class="{ active: page === currentPage }"
            >
              <button class="page-link" @click="goToPage(page)">
                {{ page }}
              </button>
            </li>

            <!-- Separador final -->
            <li class="page-item disabled" v-if="showLastSeparator">
              <span class="page-link">...</span>
            </li>

            <!-- Última página -->
            <li class="page-item" v-if="showLastPage">
              <button class="page-link" @click="goToPage(lastPage)">
                {{ lastPage }}
              </button>
            </li>

            <!-- Botón Siguiente -->
            <li class="page-item" :class="{ disabled: currentPage === lastPage }">
              <button 
                class="page-link" 
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === lastPage"
              >
                Siguiente
                <i class="fas fa-chevron-right"></i>
              </button>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Selector de items por página -->
    <div class="row mt-2" v-if="showPerPageSelector">
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-center">
          <label class="mb-0 mr-2">
            <small>Mostrar:</small>
          </label>
          <select 
            class="form-control form-control-sm d-inline-block w-auto"
            v-model="itemsPerPage"
            @change="onPerPageChange"
          >
            <option v-for="option in perPageOptions" :key="option" :value="option">
              {{ option }}
            </option>
          </select>
          <span class="ml-2">
            <small>por página</small>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DataPagination',
  props: {
    currentPage: {
      type: Number,
      required: true
    },
    total: {
      type: Number,
      required: true
    },
    perPage: {
      type: Number,
      default: 15
    },
    maxVisiblePages: {
      type: Number,
      default: 5
    },
    showPerPageSelector: {
      type: Boolean,
      default: true
    },
    perPageOptions: {
      type: Array,
      default: () => [10, 15, 25, 50, 100]
    }
  },
  data() {
    return {
      itemsPerPage: this.perPage
    };
  },
  computed: {
    lastPage() {
      return Math.ceil(this.total / this.itemsPerPage);
    },
    startItem() {
      return (this.currentPage - 1) * this.itemsPerPage + 1;
    },
    endItem() {
      const end = this.currentPage * this.itemsPerPage;
      return end > this.total ? this.total : end;
    },
    shouldShowPagination() {
      return this.total > this.itemsPerPage;
    },
    visiblePages() {
      const pages = [];
      const half = Math.floor(this.maxVisiblePages / 2);
      
      let start = Math.max(1, this.currentPage - half);
      let end = Math.min(this.lastPage, start + this.maxVisiblePages - 1);
      
      // Ajustar el inicio si estamos cerca del final
      if (end - start + 1 < this.maxVisiblePages) {
        start = Math.max(1, end - this.maxVisiblePages + 1);
      }
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    },
    showFirstPage() {
      return this.visiblePages[0] > 1;
    },
    showLastPage() {
      return this.visiblePages[this.visiblePages.length - 1] < this.lastPage;
    },
    showFirstSeparator() {
      return this.visiblePages[0] > 2;
    },
    showLastSeparator() {
      return this.visiblePages[this.visiblePages.length - 1] < this.lastPage - 1;
    }
  },
  watch: {
    perPage(newVal) {
      this.itemsPerPage = newVal;
    }
  },
  methods: {
    goToPage(page) {
      if (page >= 1 && page <= this.lastPage && page !== this.currentPage) {
        this.$emit('page-change', page);
      }
    },
    onPerPageChange() {
      this.$emit('per-page-change', this.itemsPerPage);
    }
  }
};
</script>

<style scoped>
.pagination-info {
  display: flex;
  align-items: center;
  height: 32px; /* Align with pagination buttons */
}

.page-link {
  border-color: #dee2e6;
  color: #6c757d;
}

.page-link:hover {
  background-color: #e9ecef;
  border-color: #dee2e6;
  color: #495057;
}

.page-item.active .page-link {
  background-color: #007bff;
  border-color: #007bff;
  color: white;
}

.page-item.disabled .page-link {
  color: #6c757d;
  background-color: #fff;
  border-color: #dee2e6;
}

@media (max-width: 768px) {
  .pagination-info {
    text-align: center;
    margin-bottom: 1rem;
  }
  
  .pagination {
    justify-content: center !important;
  }
}
</style>
