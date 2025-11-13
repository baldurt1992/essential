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
                    <!-- Dark Mode Toggle -->
                    <div class="admin-header__dark-mode-toggle">
                        <div class="widget widget_moreau_core_switcher" data-area="main-home-2">
                            <div class="widget qodef-switcher">
                                <div class="qodef-switch">
                                    <input type="checkbox" name="switch" class="qodef-switch-input" id="admin-dark-mode-switch"
                                        :checked="!isDarkMode" @change="toggleDarkMode">
                                    <label class="qodef-switch-label" for="admin-dark-mode-switch">
                                        <span class="qodef-on-off-switch-inner"></span>
                                        <span class="qodef-on-off-switch-switch">
                                            <span class="qodef-switch--light">
                                                <svg class="qodef-svg--sun" xmlns="http://www.w3.org/2000/svg"
                                                    width="19.298" height="19.298" viewBox="0 0 19.298 19.298">
                                                    <path class="qodef-path-fill"
                                                        d="M9.649 12.281a2.538 2.538 0 0 0 1.864-.768 2.538 2.538 0 0 0 .768-1.864 2.538 2.538 0 0 0-.768-1.864 2.538 2.538 0 0 0-1.864-.768 2.538 2.538 0 0 0-1.864.768 2.538 2.538 0 0 0-.768 1.864 2.538 2.538 0 0 0 .768 1.864 2.538 2.538 0 0 0 1.864.768Zm0 1.754a4.227 4.227 0 0 1-3.1-1.283 4.227 4.227 0 0 1-1.283-3.1 4.227 4.227 0 0 1 1.283-3.1 4.227 4.227 0 0 1 3.1-1.283 4.227 4.227 0 0 1 3.1 1.283 4.227 4.227 0 0 1 1.283 3.1 4.227 4.227 0 0 1-1.283 3.1 4.227 4.227 0 0 1-3.1 1.283Zm-6.14-3.509H0V8.772h3.509Zm15.79 0H15.79V8.772h3.51ZM8.773 3.508V0h1.754v3.509Zm0 15.79v-3.509h1.754v3.509ZM4.738 5.921 2.523 3.794 3.773 2.5l2.105 2.193ZM15.526 16.8 13.4 14.583l1.162-1.206 2.215 2.127ZM13.377 4.739l2.127-2.215 1.294 1.25-2.193 2.105ZM2.5 15.526l2.215-2.127 1.206 1.162-2.127 2.215Zm7.149-5.877Z">
                                                    </path>
                                                    <g class="qodef-path-outline" fill="none">
                                                        <circle cx="9.649" cy="9.649" r="4.227" />
                                                        <line x1="9.649" y1="0" x2="9.649" y2="3.509" />
                                                        <line x1="9.649" y1="15.79" x2="9.649" y2="19.298" />
                                                        <line x1="0" y1="9.649" x2="3.509" y2="9.649" />
                                                        <line x1="15.79" y1="9.649" x2="19.298" y2="9.649" />
                                                        <line x1="3.618" y1="3.618" x2="5.921" y2="5.921" />
                                                        <line x1="13.377" y1="13.377" x2="15.679" y2="15.679" />
                                                        <line x1="13.377" y1="5.921" x2="15.679" y2="3.618" />
                                                        <line x1="3.618" y1="15.679" x2="5.921" y2="13.377" />
                                                    </g>
                                                </svg>
                                            </span>
                                            <span class="qodef-switch--dark">
                                                <svg class="qodef-svg--moon" xmlns="http://www.w3.org/2000/svg"
                                                    width="19.298" height="19.298" viewBox="0 0 19.298 19.298">
                                                    <path class="qodef-path-fill"
                                                        d="M9.649 19.3a9.305 9.305 0 0 1-6.835-2.814A9.305 9.305 0 0 1 0 9.649a9.305 9.305 0 0 1 2.814-6.835A9.305 9.305 0 0 1 9.649 0q.375 0 .737.027t.71.08A5.674 5.674 0 0 0 9.34 2.131a5.692 5.692 0 0 0-.657 2.694 5.583 5.583 0 0 0 1.689 4.1 5.583 5.583 0 0 0 4.1 1.689 5.668 5.668 0 0 0 2.707-.657 5.707 5.707 0 0 0 2.01-1.756q.054.348.08.71t.027.737a9.305 9.305 0 0 1-2.814 6.835A9.305 9.305 0 0 1 9.649 19.3Zm0-2.144a7.261 7.261 0 0 0 4.235-1.3 7.519 7.519 0 0 0 2.734-3.391q-.536.134-1.072.214a7.22 7.22 0 0 1-1.072.08 7.648 7.648 0 0 1-5.615-2.318 7.648 7.648 0 0 1-2.318-5.615 7.218 7.218 0 0 1 .08-1.072q.08-.536.214-1.072a7.519 7.519 0 0 0-3.391 2.734 7.261 7.261 0 0 0-1.3 4.235 7.231 7.231 0 0 0 2.2 5.307 7.231 7.231 0 0 0 5.305 2.196Zm-.268-7.239Z">
                                                    </path>
                                                    <path class="qodef-path-outline" fill="none"
                                                        d="M9.649 19.3a9.305 9.305 0 0 1-6.835-2.814A9.305 9.305 0 0 1 0 9.649a9.305 9.305 0 0 1 2.814-6.835A9.305 9.305 0 0 1 9.649 0q.375 0 .737.027t.71.08A5.674 5.674 0 0 0 9.34 2.131a5.692 5.692 0 0 0-.657 2.694 5.583 5.583 0 0 0 1.689 4.1 5.583 5.583 0 0 0 4.1 1.689 5.668 5.668 0 0 0 2.707-.657 5.707 5.707 0 0 0 2.01-1.756q.054.348.08.71t.027.737a9.305 9.305 0 0 1-2.814 6.835A9.305 9.305 0 0 1 9.649 19.3Z">
                                                    </path>
                                                </svg>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
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
    const isDarkMode = ref(false);

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

    const toggleDarkMode = () => {
        isDarkMode.value = !isDarkMode.value;
        // Agregar/quitar clase dark-mode al body
        if (isDarkMode.value) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'true');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'false');
        }
    };

    const handleLogout = async () => {
        await auth.logout();
        router.push({ name: 'home' });
    };

    // Cargar preferencia del usuario al montar
    onMounted(() => {
        const savedDarkMode = localStorage.getItem('darkMode');
        if (savedDarkMode === 'true') {
            isDarkMode.value = true;
            document.body.classList.add('dark-mode');
        }
    });
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
        transition: transform 0.3s ease, background 0.3s ease;
    }

    body.dark-mode .admin-sidebar {
        background: rgba(23, 23, 23, 0.95) !important;
        border-right-color: rgba(255, 255, 255, 0.1) !important;
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
        background: var(--qode-background-color);
        overflow: hidden;
        transition: background 0.3s ease;
    }

    body.dark-mode .admin-main {
        background: rgba(23, 23, 23, 0.95) !important;
    }

    .admin-header {
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        background: var(--qode-background-color);
        backdrop-filter: blur(10px);
        position: sticky;
        top: 0;
        z-index: 2;
        transition: background 0.3s ease, border-color 0.3s ease;
    }

    body.dark-mode .admin-header {
        border-bottom-color: rgba(255, 255, 255, 0.1) !important;
        background: rgba(23, 23, 23, 0.95) !important;
    }

    .admin-header__burger {
        display: none;
        background: none;
        border: none;
        padding: 0;
        margin-right: 16px;
        cursor: pointer;
        color: var(--qode-text-color);
        transition: color 0.3s ease;
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
        color: var(--qode-text-color);
        transition: color 0.3s ease, opacity 0.3s ease;
    }

    .admin-header__profile {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .admin-header__dark-mode-toggle {
        display: flex;
        align-items: center;
    }

    .admin-header__user {
        display: flex;
        flex-direction: column;
        text-align: right;
        font-family: 'Inter', sans-serif;
        color: var(--qode-text-color);
        transition: color 0.3s ease;
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
        background: var(--qode-background-color);
        color: var(--qode-text-color);
        transition: background 0.3s ease, color 0.3s ease;
    }

    body.dark-mode .admin-content {
        background: rgba(23, 23, 23, 0.95) !important;
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

    /* Dark Mode Toggle Styles */
    .qodef-switcher {
        display: flex;
        align-items: center;
    }

    .qodef-switcher .qodef-switch {
        position: relative;
        width: 71px;
        margin: 0;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .qodef-switcher .qodef-switch .qodef-switch-input {
        display: none;
    }

    .qodef-switcher .qodef-switch .qodef-switch-label {
        display: block;
        overflow: hidden;
        cursor: pointer;
        border: 1px solid var(--qode-text-color);
        border-radius: 17px;
        height: 34px;
        margin-bottom: 0;
        background-color: var(--qode-background-color);
        transition: border-color 0.3s ease, background-color 0.3s ease;
    }

    .qodef-switcher .qodef-switch .qodef-on-off-switch-inner {
        display: block;
        transition: margin 0.3s ease-in 0s;
    }

    .qodef-switcher .qodef-switch .qodef-on-off-switch-switch {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 100%;
        padding: 0 10px;
    }

    .qodef-switcher .qodef-switch .qodef-on-off-switch-switch .qodef-switch--light {
        color: var(--qode-text-color);
        transition: color 0.3s ease;
    }

    .qodef-switcher .qodef-switch .qodef-on-off-switch-switch .qodef-switch--dark {
        color: var(--qode-text-color);
        opacity: 0.5;
        transition: color 0.3s ease, opacity 0.3s ease;
    }

    .qodef-switcher .qodef-switch .qodef-on-off-switch-switch svg {
        display: block;
    }

    .qodef-path-outline {
        display: none;
    }

    .qodef-path-fill {
        fill: currentColor;
        stroke: none;
    }

    .qodef-switch-input:checked+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--light {
        color: var(--qode-text-color);
        opacity: 1;
    }

    .qodef-switch-input:checked+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--light .qodef-path-fill {
        display: block;
        fill: currentColor;
        stroke: none;
    }

    .qodef-switch-input:checked+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--light .qodef-path-outline {
        display: none;
    }

    .qodef-switch-input:checked+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--dark {
        color: var(--qode-text-color);
        opacity: 0.5;
    }

    .qodef-switch-input:checked+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--dark .qodef-path-fill {
        display: none;
    }

    .qodef-switch-input:checked+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--dark .qodef-svg--moon {
        transform: scale(0.95);
        transform-origin: center;
    }

    .qodef-switch-input:checked+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--dark .qodef-path-outline {
        display: block;
        stroke: currentColor;
        stroke-width: 1.2;
    }

    .qodef-switch-input:checked+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--dark .qodef-path-outline circle,
    .qodef-switch-input:checked+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--dark .qodef-path-outline line,
    .qodef-switch-input:checked+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--dark .qodef-path-outline path {
        stroke: currentColor;
        stroke-width: 1.2;
        fill: none;
    }

    .qodef-switch-input:not(:checked)+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--light {
        color: var(--qode-text-color);
        opacity: 0.5;
    }

    .qodef-switch-input:not(:checked)+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--light .qodef-path-fill {
        display: none;
    }

    .qodef-switch-input:not(:checked)+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--light .qodef-path-outline {
        display: block;
        stroke: currentColor;
        stroke-width: 1.2;
    }

    .qodef-switch-input:not(:checked)+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--light .qodef-path-outline circle,
    .qodef-switch-input:not(:checked)+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--light .qodef-path-outline line {
        stroke: currentColor;
        stroke-width: 1.2;
        fill: none;
    }

    .qodef-switch-input:not(:checked)+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--dark {
        color: var(--qode-text-color);
        opacity: 1;
    }

    .qodef-switch-input:not(:checked)+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--dark .qodef-path-fill {
        display: block;
        fill: currentColor;
        stroke: none;
    }

    .qodef-switch-input:not(:checked)+.qodef-switch-label .qodef-on-off-switch-switch .qodef-switch--dark .qodef-path-outline {
        display: none;
    }
</style>
