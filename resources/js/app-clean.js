// Aplicación Vue.js principal para el sistema BMX con AdminLTE
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

// Objeto global para exponer funciones útiles
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
