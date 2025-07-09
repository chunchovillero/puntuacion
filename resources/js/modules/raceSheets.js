// MÓDULO MIGRADO A VUE.JS
// Este módulo JavaScript vanilla ha sido completamente migrado a Vue.js
// 
// Para usar la funcionalidad de planillas de carreras, utiliza:
// - Componente Vue: RaceSheetManager.vue
// - Vista Blade: race-sheets/vue-index.blade.php
// - Ruta: admin.race-sheets.vue-index
//
// MIGRACIÓN COMPLETADA EL: 2025-01-04
// RAZONES DE LA MIGRACIÓN:
// 1. Mejor gestión del estado reactivo
// 2. Drag & drop más fluido con Vue
// 3. Componentes reutilizables y modulares
// 4. Sistema de notificaciones unificado
// 5. Validaciones integradas
// 6. Mejor experiencia de usuario

console.warn('⚠️ DEPRECADO: Este módulo JavaScript vanilla ha sido migrado a Vue.js');
console.info('✅ Usa el componente <race-sheet-manager> en su lugar');

// Wrapper de compatibilidad para evitar errores en código legacy
export class RaceSheetManager {
    constructor() {
        console.warn(
            'RaceSheetManager (JS Vanilla) está deprecado.\n' +
            'Usa el componente Vue.js <race-sheet-manager> en su lugar.\n' +
            'Migración: route("admin.race-sheets.vue-index", matchday)'
        );
        
        // Redirigir automáticamente a la versión Vue si está disponible
        if (window.location.pathname.includes('/planilla') && !window.location.pathname.includes('/vue')) {
            const vueUrl = window.location.pathname + '/vue';
            console.info(`🚀 Redirigiendo a la versión Vue: ${vueUrl}`);
            // Opcional: redirección automática
            // window.location.href = vueUrl;
        }
    }

    // Métodos stub para compatibilidad
    init() { this.showMigrationMessage(); }
    bindEvents() { this.showMigrationMessage(); }
    handlePilotAssignment() { this.showMigrationMessage(); }
    handlePilotRemoval() { this.showMigrationMessage(); }
    initializeSortable() { this.showMigrationMessage(); }
    
    showMigrationMessage() {
        if (!this.messageShown) {
            this.messageShown = true;
            
            // Mostrar notificación de migración
            const notification = document.createElement('div');
            notification.className = 'alert alert-info alert-dismissible';
            notification.innerHTML = `
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h5><i class="fas fa-info-circle"></i> Migración a Vue.js</h5>
                <p>Esta funcionalidad ha sido migrada a Vue.js para una mejor experiencia.</p>
                <p><a href="${window.location.pathname}/vue" class="btn btn-primary btn-sm">
                    <i class="fas fa-rocket"></i> Usar Versión Vue.js
                </a></p>
            `;
            
            const container = document.querySelector('.content-wrapper') || document.body;
            container.insertBefore(notification, container.firstChild);
        }
    }
}

// Auto-inicializar solo para mostrar mensaje de migración
if (document.querySelector('.race-sheets-page')) {
    new RaceSheetManager();
}

// Exportar un objeto de migración para estadísticas
export const migrationInfo = {
    migratedDate: '2025-01-04',
    originalSize: '138 lines',
    vueComponentSize: '600+ lines',
    improvements: [
        'Reactive state management',
        'Better drag & drop UX',
        'Modular components',
        'Integrated notifications',
        'Form validation',
        'Better error handling',
        'TypeScript support ready',
        'Mobile-friendly UI'
    ],
    vueComponent: 'RaceSheetManager.vue',
    bladePath: 'admin.race-sheets.vue-index'
};
