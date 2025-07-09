<template>
  <span 
    :class="badgeClasses"
    :title="tooltipText"
  >
    <i :class="iconClass" v-if="showIcon"></i>
    {{ displayText }}
  </span>
</template>

<script>
export default {
  name: 'StatusBadge',
  props: {
    status: {
      type: String,
      required: true
    },
    type: {
      type: String,
      default: 'default' // default, pilot, club, championship, matchday
    },
    showIcon: {
      type: Boolean,
      default: true
    },
    size: {
      type: String,
      default: 'md', // sm, md, lg
      validator: value => ['sm', 'md', 'lg'].includes(value)
    }
  },
  computed: {
    statusConfig() {
      const configs = {
        // Estados generales
        active: { 
          class: 'success', 
          text: 'Activo', 
          icon: 'fas fa-check-circle',
          tooltip: 'Estado activo'
        },
        inactive: { 
          class: 'secondary', 
          text: 'Inactivo', 
          icon: 'fas fa-times-circle',
          tooltip: 'Estado inactivo'
        },
        pending: { 
          class: 'warning', 
          text: 'Pendiente', 
          icon: 'fas fa-clock',
          tooltip: 'Pendiente de confirmación'
        },
        cancelled: { 
          class: 'danger', 
          text: 'Cancelado', 
          icon: 'fas fa-ban',
          tooltip: 'Cancelado'
        },
        completed: { 
          class: 'info', 
          text: 'Completado', 
          icon: 'fas fa-check',
          tooltip: 'Completado exitosamente'
        },
        
        // Estados específicos de pilotos
        registered: { 
          class: 'primary', 
          text: 'Inscrito', 
          icon: 'fas fa-user-check',
          tooltip: 'Piloto inscrito'
        },
        confirmed: { 
          class: 'success', 
          text: 'Confirmado', 
          icon: 'fas fa-check-double',
          tooltip: 'Participación confirmada'
        },
        
        // Estados de campeonatos
        draft: { 
          class: 'secondary', 
          text: 'Borrador', 
          icon: 'fas fa-edit',
          tooltip: 'En estado de borrador'
        },
        published: { 
          class: 'primary', 
          text: 'Publicado', 
          icon: 'fas fa-eye',
          tooltip: 'Publicado y visible'
        },
        planned: {
          class: 'secondary',
          text: 'Planeado',
          icon: 'fas fa-clock',
          tooltip: 'Campeonato planeado'
        },
        completed: {
          class: 'primary',
          text: 'Completado',
          icon: 'fas fa-check',
          tooltip: 'Campeonato completado'
        },
        cancelled: {
          class: 'danger',
          text: 'Cancelado',
          icon: 'fas fa-times',
          tooltip: 'Campeonato cancelado'
        },
        
        // Estados de jornadas (matchdays)
        scheduled: { 
          class: 'info', 
          text: 'Programada', 
          icon: 'fas fa-calendar',
          tooltip: 'Jornada programada'
        },
        ongoing: { 
          class: 'warning', 
          text: 'En Curso', 
          icon: 'fas fa-play',
          tooltip: 'Jornada en curso'
        },
        'in-progress': { 
          class: 'warning', 
          text: 'En Curso', 
          icon: 'fas fa-play',
          tooltip: 'Jornada en curso'
        },
        completed: { 
          class: 'success', 
          text: 'Completada', 
          icon: 'fas fa-flag-checkered',
          tooltip: 'Jornada completada'
        },
        finished: { 
          class: 'success', 
          text: 'Finalizada', 
          icon: 'fas fa-flag-checkered',
          tooltip: 'Jornada finalizada'
        },
        cancelled: { 
          class: 'danger', 
          text: 'Cancelada', 
          icon: 'fas fa-ban',
          tooltip: 'Jornada cancelada'
        },
        postponed: { 
          class: 'secondary', 
          text: 'Postergada', 
          icon: 'fas fa-clock',
          tooltip: 'Jornada postergada'
        },
        
        // Estados de clubes
        suspended: { 
          class: 'danger', 
          text: 'Suspendido', 
          icon: 'fas fa-ban',
          tooltip: 'Club suspendido'
        },
        verified: { 
          class: 'success', 
          text: 'Verificado', 
          icon: 'fas fa-shield-alt',
          tooltip: 'Club verificado'
        },
        unverified: { 
          class: 'warning', 
          text: 'No Verificado', 
          icon: 'fas fa-exclamation-triangle',
          tooltip: 'Club pendiente de verificación'
        }
      };
      
      return configs[this.status] || {
        class: 'secondary',
        text: this.status,
        icon: 'fas fa-question',
        tooltip: `Estado: ${this.status}`
      };
    },
    badgeClasses() {
      const baseClass = 'badge';
      const sizeClass = this.size === 'sm' ? 'badge-sm' : this.size === 'lg' ? 'badge-lg' : '';
      const colorClass = `badge-${this.statusConfig.class}`;
      
      return [baseClass, colorClass, sizeClass].filter(Boolean).join(' ');
    },
    displayText() {
      return this.statusConfig.text;
    },
    iconClass() {
      return this.statusConfig.icon;
    },
    tooltipText() {
      return this.statusConfig.tooltip;
    }
  }
};
</script>

<style scoped>
.badge {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  font-weight: 500;
  white-space: nowrap;
}

.badge-sm {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
}

.badge-lg {
  font-size: 1rem;
  padding: 0.5rem 0.75rem;
}

.badge i {
  font-size: 0.8em;
}

/* Bootstrap badge colors */
.badge-primary {
  color: #fff;
  background-color: #007bff;
}

.badge-secondary {
  color: #fff;
  background-color: #6c757d;
}

.badge-success {
  color: #fff;
  background-color: #28a745;
}

.badge-danger {
  color: #fff;
  background-color: #dc3545;
}

.badge-warning {
  color: #212529;
  background-color: #ffc107;
}

.badge-info {
  color: #fff;
  background-color: #17a2b8;
}

.badge-light {
  color: #212529;
  background-color: #f8f9fa;
}

.badge-dark {
  color: #fff;
  background-color: #343a40;
}
</style>
