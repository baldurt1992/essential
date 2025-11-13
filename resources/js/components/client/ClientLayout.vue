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
                    <!-- Dark Mode Toggle -->
                    <div class="client-header__dark-mode-toggle">
                        <div class="widget widget_moreau_core_switcher" data-area="main-home-2">
                            <div class="widget qodef-switcher">
                                <div class="qodef-switch">
                                    <input type="checkbox" name="switch" class="qodef-switch-input" id="client-dark-mode-switch"
                                        :checked="!isDarkMode" @change="toggleDarkMode">
                                    <label class="qodef-switch-label" for="client-dark-mode-switch">
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
        { label: 'Resumen', name: 'client.dashboard', icon: 'pi-home' },
        { label: 'Suscripciones', name: 'client.subscriptions', icon: 'pi-id-card' },
        { label: 'Mis Compras', name: 'client.purchases', icon: 'pi-shopping-bag' },
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
        transition: transform 0.3s ease, background 0.3s ease;
        z-index: 100;
    }

    body.dark-mode .client-sidebar {
        background: linear-gradient(180deg, #0a0a0a 0%, #151515 100%);
        border-right-color: rgba(255, 255, 255, 0.12);
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
        color: var(--qode-heading-color);
        transition: color 0.3s ease;
    }

    .client-header__subtitle {
        margin: 4px 0 0;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        opacity: 0.7;
        color: var(--qode-text-color);
        transition: color 0.3s ease, opacity 0.3s ease;
    }

    .client-header__profile {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .client-header__dark-mode-toggle {
        display: flex;
        align-items: center;
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
        color: var(--qode-text-color);
        transition: color 0.3s ease;
    }

    .client-header__user-email {
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        opacity: 0.7;
        color: var(--qode-text-color);
        transition: color 0.3s ease, opacity 0.3s ease;
    }

    .client-header__logout-button {
        padding: 10px;
    }

    .client-content {
        flex: 1;
        overflow-y: auto;
        padding: 32px;
        background: var(--qode-background-color);
        color: var(--qode-text-color);
        transition: background 0.3s ease, color 0.3s ease;
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

