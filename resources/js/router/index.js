import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth.js';
import HomeLayout from '../components/layout/HomeLayout.vue';
import HomePage from '../components/pages/HomePage.vue';
import ServicesPage from '../components/pages/ServicesPage.vue';
import TemplatesPage from '../components/pages/TemplatesPage.vue';
import PlansPage from '../components/pages/PlansPage.vue';
import ContactPage from '../components/pages/ContactPage.vue';
import AdminLayout from '../components/admin/AdminLayout.vue';
import AdminDashboardPage from '../components/admin/pages/AdminDashboardPage.vue';
import AdminTemplatesPage from '../components/admin/pages/AdminTemplatesPage.vue';
import AdminPlansPage from '../components/admin/pages/AdminPlansPage.vue';
import AdminSubscriptionsPage from '../components/admin/pages/AdminSubscriptionsPage.vue';
import AdminServicesPage from '../components/admin/pages/AdminServicesPage.vue';
import AdminContactSettingsPage from '../components/admin/pages/AdminContactSettingsPage.vue';
import ClientLayout from '../components/client/ClientLayout.vue';
import ClientDashboardPage from '../components/client/pages/ClientDashboardPage.vue';
import ClientSubscriptionsPage from '../components/client/pages/ClientSubscriptionsPage.vue';
import ClientPurchasesPage from '../components/client/pages/ClientPurchasesPage.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: HomeLayout,
            children: [
                {
                    path: '',
                    name: 'home',
                    component: HomePage,
                },
                {
                    path: 'servicios',
                    name: 'services',
                    component: ServicesPage,
                },
                {
                    path: 'plantillas',
                    name: 'templates',
                    component: TemplatesPage,
                },
                {
                    path: 'planes',
                    name: 'plans',
                    component: PlansPage,
                },
                {
                    path: 'contacto',
                    name: 'contact',
                    component: ContactPage,
                },
            ],
        },
        {
            path: '/admin',
            component: AdminLayout,
            meta: {
                requiresAuth: true,
                requiresAdmin: true,
            },
            children: [
                {
                    path: '',
                    name: 'admin.dashboard',
                    component: AdminDashboardPage,
                },
                {
                    path: 'templates',
                    name: 'admin.templates',
                    component: AdminTemplatesPage,
                },
                {
                    path: 'plans',
                    name: 'admin.plans',
                    component: AdminPlansPage,
                },
                {
                    path: 'subscriptions',
                    name: 'admin.subscriptions',
                    component: AdminSubscriptionsPage,
                },
                {
                    path: 'services',
                    name: 'admin.services',
                    component: AdminServicesPage,
                },
                {
                    path: 'contact',
                    name: 'admin.contact',
                    component: AdminContactSettingsPage,
                },
            ],
        },
        {
            path: '/mi-cuenta',
            component: ClientLayout,
            meta: {
                requiresAuth: true,
                requiresClient: true,
            },
            children: [
                {
                    path: '',
                    name: 'client.dashboard',
                    component: ClientDashboardPage,
                },
                {
                    path: 'suscripciones',
                    name: 'client.subscriptions',
                    component: ClientSubscriptionsPage,
                },
                {
                    path: 'compras',
                    name: 'client.purchases',
                    component: ClientPurchasesPage,
                },
            ],
        },
        {
            path: '/:pathMatch(.*)*',
            redirect: '/',
        },
    ],
    scrollBehavior: () => ({ top: 0 }),
});

router.beforeEach(async (to, from, next) => {
    const auth = useAuth();

    if (!auth.state.initialized) {
        await auth.init();
    }

    let isAuthenticated = !!auth.user.value;
    let userRoles = auth.user.value?.roles ?? [];
    let isAdmin = userRoles.some((role) => role.name === 'admin');
    let isClient = userRoles.some((role) => role.name === 'client');

    if ((to.meta.requiresAdmin || to.meta.requiresClient) && !isAuthenticated) {
        await auth.refreshUser({ silent: true });
        isAuthenticated = !!auth.user.value;
        userRoles = auth.user.value?.roles ?? [];
        isAdmin = userRoles.some((role) => role.name === 'admin');
        isClient = userRoles.some((role) => role.name === 'client');
    }

    console.log('[router][guard]', {
        to: to.fullPath,
        isAuthenticated,
        isAdmin,
        isClient,
        user: auth.user.value,
    });

    if (to.meta.requiresAdmin) {
        if (!isAuthenticated) {
            console.warn('[router][guard] Usuario no autenticado. Redirigiendo a home.');
            return next({ name: 'home' });
        }

        if (!isAdmin) {
            console.warn('[router][guard] Usuario sin rol admin. Redirigiendo a home.');
            return next({ name: 'home' });
        }
    }

    if (to.meta.requiresClient) {
        if (!isAuthenticated) {
            console.warn('[router][guard] Usuario no autenticado. Redirigiendo a home.');
            return next({ name: 'home' });
        }

        if (!isClient) {
            console.warn('[router][guard] Usuario sin rol client. Redirigiendo a home.');
            return next({ name: 'home' });
        }
    }

    return next();
});

export default router;
