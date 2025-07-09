<template>
  <form @submit.prevent="handleSubmit" :class="formClass">
    <slot :errors="errors" :valid="isValid" :submitting="submitting"></slot>
  </form>
</template>

<script>
export default {
  name: 'FormValidator',
  props: {
    rules: {
      type: Object,
      default: () => ({})
    },
    submitHandler: {
      type: Function,
      required: true
    },
    formClass: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      errors: {},
      submitting: false,
      formData: {}
    }
  },
  computed: {
    isValid() {
      return Object.keys(this.errors).length === 0;
    }
  },
  methods: {
    validate(fieldName, value) {
      const fieldRules = this.rules[fieldName];
      if (!fieldRules) return true;
      
      const errors = [];
      
      // Validación requerido
      if (fieldRules.required && (!value || value.toString().trim() === '')) {
        errors.push('Este campo es requerido');
      }
      
      // Validación mínimo
      if (fieldRules.min && value && value.length < fieldRules.min) {
        errors.push(`Mínimo ${fieldRules.min} caracteres`);
      }
      
      // Validación máximo
      if (fieldRules.max && value && value.length > fieldRules.max) {
        errors.push(`Máximo ${fieldRules.max} caracteres`);
      }
      
      // Validación email
      if (fieldRules.email && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
          errors.push('Email inválido');
        }
      }
      
      // Validación personalizada
      if (fieldRules.custom && typeof fieldRules.custom === 'function') {
        const customError = fieldRules.custom(value);
        if (customError) {
          errors.push(customError);
        }
      }
      
      // Actualizar errores
      if (errors.length > 0) {
        this.$set(this.errors, fieldName, errors[0]);
      } else {
        this.$delete(this.errors, fieldName);
      }
      
      return errors.length === 0;
    },
    
    validateAll() {
      Object.keys(this.rules).forEach(fieldName => {
        const value = this.formData[fieldName];
        this.validate(fieldName, value);
      });
      
      return this.isValid;
    },
    
    async handleSubmit() {
      if (!this.validateAll()) {
        return;
      }
      
      this.submitting = true;
      
      try {
        await this.submitHandler(this.formData);
        this.resetForm();
      } catch (error) {
        if (error.response?.data?.errors) {
          this.errors = { ...this.errors, ...error.response.data.errors };
        }
      } finally {
        this.submitting = false;
      }
    },
    
    updateField(fieldName, value) {
      this.$set(this.formData, fieldName, value);
      this.validate(fieldName, value);
    },
    
    resetForm() {
      this.formData = {};
      this.errors = {};
    },
    
    setErrors(errors) {
      this.errors = errors;
    }
  }
}
</script>
