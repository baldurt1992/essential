import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth.js';
import HomeLayout from '../components/layout/HomeLayout.vue';
import HomePage from '../components/pages/HomePage.vue';
import AdminLayout from '../components/admin/AdminLayout.vue';
import AdminDashboardPage from '../components/admin/pages/AdminDashboardPage.vue';
import AdminTemplatesPage from '../components/admin/pages/AdminTemplatesPage.vue';
import AdminPlansPage from '../components/admin/pages/AdminPlansPage.vue';
import AdminSubscriptionsPage from '../components/admin/pages/AdminSubscriptionsPage.vue';
import AdminServicesPage from '../components/admin/pages/AdminServicesPage.vue';
import AdminPopularServicesPage from '../components/admin/pages/AdminPopularServicesPage.vue';
import AdminContactSettingsPage from '../components/admin/pages/AdminContactSettingsPage.vue';

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
                    path: 'popular-services',
                    name: 'admin.popular-services',
                    component: AdminPopularServicesPage,
                },
                {
                    path: 'contact',
                    name: 'admin.contact',
                    component: AdminContactSettingsPage,
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

    if (to.meta.requiresAdmin && !isAuthenticated) {
        await auth.refreshUser({ silent: true });
        isAuthenticated = !!auth.user.value;
        userRoles = auth.user.value?.roles ?? [];
        isAdmin = userRoles.some((role) => role.name === 'admin');
    }

    console.log('[router][guard]', {
        to: to.fullPath,
        isAuthenticated,
        isAdmin,
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

    return next();
});

export default router;
