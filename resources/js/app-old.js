// Aplicación Vue.js principal para el sistema BMX
import Vue from 'vue';
import router from './router';
import AppLayout from './components/AppLayout.vue';
import axios from 'axios';

// Importar AdminLTE JavaScript
import 'admin-lte/dist/js/adminlte.min.js';

// Configurar Axios globalmente
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Token CSRF
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Importar componentes Vue existentes
import RaceSheetManager from './components/RaceSheetManager.vue';
import PilotManager from './components/PilotManager.vue';
import ClubManager from './components/ClubManager.vue';
import CategoryManager from './components/CategoryManager.vue';
import ChampionshipManager from './components/ChampionshipManager.vue';
import MatchdayManager from './components/MatchdayManager.vue';
import SettingsManager from './components/SettingsManager.vue';
import PublicRegistrationManager from './components/PublicRegistrationManager.vue';
import UserManager from './components/UserManager.vue';
import ActivityLogManager from './components/ActivityLogManager.vue';
import NotificationSystem from './components/NotificationSystem.vue';
import DataTable from './components/DataTable.vue';
import FormValidator from './components/FormValidator.vue';
import SearchFilter from './components/SearchFilter.vue';
import DataPagination from './components/DataPagination.vue';
import LoadingSpinner from './components/LoadingSpinner.vue';
import StatusBadge from './components/StatusBadge.vue';

// Registrar componentes globalmente
Vue.component('race-sheet-manager', RaceSheetManager);
Vue.component('pilot-manager', PilotManager);
Vue.component('club-manager', ClubManager);
Vue.component('category-manager', CategoryManager);
Vue.component('championship-manager', ChampionshipManager);
Vue.component('matchday-manager', MatchdayManager);
Vue.component('settings-manager', SettingsManager);
Vue.component('public-registration-manager', PublicRegistrationManager);
Vue.component('user-manager', UserManager);
Vue.component('activity-log-manager', ActivityLogManager);
Vue.component('notification-system', NotificationSystem);
Vue.component('data-table', DataTable);
Vue.component('form-validator', FormValidator);
Vue.component('search-filter', SearchFilter);
Vue.component('data-pagination', DataPagination);
Vue.component('loading-spinner', LoadingSpinner);
Vue.component('status-badge', StatusBadge);

// Funciones utilitarias globales
function showNotification(message, type = 'info', duration = 3000) {
    const notification = {
        id: Date.now(),
        message,
        type,
        duration
    };
    
    // Aquí puedes implementar tu sistema de notificaciones
    console.log(`${type.toUpperCase()}: ${message}`);
    
    // Ejemplo simple con alert para errors
    if (type === 'error') {
        alert('Error: ' + message);
    }
}

function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

function formatDate(date) {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString();
}

function validateForm(form) {
    return form.checkValidity();
}

async function loadContent(url) {
    try {
        const response = await axios.get(url);
        return response.data;
    } catch (error) {
        showNotification('Error cargando contenido', 'error');
        throw error;
    }
}

/**
 * Inicializa los tooltips de Bootstrap
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Maneja las alertas con auto-dismiss
 */
function handleAlerts() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    
    alerts.forEach(function(alert) {
        // Auto-dismiss después de 5 segundos
        setTimeout(function() {
            if (alert && alert.parentNode) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
}

/**
 * Añade animaciones a las cards cuando aparecen en viewport
 */
function animateCards() {
    const cards = document.querySelectorAll('.card');
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    cards.forEach(function(card) {
        observer.observe(card);
    });
}

/**
 * Mejora la navegación añadiendo clases activas
 */
function improveNavigation() {
    const currentLocation = location.pathname;
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    navLinks.forEach(function(link) {
        if (link.getAttribute('href') === currentLocation) {
            link.classList.add('active');
        }
    });
}
    
    return isValid;
}

/**
 * Función para cargar contenido dinámicamente
 */
async function loadContent(url, targetSelector) {
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('Error al cargar el contenido');
        }
        
        const content = await response.text();
        const target = document.querySelector(targetSelector);
        
        if (target) {
            target.innerHTML = content;
        }
        
        return content;
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al cargar el contenido', 'error');
    }
}

/**
 * Inicializar formularios AJAX globales
 */
function initializeAjaxForms() {
    const ajaxForms = document.querySelectorAll('.ajax-form');
    
    ajaxForms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validar formulario
            if (!FormValidator.validateRequired(this)) {
                return;
            }
            
            const submitBtn = this.querySelector('[type="submit"]');
            const originalText = submitBtn.textContent;
            
            try {
                // Deshabilitar botón y mostrar loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
                
                const formData = new FormData(this);
                const url = this.action;
                
                const response = await AjaxHelper.post(url, Object.fromEntries(formData));
                
                if (response.success) {
                    NotificationManager.show(response.message || 'Operación exitosa', 'success');
                    
                    // Recargar página o redirigir si es necesario
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else if (response.reload) {
                        window.location.reload();
                    }
                }
                
            } catch (error) {
                NotificationManager.show('Error al procesar la solicitud', 'error');
                console.error(error);
            } finally {
                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    });
}

/**
 * Inicializar tablas de datos
 */
function initializeDataTables() {
    const tables = document.querySelectorAll('.data-table');
    
    tables.forEach(table => {
        if (table.tagName === 'TABLE') {
            TableHelper.initializeDataTable(`#${table.id}`);
        }
    });
}

// Exponer funciones globalmente para uso en plantillas Blade
window.LaravelApp = {
    showNotification,
    confirmAction,
    formatDate,
    validateForm,
    loadContent
};

// Hacer Vue disponible globalmente
window.Vue = Vue;

// Configuración global de Vue
Vue.config.productionTip = false;

// Crear la instancia principal de Vue para la SPA
const app = new Vue({
    el: '#app',
    router,
    render: h => h(AppLayout),
    mounted() {
        console.log('Vue SPA with AdminLTE mounted successfully');
        
        // Inicializar funcionalidades de AdminLTE después del montaje
        this.$nextTick(() => {
            // AdminLTE se auto-inicializa, pero podemos forzar la inicialización de algunos widgets
            if (window.AdminLTE) {
                window.AdminLTE.init();
            }
        });
    },
    errorCaptured(err, instance, info) {
        console.error('Vue error captured:', err, info);
        return false;
    }
});

// Exportar la instancia para debugging
window.vueApp = app;
