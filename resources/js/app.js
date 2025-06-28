// JavaScript personalizado para la aplicación Laravel

document.addEventListener('DOMContentLoaded', function() {
    console.log('Aplicación Laravel cargada correctamente');
    
    // Inicializar tooltips de Bootstrap
    initializeTooltips();
    
    // Manejo de alertas auto-dismiss
    handleAlerts();
    
    // Añadir animaciones a las cards
    animateCards();
    
    // Mejorar la navegación
    improveNavigation();
});

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

/**
 * Función utilitaria para mostrar notificaciones
 */
function showNotification(message, type = 'info') {
    const alertTypes = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const alertClass = alertTypes[type] || 'alert-info';
    
    const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertAdjacentHTML('afterbegin', alertHTML);
        
        // Auto-dismiss después de 4 segundos
        setTimeout(function() {
            const alert = container.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 4000);
    }
}

/**
 * Función para confirmar acciones destructivas
 */
function confirmAction(message = '¿Estás seguro de que quieres continuar?') {
    return confirm(message);
}

/**
 * Función para formatear fechas
 */
function formatDate(date, options = {}) {
    const defaultOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    
    const formatOptions = Object.assign(defaultOptions, options);
    
    return new Intl.DateTimeFormat('es-ES', formatOptions).format(new Date(date));
}

/**
 * Función para validar formularios básicos
 */
function validateForm(formSelector) {
    const form = document.querySelector(formSelector);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
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

// Exponer funciones globalmente para uso en plantillas Blade
window.LaravelApp = {
    showNotification,
    confirmAction,
    formatDate,
    validateForm,
    loadContent
};
