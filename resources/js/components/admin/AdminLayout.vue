<template>
    <div class="admin-shell" :class="{ 'admin-shell--sidebar-open': sidebarOpen }">
        <aside class="admin-sidebar" :class="{ 'admin-sidebar--mobile-open': sidebarOpen }">
            <div class="admin-brand">
                <span class="admin-brand__title">Essential Admin</span>
                <span class="admin-brand__subtitle">Control Panel</span>
            </div>

            <nav class="admin-nav">
                <RouterLink v-for="item in navItems" :key="item.name" :to="{ name: item.name }" class="admin-nav__link"
                    :class="{ 'admin-nav__link--active': route.name === item.name }">
                    <i :class="['pi', item.icon, 'admin-nav__icon']"></i>
                    <span>{{ item.label }}</span>
                </RouterLink>
            </nav>

            <div class="admin-sidebar__footer">
                <RouterLink :to="{ name: 'home' }"
                    class="admin-sidebar__home-button qodef-button qodef-button--primary">
                    <i class="pi pi-home"></i>
                    <span>Home</span>
                </RouterLink>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-header">
                <button class="admin-header__burger" @click="toggleSidebar" aria-label="Abrir menú" v-if="isMobile">
                    <span class="admin-header__burger-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 41.477 41.477">
                            <g>
                                <g transform="translate(8.257 8.257)">
                                    <ellipse cx="12.715" cy="12.715" rx="12.715" ry="12.715" fill="none"
                                        stroke="currentColor" stroke-width="1.5"></ellipse>
                                    <ellipse cx="12.715" cy="12.715" rx="11.715" ry="11.715" fill="none"
                                        stroke="currentColor" stroke-width="1.5"></ellipse>
                                </g>
                                <g>
                                    <path d="M20.737 0v41.478" fill="none" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round"></path>
                                    <path d="M41.477 20.739H0" fill="none" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round"></path>
                                </g>
                            </g>
                        </svg>
                    </span>
                </button>
                <div class="admin-header__info">
                    <img src="/images/liquid-removebg-preview-100x100.png" alt="Essential Innovation"
                        class="admin-header__logo" />
                    <p class="admin-header__subtitle">Todo en orden · Última actualización automática</p>
                </div>
                <div class="admin-header__profile">
                    <div class="admin-header__user">
                        <span class="admin-header__user-name">{{ auth.user.value?.name }}</span>
                        <span class="admin-header__user-email">{{ auth.user.value?.email }}</span>
                    </div>
                    <button type="button" class="admin-header__logout-button qodef-button qodef-button--ghost"
                        @click="handleLogout" v-tooltip.bottom="'Cerrar sesión'" aria-label="Cerrar sesión">
                        <i class="pi pi-sign-out"></i>
                    </button>
                </div>
            </header>

            <main class="admin-content">
                <RouterView />
            </main>
        </div>

        <div v-if="isMobile" class="admin-sidebar__backdrop"
            :class="{ 'admin-sidebar__backdrop--visible': sidebarOpen }" @click="closeSidebar"></div>
    </div>
</template>

<script setup>
    import { computed, onMounted, onBeforeUnmount, reactive, ref } from 'vue';
    import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router';
    import { useAuth } from '../../composables/useAuth.js';

    const auth = useAuth();
    const route = useRoute();
    const router = useRouter();

    const breakpoint = 880;
    const isMobile = ref(typeof window !== 'undefined' ? window.innerWidth <= breakpoint : false);

    const handleResize = () => {
        isMobile.value = window.innerWidth <= breakpoint;
        if (!isMobile.value) {
            state.sidebarOpen = false;
        }
    };

    onMounted(() => {
        handleResize();
        window.addEventListener('resize', handleResize, { passive: true });
    });

    onBeforeUnmount(() => {
        window.removeEventListener('resize', handleResize);
    });

    const state = reactive({
        sidebarOpen: false,
    });

    const sidebarOpen = computed({
        get: () => state.sidebarOpen,
        set: (value) => {
            state.sidebarOpen = value;
        }
    });

    const toggleSidebar = () => {
        state.sidebarOpen = !state.sidebarOpen;
    };

    const closeSidebar = () => {
        state.sidebarOpen = false;
    };

    const navItems = computed(() => ([
        { label: 'Resumen', name: 'admin.dashboard', icon: 'pi-home' },
        { label: 'Plantillas', name: 'admin.templates', icon: 'pi-clone' },
        { label: 'Planes', name: 'admin.plans', icon: 'pi-briefcase' },
        { label: 'Suscripciones', name: 'admin.subscriptions', icon: 'pi-id-card' },
        { label: 'Servicios', name: 'admin.services', icon: 'pi-list' },
        { label: 'Contacto', name: 'admin.contact', icon: 'pi-envelope' },
    ]));

    const handleLogout = async () => {
        await auth.logout();
        router.push({ name: 'home' });
    };
