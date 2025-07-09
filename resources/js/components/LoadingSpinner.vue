<template>
  <div class="loading-spinner" v-if="isLoading">
    <div class="spinner-overlay" v-if="overlay">
      <div class="spinner-container">
        <div class="spinner-content">
          <div class="spinner" :class="spinnerClass"></div>
          <div class="spinner-text" v-if="message">
            {{ message }}
          </div>
        </div>
      </div>
    </div>
    <div v-else class="spinner-inline">
      <div class="spinner" :class="spinnerClass"></div>
      <span class="spinner-text ml-2" v-if="message">{{ message }}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LoadingSpinner',
  props: {
    isLoading: {
      type: Boolean,
      default: false
    },
    message: {
      type: String,
      default: ''
    },
    overlay: {
      type: Boolean,
      default: false
    },
    size: {
      type: String,
      default: 'md', // sm, md, lg
      validator: value => ['sm', 'md', 'lg'].includes(value)
    },
    color: {
      type: String,
      default: 'primary' // primary, secondary, success, danger, warning, info
    }
  },
  computed: {
    spinnerClass() {
      return `spinner-border-${this.size} text-${this.color}`;
    }
  }
};
</script>

<style scoped>
.spinner-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.spinner-container {
  background: white;
  padding: 2rem;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.spinner-inline {
  display: inline-flex;
  align-items: center;
}

.spinner {
  display: inline-block;
  width: 2rem;
  height: 2rem;
  vertical-align: text-bottom;
  border: 0.25em solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  animation: spinner-border 0.75s linear infinite;
}

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
  border-width: 0.2em;
}

.spinner-border-lg {
  width: 3rem;
  height: 3rem;
  border-width: 0.3em;
}

.spinner-text {
  color: #6c757d;
  font-size: 0.9rem;
}

@keyframes spinner-border {
  to {
    transform: rotate(360deg);
  }
}
</style>
