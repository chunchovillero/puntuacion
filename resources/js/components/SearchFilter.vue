<template>
  <div class="search-filter-container">
    <!-- Búsqueda principal -->
    <div class="row mb-3">
      <div class="col-md-6">
        <div class="input-group">
          <input 
            type="text" 
            class="form-control" 
            :placeholder="searchPlaceholder"
            v-model="searchQuery"
            @input="onSearchInput"
          />
          <div class="input-group-append">
            <span class="input-group-text">
              <i class="fas fa-search"></i>
            </span>
          </div>
        </div>
      </div>
      
      <!-- Filtros adicionales -->
      <div class="col-md-6" v-if="hasFilters">
        <div class="row">
          <div class="col-md-6" v-for="filter in filters" :key="filter.key">
            <select 
              class="form-control"
              v-model="filterValues[filter.key]"
              @change="onFilterChange"
            >
              <option value="">{{ filter.placeholder }}</option>
              <option 
                v-for="option in filter.options" 
                :key="option.value"
                :value="option.value"
              >
                {{ option.label }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Botones de acción -->
    <div class="row mb-3" v-if="hasActions">
      <div class="col-12">
        <div class="btn-group" role="group">
          <button 
            v-for="action in actions"
            :key="action.key"
            :class="`btn ${action.class || 'btn-primary'}`"
            @click="$emit('action', action.key)"
          >
            <i :class="action.icon" v-if="action.icon"></i>
            {{ action.label }}
          </button>
        </div>
      </div>
    </div>

    <!-- Resumen de resultados -->
    <div class="row mb-2" v-if="showSummary">
      <div class="col-12">
        <small class="text-muted">
          {{ totalResults }} resultado(s) encontrado(s)
          <span v-if="hasActiveFilters">
            - Filtrado por: {{ activeFiltersText }}
          </span>
        </small>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SearchFilter',
  props: {
    searchPlaceholder: {
      type: String,
      default: 'Buscar...'
    },
    filters: {
      type: Array,
      default: () => []
    },
    actions: {
      type: Array,
      default: () => []
    },
    totalResults: {
      type: Number,
      default: 0
    },
    showSummary: {
      type: Boolean,
      default: true
    },
    searchDelay: {
      type: Number,
      default: 800
    }
  },
  data() {
    return {
      searchQuery: '',
      filterValues: {},
      searchTimeout: null
    };
  },
  computed: {
    hasFilters() {
      return this.filters.length > 0;
    },
    hasActions() {
      return this.actions.length > 0;
    },
    hasActiveFilters() {
      return Object.values(this.filterValues).some(value => value !== '');
    },
    activeFiltersText() {
      const activeFilters = [];
      for (const [key, value] of Object.entries(this.filterValues)) {
        if (value !== '') {
          const filter = this.filters.find(f => f.key === key);
          const option = filter?.options.find(o => o.value === value);
          if (option) {
            activeFilters.push(`${filter.label}: ${option.label}`);
          }
        }
      }
      return activeFilters.join(', ');
    }
  },
  mounted() {
    // Inicializar valores de filtros
    this.filters.forEach(filter => {
      this.$set(this.filterValues, filter.key, '');
    });
  },
  methods: {
    onSearchInput() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.$emit('search', {
          query: this.searchQuery,
          filters: this.filterValues
        });
      }, this.searchDelay);
    },
    onFilterChange() {
      this.$emit('search', {
        query: this.searchQuery,
        filters: this.filterValues
      });
    },
    clearFilters() {
      this.searchQuery = '';
      Object.keys(this.filterValues).forEach(key => {
        this.filterValues[key] = '';
      });
      this.$emit('search', {
        query: '',
        filters: this.filterValues
      });
    }
  }
};
</script>

<style scoped>
.search-filter-container {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 0.5rem;
  margin-bottom: 1rem;
}

.input-group-text {
  background-color: #e9ecef;
  border-color: #ced4da;
}
</style>
