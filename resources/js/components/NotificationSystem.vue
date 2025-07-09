<template>
  <div class="notification-system">
    <transition-group name="notification" tag="div" class="notification-container">
      <div 
        v-for="notification in notifications" 
        :key="notification.id"
        :class="['alert', `alert-${notification.type}`, 'notification-toast']"
      >
        <div class="d-flex align-items-center">
          <i :class="getIcon(notification.type)" class="me-2"></i>
          {{ notification.message }}
          <button 
            @click="removeNotification(notification.id)"
            type="button" 
            class="btn-close ms-auto"
          ></button>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script>
export default {
  name: 'NotificationSystem',
  props: {
    notifications: {
      type: Array,
      default: () => []
    }
  },
  methods: {
    getIcon(type) {
      const icons = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-circle',
        'warning': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
      };
      return icons[type] || 'fas fa-info-circle';
    },
    
    removeNotification(id) {
      this.$parent.removeNotification(id);
    }
  }
}
</script>

<style scoped>
.notification-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  max-width: 400px;
}

.notification-toast {
  margin-bottom: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
}

/* Animaciones */
.notification-enter-active,
.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.notification-move {
  transition: transform 0.3s ease;
}
</style>
