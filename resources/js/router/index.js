import Vue from 'vue';
import VueRouter from 'vue-router';

// Componentes de páginas
import HomePage from '../components/pages/HomePage.vue';
import PilotsPage from '../components/pages/PilotsPage.vue';
import ClubsPage from '../components/pages/ClubsPage.vue';
import CategoriesPage from '../components/pages/CategoriesPage.vue';
import ChampionshipsPage from '../components/pages/ChampionshipsPage.vue';
import MatchdaysPage from '../components/pages/MatchdaysPage.vue';
import UsersPage from '../components/pages/UsersPage.vue';
import SettingsPage from '../components/pages/SettingsPage.vue';
import ActivityLogsPage from '../components/pages/ActivityLogsPage.vue';
import NotFoundPage from '../components/pages/NotFoundPage.vue';

Vue.use(VueRouter);

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomePage,
        meta: { title: 'Inicio' }
    },
    {
        path: '/pilotos',
        name: 'pilots',
        component: PilotsPage,
        meta: { title: 'Pilotos' }
    },
    {
        path: '/pilotos/crear',
        name: 'pilots.create',
        component: () => import('../components/forms/PilotForm.vue'),
        meta: { 
            title: 'Crear Piloto',
            requiresAuth: true
        }
    },
    {
        path: '/pilotos/:id',
        name: 'pilots.show',
        component: () => import('../components/forms/PilotDetail.vue'),
        meta: { title: 'Ver Piloto' }
    },
    {
        path: '/pilotos/:id/editar',
        name: 'pilots.edit',
        component: () => import('../components/forms/PilotForm.vue'),
        meta: { 
            title: 'Editar Piloto',
            requiresAuth: true
        }
    },
    {
        path: '/clubes',
        name: 'clubs',
        component: ClubsPage,
        meta: { title: 'Clubes' }
    },
    {
        path: '/clubes/crear',
        name: 'clubs.create',
        component: () => import('../components/forms/ClubForm.vue'),
        meta: { 
            title: 'Crear Club',
            requiresAuth: true
        }
    },
    {
        path: '/clubes/:id',
        name: 'clubs.show',
        component: () => import('../components/forms/ClubDetail.vue'),
        meta: { title: 'Ver Club' }
    },
    {
        path: '/clubes/:id/editar',
        name: 'clubs.edit',
        component: () => import('../components/forms/ClubForm.vue'),
        meta: { 
            title: 'Editar Club',
            requiresAuth: true
        }
    },
    {
        path: '/categorias',
        name: 'categories',
        component: CategoriesPage,
        meta: { title: 'Categorías' }
    },
    {
        path: '/categorias/crear',
        name: 'categories.create',
        component: () => import('../components/forms/CategoryForm.vue'),
        meta: { 
            title: 'Crear Categoría',
            requiresAuth: true
        }
    },
    {
        path: '/categorias/:id',
        name: 'categories.show',
        component: () => import('../components/forms/CategoryDetail.vue'),
        meta: { title: 'Ver Categoría' }
    },
    {
        path: '/categorias/:id/editar',
        name: 'categories.edit',
        component: () => import('../components/forms/CategoryForm.vue'),
        meta: { 
            title: 'Editar Categoría',
            requiresAuth: true
        }
    },
    {
        path: '/campeonatos',
        name: 'championships',
        component: ChampionshipsPage,
        meta: { title: 'Campeonatos' }
    },
    {
        path: '/campeonatos/crear',
        name: 'championships.create',
        component: () => import('../components/forms/ChampionshipForm.vue'),
        meta: { 
            title: 'Crear Campeonato',
            requiresAuth: true
        }
    },
    {
        path: '/campeonatos/:id',
        name: 'championships.show',
        component: () => import('../components/forms/ChampionshipDetail.vue'),
        meta: { title: 'Ver Campeonato' }
    },
    {
        path: '/campeonatos/:id/editar',
        name: 'championships.edit',
        component: () => import('../components/forms/ChampionshipForm.vue'),
        meta: { 
            title: 'Editar Campeonato',
            requiresAuth: true
        }
    },
    {
        path: '/jornadas',
        name: 'matchdays',
        component: MatchdaysPage,
        meta: { title: 'Jornadas' }
    },
    {
        path: '/jornadas/crear',
        name: 'matchdays.create',
        component: () => import('../components/forms/MatchdayForm.vue'),
        meta: { 
            title: 'Crear Jornada',
            requiresAuth: true
        }
    },
    {
        path: '/jornadas/:id',
        name: 'matchdays.show',
        component: () => import('../components/forms/MatchdayDetail.vue'),
        meta: { title: 'Ver Jornada' }
    },
    {
        path: '/jornadas/:id/editar',
        name: 'matchdays.edit',
        component: () => import('../components/forms/MatchdayForm.vue'),
        meta: { 
            title: 'Editar Jornada',
            requiresAuth: true
        }
    },
    {
        path: '/usuarios',
        name: 'users',
        component: UsersPage,
        meta: { 
            title: 'Usuarios',
            requiresAuth: true
        }
    },
    {
        path: '/configuracion',
        name: 'settings',
        component: SettingsPage,
        meta: { 
            title: 'Configuración',
            requiresAuth: true
        }
    },
    {
        path: '/actividad',
        name: 'activity-logs',
        component: ActivityLogsPage,
        meta: { 
            title: 'Registro de Actividad',
            requiresAuth: true
        }
    },
    {
        path: '*',
        name: 'not-found',
        component: NotFoundPage,
        meta: { title: 'Página no encontrada' }
    }
];

const router = new VueRouter({
    mode: 'history',
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { x: 0, y: 0 };
        }
    }
});

// Guard de navegación para rutas protegidas
router.beforeEach((to, from, next) => {
    // Actualizar el título de la página
    if (to.meta.title) {
        document.title = `${to.meta.title} | Sistema BMX`;
    }

    // Verificar autenticación para rutas protegidas
    if (to.meta.requiresAuth) {
        const user = window.Laravel?.user;
        if (!user) {
            // Redirigir al login si no está autenticado
            window.location.href = '/login';
            return;
        }
    }

    next();
});

export default router;