</script>

<style scoped>
    .admin-shell {
        height: 100vh;
        display: grid;
        grid-template-columns: 260px 1fr;
        background: var(--qode-background-color);
        color: var(--qode-text-color);
        overflow: hidden;
    }

    .admin-shell--sidebar-open {
        overflow: hidden;
    }

    .admin-sidebar {
        background: linear-gradient(180deg, #101010 0%, #1f1f1f 100%);
        color: #ffffff;
        padding: 28px 22px;
        display: flex;
        flex-direction: column;
        gap: 24px;
        border-right: 1px solid rgba(255, 255, 255, 0.08);
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
        overscroll-behavior: contain;
        transition: transform 0.3s ease;
    }

    .admin-sidebar--mobile-open {
        transform: translateX(0);
    }

    .admin-brand {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .admin-brand__title {
        font-family: 'Space Mono', monospace;
        font-size: 19px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .admin-brand__subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        opacity: 0.7;
    }

    .admin-nav {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-top: 12px;
        flex: 1;
    }

    .admin-nav__link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 12px;
        color: #f3f3f3;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 13px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .admin-nav__link:hover {
        background: rgba(221, 51, 51, 0.2);
        color: #ffffff;
    }

    .admin-nav__link--active {
        background: #dd3333;
        color: #ffffff;
    }

    .admin-nav__link--active:hover {
        background: #c42b2b;
        color: #ffffff;
    }

    .admin-nav__icon {
        font-size: 14px;
    }

    .admin-sidebar__footer {
        margin-top: auto;
    }

    .admin-sidebar__home-button {
        width: 100%;
    }

    .admin-main {
        display: flex;
        flex-direction: column;
        height: 100vh;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.9) 0%, rgba(240, 240, 240, 0.9) 100%);
        overflow: hidden;
    }

    .admin-header {
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .admin-header__burger {
        display: none;
        background: none;
        border: none;
        padding: 0;
        margin-right: 16px;
        cursor: pointer;
        color: #171717;
    }

    .admin-header__burger-icon {
        display: inline-flex;
    }

    .admin-header__info {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .admin-header__logo {
        width: 64px;
        height: 64px;
        object-fit: contain;
        pointer-events: none;
    }

    .admin-header__subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        opacity: 0.75;
        margin: 0;
    }

    .admin-header__profile {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .admin-header__user {
        display: flex;
        flex-direction: column;
        text-align: right;
        font-family: 'Inter', sans-serif;
    }

    .admin-header__user-name {
        font-weight: 600;
    }

    .admin-header__user-email {
        font-size: 13px;
        opacity: 0.7;
    }

    .admin-header__logout-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        padding: 0;
    }

    .admin-header__logout-button:hover {
        background: #000000;
    }

    .admin-header__logout-button:active {
        transform: translateY(0);
    }

    .admin-header__logout-button .pi {
        font-size: 16px;
    }

    .admin-content {
        flex: 1;
        padding: 28px 36px 40px;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    @media (max-width: 1024px) {
        .admin-shell {
            height: 100vh;
            grid-template-columns: 220px 1fr;
        }

        .admin-content {
            padding: 24px 28px 36px;
        }
    }

    @media (max-width: 880px) {
        .admin-shell {
            grid-template-columns: 1fr;
            height: auto;
        }

        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            transform: translateX(-100%);
            z-index: 10;
        }

        .admin-sidebar--mobile-open {
            transform: translateX(0);
        }

        .admin-main {
            height: auto;
        }

        .admin-header {
            position: sticky;
            top: 0;
        }

        .admin-header__burger {
            display: inline-flex;
        }

        .admin-header__info {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .admin-header__logo {
            width: 56px;
            height: 56px;
        }

        .admin-header__subtitle {
            font-size: 13px;
        }

        .admin-header__profile {
            display: none;
        }

        .admin-content {
            padding: 24px 22px 40px;
            overflow: visible;
        }
    }

    .admin-sidebar__backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        z-index: 9;
    }

    .admin-sidebar__backdrop--visible {
        opacity: 1;
        pointer-events: all;
    }
</style>
