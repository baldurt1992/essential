<template>
    <div class="client-shell" :class="{ 'client-shell--sidebar-open': sidebarOpen }">
        <aside class="client-sidebar" :class="{ 'client-sidebar--mobile-open': sidebarOpen }">
            <div class="client-brand">
                <span class="client-brand__title">Mi Cuenta</span>
                <span class="client-brand__subtitle">Essential</span>
            </div>

            <nav class="client-nav">
                <RouterLink v-for="item in navItems" :key="item.name" :to="{ name: item.name }" class="client-nav__link"
                    :class="{ 'client-nav__link--active': route.name === item.name }">
                    <i :class="['pi', item.icon, 'client-nav__icon']"></i>
                    <span>{{ item.label }}</span>
                </RouterLink>
            </nav>

            <div class="client-sidebar__footer">
                <RouterLink :to="{ name: 'home' }"
                    class="client-sidebar__home-button qodef-button qodef-button--primary">
                    <i class="pi pi-home"></i>
                    <span>Volver al sitio</span>
                </RouterLink>
            </div>
        </aside>

        <div class="client-main">
            <header class="client-header">
                <button class="client-header__burger" @click="toggleSidebar" aria-label="Abrir menú" v-if="isMobile">
                    <span class="client-header__burger-icon">
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
                <div class="client-header__info">
                    <h1 class="client-header__title">Mi Cuenta</h1>
                    <p class="client-header__subtitle">Gestiona tus suscripciones y compras</p>
                </div>
                <div class="client-header__profile">
                    <div class="client-header__user">
                        <span class="client-header__user-name">{{ auth.user.value?.name }}</span>
                        <span class="client-header__user-email">{{ auth.user.value?.email }}</span>
                    </div>
                    <button type="button" class="client-header__logout-button qodef-button qodef-button--ghost"
                        @click="handleLogout" v-tooltip.bottom="'Cerrar sesión'" aria-label="Cerrar sesión">
                        <i class="pi pi-sign-out"></i>
                    </button>
                </div>
            </header>

            <main class="client-content">
                <RouterView />
            </main>
        </div>

        <div v-if="isMobile" class="client-sidebar__backdrop"
            :class="{ 'client-sidebar__backdrop--visible': sidebarOpen }" @click="closeSidebar"></div>
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
        { label: 'Resumen', name: 'client.dashboard', icon: 'pi-home' },
        { label: 'Suscripciones', name: 'client.subscriptions', icon: 'pi-id-card' },
        { label: 'Mis Compras', name: 'client.purchases', icon: 'pi-shopping-bag' },
    ]));

    const handleLogout = async () => {
        await auth.logout();
        router.push({ name: 'home' });
    };
</script>

<style scoped>
    .client-shell {
        height: 100vh;
        display: grid;
        grid-template-columns: 260px 1fr;
        background: var(--qode-background-color);
        color: var(--qode-text-color);
        overflow: hidden;
    }

    .client-shell--sidebar-open {
        overflow: hidden;
    }

    .client-sidebar {
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
        z-index: 100;
    }

    @media (max-width: 880px) {
        .client-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            transform: translateX(-100%);
            width: 260px;
        }

        .client-sidebar--mobile-open {
            transform: translateX(0);
        }
    }

    .client-brand {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .client-brand__title {
        font-family: 'Space Mono', monospace;
        font-size: 19px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .client-brand__subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        opacity: 0.7;
    }

    .client-nav {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-top: 12px;
        flex: 1;
    }

    .client-nav__link {
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
        text-decoration: none;
    }

    .client-nav__link:hover {
        background: rgba(221, 51, 51, 0.2);
        color: #ffffff;
    }

    .client-nav__link--active {
        background: #dd3333;
        color: #ffffff;
    }

    .client-nav__link--active:hover {
        background: #c42b2b;
        color: #ffffff;
    }

    .client-nav__icon {
        font-size: 14px;
    }

    .client-sidebar__footer {
        margin-top: auto;
    }

    .client-sidebar__home-button {
        width: 100%;
        justify-content: center;
    }

    .client-main {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .client-header {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px 32px;
        border-bottom: 1px solid rgba(23, 23, 23, 0.1);
        background: var(--qode-background-color);
    }

    body.dark-mode .client-header {
        border-bottom-color: rgba(255, 255, 255, 0.08);
    }

    .client-header__burger {
        display: none;
        background: none;
        border: none;
        color: var(--qode-text-color);
        cursor: pointer;
        padding: 8px;
    }

    @media (max-width: 880px) {
        .client-header__burger {
            display: block;
        }
    }

    .client-header__info {
        flex: 1;
    }

    .client-header__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: 24px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .client-header__subtitle {
        margin: 4px 0 0;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        opacity: 0.7;
    }

    .client-header__profile {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .client-header__user {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 2px;
    }

    .client-header__user-name {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 13px;
        font-weight: 500;
    }

    .client-header__user-email {
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        opacity: 0.7;
    }

    .client-header__logout-button {
        padding: 10px;
    }

    .client-content {
        flex: 1;
        overflow-y: auto;
        padding: 32px;
    }

    @media (max-width: 880px) {
        .client-content {
            padding: 20px;
        }
    }

    .client-sidebar__backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 99;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    @media (max-width: 880px) {
        .client-sidebar__backdrop {
            display: block;
        }

        .client-sidebar__backdrop--visible {
            opacity: 1;
            pointer-events: auto;
        }
    }
</style>

