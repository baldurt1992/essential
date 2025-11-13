<template>
    <header id="qodef-page-header"
        :class="['qodef-header--standard-no-logo', { 'qodef-header--transparent': shouldBeTransparent }]" role="banner">
        <div id="qodef-page-header-inner">
            <div class="qodef-header-wrapper">
                <!-- Widget Holder Left - Dark/Light Switcher -->
                <div class="qodef-widget-holder qodef--two">
                    <div class="widget widget_moreau_core_switcher" data-area="main-home-2">
                        <div class="widget qodef-switcher">
                            <div class="qodef-switch">
                                <input type="checkbox" name="switch" class="qodef-switch-input" id="qodef-switch-88"
                                    :checked="!isDarkMode" @change="toggleDarkMode">
                                <label class="qodef-switch-label" for="qodef-switch-88">
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

                <!-- Navigation -->
                <nav class="qodef-header-navigation" role="navigation" aria-label="Top Menu">
                    <ul id="menu-main-menu-1" class="menu">
                        <NavMenuItem text="INICIO" :link="{ name: 'home' }" />
                        <NavMenuItem text="SERVICIOS" :link="{ name: 'services' }" />
                        <NavMenuItem text="PLANTILLAS" :link="{ name: 'templates' }" :has-children="true" :sub-items="[
                            { id: 1, text: 'Flyers', link: { name: 'templates', query: { category: 'flyer' } } },
                            { id: 2, text: 'Novedades', link: { name: 'templates', query: { fresh: '1' } } },
                            { id: 3, text: 'Populares', link: { name: 'templates', query: { popular: '1' } } }
                        ]" />
                        <NavMenuItem text="CONTATO" :link="{ name: 'contact' }" />
                        <NavMenuItem text="PLANES" :link="{ name: 'plans' }" />
                        <li v-if="!isLoggedIn" class="menu-item auth-nav-item">
                            <div class="auth-nav-group">
                                <button type="button" class="auth-nav-button auth-nav-button--ghost"
                                    @click.prevent="openAuthModal('login')">
                                    Ingresar
                                </button>
                                <button type="button" class="auth-nav-button auth-nav-button--primary"
                                    @click.prevent="openAuthModal('register')">
                                    Registrarse
                                </button>
                            </div>
                        </li>
                        <li v-else class="menu-item auth-nav-item">
                            <div class="auth-nav-group">
                                <RouterLink :to="accountLink" class="auth-nav-button auth-nav-button--ghost">
                                    {{ headerAccountLabel }}
                                </RouterLink>
                                <button type="button" class="auth-nav-button auth-nav-button--primary"
                                    @click.prevent="handleLogout">
                                    {{ isAdmin ? 'Cerrar sesión' : 'Salir' }}
                                </button>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Widget Holder Right - Side Area Opener / Burger Menu -->
                <div class="qodef-widget-holder qodef--one">
                    <!-- Mobile Menu Opener (uses side area SVG) -->
                    <button class="qodef-mobile-menu-opener" @click="toggleMobileMenu" aria-label="Toggle menu">
                        <span class="qodef-m-icon">
                            <svg class="qodef-svg--menu" xmlns="http://www.w3.org/2000/svg" width="41.477"
                                height="41.477" viewBox="0 0 41.477 41.477">
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

                    <!-- Side Area Opener (Desktop Only) -->
                    <div class="widget widget_moreau_core_side_area_opener qodef-desktop-only" data-area="main-home">
                        <a href="javascript:void(0)"
                            class="qodef-opener-icon qodef-m qodef-source--predefined qodef-side-area-opener"
                            @click="toggleSideArea">
                            <span class="qodef-m-icon qodef--open">
                                <svg class="qodef-svg--menu" xmlns="http://www.w3.org/2000/svg" width="41.477"
                                    height="41.477" viewBox="0 0 41.477 41.477">
                                    <g>
                                        <g transform="translate(8.257 8.257)">
                                            <ellipse cx="12.715" cy="12.715" rx="12.715" ry="12.715" fill="none"
                                                stroke="currentColor" stroke-width="1.5"></ellipse>
                                            <ellipse cx="12.715" cy="12.715" rx="11.715" ry="11.715" fill="none"
                                                stroke="currentColor" stroke-width="1.5"></ellipse>
                                        </g>
                                        <g>
                                            <path d="M20.737 0v41.478" fill="none" stroke="currentColor"
                                                stroke-width="1.5" stroke-linecap="round"></path>
                                            <path d="M41.477 20.739H0" fill="none" stroke="currentColor"
                                                stroke-width="1.5" stroke-linecap="round"></path>
                                        </g>
                                    </g>
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (includes navigation and side area content) -->
        <div class="qodef-mobile-menu" :class="{ 'qodef--opened': isMobileMenuOpen }">
            <!-- Close Button inside menu -->
            <button class="qodef-mobile-menu-close" @click="closeMobileMenu" aria-label="Close menu">
                <svg class="qodef-svg--close" xmlns="http://www.w3.org/2000/svg" width="41.477" height="41.477"
                    viewBox="0 0 41.477 41.477">
                    <g>
                        <g transform="translate(8.257 8.257)">
                            <ellipse cx="12.715" cy="12.715" rx="12.715" ry="12.715" fill="none" stroke="currentColor"
                                stroke-width="1.5"></ellipse>
                            <ellipse cx="12.715" cy="12.715" rx="11.715" ry="11.715" fill="none" stroke="currentColor"
                                stroke-width="1.5"></ellipse>
                        </g>
                        <g>
                            <path d="M20.737 0v41.478" fill="none" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round"></path>
                            <path d="M41.477 20.739H0" fill="none" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round"></path>
                        </g>
                    </g>
                </svg>
            </button>

            <nav class="qodef-mobile-menu-navigation">
                <ul class="qodef-mobile-menu-list">
                    <li>
                        <RouterLink :to="{ name: 'home' }" @click="closeMobileMenu">INICIO</RouterLink>
                    </li>
                    <li>
                        <RouterLink :to="{ name: 'services' }" @click="closeMobileMenu">SERVICIOS</RouterLink>
                    </li>
                    <li class="qodef-has-submenu">
                        <div class="qodef-mobile-submenu-header">
                            <RouterLink :to="{ name: 'templates' }" @click="closeMobileMenu">PLANTILLAS</RouterLink>
                            <button type="button" class="qodef-submenu-toggle"
                                aria-label="Mostrar submenú de plantillas" @click="toggleSubmenu($event)">
                                <span></span>
                                <span></span>
                            </button>
                        </div>
                        <ul class="qodef-submenu">
                            <li>
                                <RouterLink :to="{ name: 'templates', query: { category: 'flyer' } }"
                                    @click="closeMobileMenu">Flyers</RouterLink>
                            </li>
                            <li>
                                <RouterLink :to="{ name: 'templates', query: { fresh: '1' } }" @click="closeMobileMenu">
                                    Novedades</RouterLink>
                            </li>
                            <li>
                                <RouterLink :to="{ name: 'templates', query: { popular: '1' } }"
                                    @click="closeMobileMenu">Populares</RouterLink>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <RouterLink :to="{ name: 'contact' }" @click="closeMobileMenu">CONTATO</RouterLink>
                    </li>
                    <li>
                        <RouterLink :to="{ name: 'templates' }" @click="closeMobileMenu">PLANTILLAS</RouterLink>
                    </li>
                    <li>
                        <RouterLink :to="{ name: 'plans' }" @click="closeMobileMenu">PLANES</RouterLink>
                    </li>
                    <li v-if="!isLoggedIn" class="mobile-auth-item">
                        <button type="button" class="mobile-auth-button"
                            @click="openAuthModal('login')">Ingresar</button>
                    </li>
                    <li v-if="!isLoggedIn" class="mobile-auth-item">
                        <button type="button" class="mobile-auth-button mobile-auth-button--primary"
                            @click="openAuthModal('register')">Crear cuenta</button>
                    </li>
                    <li v-if="isLoggedIn" class="mobile-auth-item">
                        <RouterLink :to="accountLink" class="mobile-auth-link" @click="closeMobileMenu">{{
                            mobileAccountLabel
                            }}</RouterLink>
                    </li>
                    <li v-if="isLoggedIn" class="mobile-auth-item">
                        <button type="button" class="mobile-auth-button mobile-auth-button--primary"
                            @click="handleLogout">Cerrar sesión</button>
                    </li>
                </ul>
            </nav>

            <!-- Side Area Content in Mobile Menu -->
            <div class="qodef-mobile-side-area">
                <div class="qodef-mobile-logo">
                    <img src="/images/logo-web-negro.png" alt="Essential Innovation">
                </div>
                <div class="qodef-mobile-separator"></div>
                <h2 class="qodef-mobile-title">PONTE EN CONTACTO</h2>
                <div class="qodef-mobile-company">
                    ESSENTIAL INNOVATION<br>
                    CREATIVE DESINGS
                </div>
                <div class="qodef-mobile-email">
                    <a href="mailto:infoessential.innovation@gmail.com">infoessential.innovation@gmail.com</a>
                </div>
            </div>
        </div>

        <!-- Header Sticky (duplicado para scroll) -->
        <div class="qodef-header-sticky qodef-header-sticky--normal qodef-custom-header-layout qodef-appearance--down">
            <div class="qodef-header-sticky-inner">
                <div class="qodef-widget-holder qodef--two">
                    <div class="widget widget_moreau_core_switcher" data-area="main-home-2">
                        <div class="widget qodef-switcher">
                            <div class="qodef-switch">
                                <input type="checkbox" name="switch" class="qodef-switch-input" id="qodef-switch-189"
                                    :checked="!isDarkMode" @change="toggleDarkMode">
                                <label class="qodef-switch-label" for="qodef-switch-189">
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

                <nav class="qodef-header-navigation" role="navigation" aria-label="Top Menu">
                    <ul id="menu-main-menu-2" class="menu">
                        <NavMenuItem text="INICIO" :link="{ name: 'home' }" />
                        <NavMenuItem text="SERVICIOS" :link="{ name: 'services' }" />
                        <NavMenuItem text="PLANTILLAS" :link="{ name: 'templates' }" :has-children="true" :sub-items="[
                            { id: 1, text: 'Flyers', link: { name: 'templates', query: { category: 'flyer' } } },
                            { id: 2, text: 'Novedades', link: { name: 'templates', query: { fresh: '1' } } },
                            { id: 3, text: 'Populares', link: { name: 'templates', query: { popular: '1' } } }
                        ]" />
                        <NavMenuItem text="CONTATO" link="/contacto" />

                        <NavMenuItem text="PLANES" :link="{ name: 'plans' }" />
                        <li v-if="!isLoggedIn" class="menu-item auth-nav-item">
                            <div class="auth-nav-group">
                                <button type="button" class="auth-nav-button auth-nav-button--ghost"
                                    @click.prevent="openAuthModal('login')">
                                    Ingresar
                                </button>
                                <button type="button" class="auth-nav-button auth-nav-button--primary"
                                    @click.prevent="openAuthModal('register')">
                                    Registrarse
                                </button>
                            </div>
                        </li>
                        <li v-else class="menu-item auth-nav-item">
                            <div class="auth-nav-group">
                                <RouterLink :to="accountLink" class="auth-nav-button auth-nav-button--ghost">
                                    {{ headerAccountLabel }}
                                </RouterLink>
                                <button type="button" class="auth-nav-button auth-nav-button--primary"
                                    @click.prevent="handleLogout">
                                    {{ isAdmin ? 'Cerrar sesión' : 'Salir' }}
                                </button>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div class="qodef-widget-holder qodef--one">
                    <button class="qodef-mobile-menu-opener" @click="toggleMobileMenu" aria-label="Toggle menu">
                        <span class="qodef-m-icon">
                            <svg class="qodef-svg--menu" xmlns="http://www.w3.org/2000/svg" width="41.477"
                                height="41.477" viewBox="0 0 41.477 41.477">
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

                    <div class="widget widget_moreau_core_side_area_opener qodef-desktop-only" data-area="main-home">
                        <a href="javascript:void(0)"
                            class="qodef-opener-icon qodef-m qodef-source--predefined qodef-side-area-opener"
                            @click="toggleSideArea">
                            <span class="qodef-m-icon qodef--open">
                                <svg class="qodef-svg--menu" xmlns="http://www.w3.org/2000/svg" width="41.477"
                                    height="41.477" viewBox="0 0 41.477 41.477">
                                    <g>
                                        <g transform="translate(8.257 8.257)">
                                            <ellipse cx="12.715" cy="12.715" rx="12.715" ry="12.715" fill="none"
                                                stroke="currentColor" stroke-width="1.5"></ellipse>
                                            <ellipse cx="12.715" cy="12.715" rx="11.715" ry="11.715" fill="none"
                                                stroke="currentColor" stroke-width="1.5"></ellipse>
                                        </g>
                                        <g>
                                            <path d="M20.737 0v41.478" fill="none" stroke="currentColor"
                                                stroke-width="1.5" stroke-linecap="round"></path>
                                            <path d="M41.477 20.739H0" fill="none" stroke="currentColor"
                                                stroke-width="1.5" stroke-linecap="round"></path>
                                        </g>
                                    </g>
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Side Area (fuera del header para que cubra toda la pantalla) -->
    <SideArea :isOpen="isSideAreaOpen" @update:isOpen="isSideAreaOpen = $event" @close="closeSideArea" />

    <AuthModal v-model="authModalVisible" :form="authModalForm" @update:form="(value) => (authModalForm = value)"
        @success="handleAuthSuccess" />
</template>

<script setup>
    import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
    import { RouterLink, useRoute } from 'vue-router';
    import SideArea from './SideArea.vue';
    import NavMenuItem from './NavMenuItem.vue';
    import AuthModal from '../auth/AuthModal.vue';
    import { useAuth } from '../../composables/useAuth';
    import { useAuthModal } from '../../composables/useAuthModal';

    const auth = useAuth();
    const route = useRoute();
    const isDarkMode = ref(false);
    const isSideAreaOpen = ref(false);
    const isMobileMenuOpen = ref(false);

    // Usar el composable global para el modal de autenticación
    const { authModalVisible, authModalForm, openAuthModal: openAuthModalGlobal, closeAuthModal } = useAuthModal();

    const isLoggedIn = computed(() => auth.isAuthenticated.value);
    const isAdmin = computed(() => auth.isAdmin.value);
    const accountLink = computed(() => (isAdmin.value ? { name: 'admin.dashboard' } : { name: 'client.dashboard' }));
    const headerAccountLabel = computed(() => (isAdmin.value ? 'Panel admin' : 'Mi cuenta'));
    const mobileAccountLabel = computed(() => (isAdmin.value ? 'Panel admin' : 'Mi cuenta'));
    const shouldBeTransparent = computed(() => {
        // Header transparente solo en servicios, independientemente del dark mode global
        return route.name === 'services';
    });

    const toggleDarkMode = () => {
        isDarkMode.value = !isDarkMode.value;
        // Agregar/quitar clase dark-mode al body
        if (isDarkMode.value) {
            document.body.classList.add('dark-mode');
            // Guardar preferencia en localStorage
            localStorage.setItem('darkMode', 'true');
        } else {
            document.body.classList.remove('dark-mode');
            // Guardar preferencia en localStorage
            localStorage.setItem('darkMode', 'false');
        }
    };

    const openAuthModal = (form = 'login') => {
        auth.clearErrors();
        openAuthModalGlobal(form);
        closeMobileMenu();
    };

    const handleAuthSuccess = () => {
        closeAuthModal();
        // Refrescar la página para actualizar el estado del frontend
        window.location.reload();
    };

    // Detectar query params para abrir el modal automáticamente
    watch(() => route.query.auth, (authQuery) => {
        if (authQuery === 'login' || authQuery === 'register') {
            openAuthModal(authQuery);
            // Limpiar el query param después de abrir el modal
            if (window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.delete('auth');
                window.history.replaceState({}, '', url);
            }
        }
    }, { immediate: true });

    const handleLogout = async () => {
        const success = await auth.logout();
        if (success) {
            closeMobileMenu();
            // Refrescar la página para actualizar el estado del frontend
            window.location.reload();
        }
    };

    // Cargar preferencia del usuario al montar el componente
    onMounted(() => {
        const savedDarkMode = localStorage.getItem('darkMode');
        if (savedDarkMode === 'true') {
            isDarkMode.value = true;
            document.body.classList.add('dark-mode');
        }

        // Header Sticky - Detectar scroll
        const headerSticky = document.querySelector('.qodef-header-sticky');
        const headerNormal = document.querySelector('#qodef-page-header');
        let lastScrollTop = 0;

        const handleScroll = () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const headerHeight = headerNormal?.offsetHeight || 96; // Altura del header normal

            // El sticky aparece cuando el header normal ya no es visible (scroll > altura del header)
            // El sticky se oculta cuando el header normal vuelve a estar en foco (scroll < altura del header)
            if (scrollTop > headerHeight) {
                // Header normal ya no es visible - mostrar sticky
                headerSticky?.classList.add('qodef--sticky');
            } else {
                // Header normal está en foco - ocultar sticky
                headerSticky?.classList.remove('qodef--sticky');
            }

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        };

        window.addEventListener('scroll', handleScroll, { passive: true });

        // Limpiar listener al desmontar
        onBeforeUnmount(() => {
            window.removeEventListener('scroll', handleScroll);
        });
    });

    const toggleSideArea = () => {
        isSideAreaOpen.value = !isSideAreaOpen.value;
    };

    const closeSideArea = () => {
        isSideAreaOpen.value = false;
    };

    const toggleMobileMenu = () => {
        isMobileMenuOpen.value = !isMobileMenuOpen.value;
        // Bloquear scroll cuando el menú está abierto
        if (isMobileMenuOpen.value) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    };

    const closeMobileMenu = () => {
        isMobileMenuOpen.value = false;
        document.body.style.overflow = '';
    };

    const toggleSubmenu = (event) => {
        const parent = event.currentTarget.closest('.qodef-has-submenu');
        if (!parent) {
            return;
        }
        parent.classList.toggle('qodef--opened');
    };
</script>

<style scoped>

    /* Header Base */
    #qodef-page-header {
        position: relative;
        display: inline-block;
        width: 100%;
        vertical-align: top;
        height: var(--qode-header-height);
        background-color: var(--qode-background-color);
        z-index: 100;
        box-sizing: border-box;
    }

    .qodef-header--standard-no-logo #qodef-page-header {
        background-color: rgba(0, 0, 0, 0);
    }

    /* Header transparente solo en servicios (dark mode) - Máxima especificidad */
    header#qodef-page-header.qodef-header--standard-no-logo.qodef-header--transparent,
    header#qodef-page-header.qodef-header--transparent.qodef-header--standard-no-logo,
    .qodef-header--standard-no-logo.qodef-header--transparent #qodef-page-header,
    .qodef-header--transparent.qodef-header--standard-no-logo #qodef-page-header {
        background-color: transparent !important;
        background: transparent !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        z-index: 1000 !important;
    }

    .qodef-header--standard-no-logo.qodef-header--transparent #qodef-page-header-inner,
    .qodef-header--transparent.qodef-header--standard-no-logo #qodef-page-header-inner {
        background-color: transparent !important;
        border-bottom: 1px solid rgb(255, 255, 255) !important;
    }

    /* Texto blanco en menú cuando header es transparente - Solo header normal (#qodef-page-header-inner), no sticky */
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .qodef-header-navigation>ul>li>a>.qodef-menu-item-text,
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner :deep(.qodef-header-navigation>ul>li>a>.qodef-menu-item-text) {
        color: #ffffff !important;
    }

    /* Background del :before para hover en header transparente - debe ser negro */
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .qodef-header-navigation>ul>li>a>.qodef-menu-item-text:before,
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner :deep(.qodef-header-navigation>ul>li>a>.qodef-menu-item-text:before),
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner :deep(.qodef-menu-item-link .qodef-menu-item-text:before) {
        background-color: #171717 !important;
    }

    /* Hover de nav links cuando header es transparente - fondo negro, letra blanca (sin cambiar color) - Solo header normal */
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .qodef-header-navigation>ul>li>a:hover>.qodef-menu-item-text:before,
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner :deep(.qodef-header-navigation>ul>li>a:hover>.qodef-menu-item-text:before),
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner :deep(.qodef-menu-item-link:hover .qodef-menu-item-text:before),
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner :deep(.qodef-header-navigation>ul>li>a:hover .qodef-menu-item-text:before) {
        background-color: #171717 !important;
        transform: scaleX(1) !important;
    }

    /* Estado activo en header transparente - fondo negro, letra blanca (similar al hover pero permanente) */
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .qodef-header-navigation>ul>li>a.router-link-active>.qodef-menu-item-text,
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner :deep(.qodef-header-navigation>ul>li>a.router-link-active>.qodef-menu-item-text) {
        color: #ffffff !important;
    }

    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .qodef-header-navigation>ul>li>a.router-link-active>.qodef-menu-item-text:before,
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner :deep(.qodef-header-navigation>ul>li>a.router-link-active>.qodef-menu-item-text:before) {
        background-color: #171717 !important;
        transform: scaleX(1);
    }

    /* Botones estilo dark mode cuando header es transparente - Solo header normal (#qodef-page-header-inner) */
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .auth-nav-button {
        border-color: rgba(255, 255, 255, 0.3) !important;
        color: #ffffff !important;
    }

    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .auth-nav-button--ghost:hover {
        background-color: #ffffff !important;
        color: #171717 !important;
        border-color: #ffffff !important;
    }

    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .auth-nav-button--primary {
        border-color: #ffffff !important;
        background-color: #DD3333 !important;
        color: #ffffff !important;
    }

    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .auth-nav-button--primary:hover {
        background-color: #c42b2b !important;
        border-color: #c42b2b !important;
    }

    /* Mobile menu opener - blanco cuando header es transparente - Solo header normal (#qodef-page-header-inner) */
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .qodef-mobile-menu-opener {
        color: #ffffff !important;
    }

    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .qodef-mobile-menu-opener svg {
        color: #ffffff !important;
    }

    /* Side area opener - blanco cuando header es transparente - Solo header normal (#qodef-page-header-inner) */
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .qodef-side-area-opener {
        color: #ffffff !important;
    }

    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .qodef-side-area-opener svg {
        color: #ffffff !important;
        stroke: #ffffff !important;
    }

    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .qodef-side-area-opener svg ellipse,
    #qodef-page-header.qodef-header--transparent #qodef-page-header-inner .qodef-side-area-opener svg path {
        stroke: #ffffff !important;
    }

    #qodef-page-header-inner {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        padding: 0 40px;
        border-bottom: 0 solid var(--qode-heading-color);
        box-sizing: border-box;
    }

    .qodef-header-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        height: 100%;
        box-sizing: border-box;
    }

    /* Widget Holder */
    .qodef-widget-holder {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .qodef-widget-holder.qodef--one {
        order: 3;
    }

    .qodef-widget-holder.qodef--two {
        order: 1;
    }

    /* Navigation */
    .qodef-header-navigation {
        height: 100%;
        order: 2;
        flex: 1;
        display: flex;
        justify-content: space-between;
    }

    .qodef-header-navigation ul {
        margin: 0 !important;
        padding: 0;
        list-style: none;
    }

    .qodef-header-navigation>ul {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        width: 100%;
        white-space: nowrap;
    }

    .qodef-header-navigation>ul>li,
    :deep(.qodef-header-navigation>ul>li) {
        position: relative;
        height: 100%;
        margin: 8px;
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .qodef-header-navigation>ul>li:first-child,
    :deep(.qodef-header-navigation>ul>li:first-child) {
        margin-left: 0;
    }

    .qodef-header-navigation>ul>li:last-child,
    :deep(.qodef-header-navigation>ul>li:last-child) {
        margin-right: 0;
    }

    .qodef-header-navigation>ul>li>a,
    :deep(.qodef-header-navigation>ul>li>a) {
        position: relative;
        display: flex;
        align-items: center;
        height: 100%;
        font-family: "IBM Plex Mono", sans-serif;
        font-size: 16px;
        font-weight: 500;
        line-height: 1.5625em;
        text-transform: uppercase;
        color: var(--qode-text-color);
        text-decoration: none;
    }

    .qodef-header-navigation>ul>li.qodef-menu-item--narrow>a,
    :deep(.qodef-header-navigation>ul>li.qodef-menu-item--narrow>a) {
        cursor: pointer;
    }

    .qodef-header-navigation>ul>li>a>.qodef-menu-item-text,
    :deep(.qodef-header-navigation>ul>li>a>.qodef-menu-item-text) {
        position: relative;
        transition: color 0.15s ease-out;
        padding: 0 5px 1px;
    }

    .qodef-header-navigation>ul>li>a>.qodef-menu-item-text:before,
    :deep(.qodef-header-navigation>ul>li>a>.qodef-menu-item-text:before) {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: var(--qode-heading-color);
        transition: transform 0.42s cubic-bezier(0.33, 0.81, 0.11, 0.96);
        transform: scaleX(0);
        transform-origin: left;
        z-index: -1;
    }

    .qodef-header-navigation>ul>li>a:hover>.qodef-menu-item-text,
    :deep(.qodef-header-navigation>ul>li>a:hover>.qodef-menu-item-text) {
        color: var(--qode-background-color);
    }

    .qodef-header-navigation>ul>li>a:hover>.qodef-menu-item-text:before,
    :deep(.qodef-header-navigation>ul>li>a:hover>.qodef-menu-item-text:before) {
        transform: scaleX(1);
    }

    /* Estado activo - fondo negro, letra blanca (similar al hover pero permanente) */
    .qodef-header-navigation>ul>li>a.router-link-active>.qodef-menu-item-text,
    :deep(.qodef-header-navigation>ul>li>a.router-link-active>.qodef-menu-item-text) {
        color: var(--qode-background-color);
    }

    .qodef-header-navigation>ul>li>a.router-link-active>.qodef-menu-item-text:before,
    :deep(.qodef-header-navigation>ul>li>a.router-link-active>.qodef-menu-item-text:before) {
        transform: scaleX(1);
    }

    /* Menu Arrow */
    .qodef-header-navigation ul li a .qodef-menu-item-arrow {
        position: absolute;
        top: calc(50% - 3px);
        right: 0;
        width: 6px;
        height: auto;
        display: inline-block;
        margin-left: 5px;
    }

    .qodef-svg--menu-arrow {
        fill: currentColor;
        width: 6.05px;
        height: 9.272px;
    }

    /* Submenu - Estilos legacy para compatibilidad */
    .qodef-header-navigation ul li.qodef-menu-item--narrow ul {
        position: absolute;
        top: 100%;
        left: 30px;
        width: 268px;
        margin: 0;
        padding: 14px 0;
        background-color: #DD3333;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease-out, visibility 0.3s ease-out;
        border: 0 solid var(--qode-border-color);
        list-style: none;
        z-index: 1000;
    }

    .qodef-header-navigation ul li.qodef-menu-item--narrow ul li {
        padding: 0 34px;
        margin: 0;
    }

    .qodef-header-navigation ul li.qodef-menu-item--narrow:hover>ul {
        opacity: 1;
        visibility: visible;
        transition: opacity 0.3s ease-out, visibility 0.3s ease-out;
    }

    .qodef-header-navigation>ul>li.qodef-menu-item--narrow ul li>a {
        color: #ffffff;
        font-family: "Inter", sans-serif;
        font-size: 17px;
        font-weight: 400;
        line-height: 1.52941em;
        text-transform: initial;
        text-decoration: none;
    }

    .qodef-header-navigation>ul>li.qodef-menu-item--narrow ul:not(.qodef-drop-down--right) li>a {
        position: relative;
        display: flex;
        white-space: normal;
        margin: 10px 0;
        overflow: hidden;
        transition: padding 0.2s ease-out;
        padding-right: 15px;
    }

    .qodef-header-navigation>ul>li.qodef-menu-item--narrow ul:not(.qodef-drop-down--right) li>a:before {
        content: '';
        position: absolute;
        top: calc(50% - 3px);
        height: 6px;
        width: 6px;
        background: #ffffff;
        transition: left 0.2s ease-out, right 0.2s ease-out;
        left: -15px;
    }

    .qodef-header-navigation>ul>li.qodef-menu-item--narrow ul:not(.qodef-drop-down--right) li:hover>a {
        padding-left: 15px;
        padding-right: 0;
    }

    .qodef-header-navigation>ul>li.qodef-menu-item--narrow ul:not(.qodef-drop-down--right) li:hover>a:before {
        left: 0;
    }

    /* Dropdown Second - Estilos para el dropdown reutilizable (usando :deep para componentes hijos) */
    :deep(.qodef-drop-down-second) {
        position: absolute;
        top: 100%;
        left: 30px;
        width: 268px;
        overflow: hidden;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease-out, visibility 0.3s ease-out;
        z-index: 1000;
    }

    :deep(.qodef-header-navigation>ul>li.qodef-menu-item--narrow:hover .qodef-drop-down-second) {
        opacity: 1;
        visibility: visible;
        transition: opacity 0.3s ease-out, visibility 0.3s ease-out;
    }

    :deep(.qodef-drop-down-second-inner) {
        padding: 14px 0;
        background-color: #DD3333;
    }

    :deep(.qodef-drop-down-second .sub-menu) {
        position: relative;
        background-color: #DD3333;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    :deep(.qodef-drop-down-second .sub-menu li) {
        padding: 0 34px;
        margin: 0;
    }

    :deep(.qodef-drop-down-second .sub-menu li a) {
        position: relative;
        display: flex;
        white-space: normal;
        margin: 10px 0;
        overflow: hidden;
        transition: padding 0.2s ease-out;
        padding-right: 15px;
        color: #ffffff;
        font-family: "Inter", sans-serif;
        font-size: 17px;
        font-weight: 400;
        line-height: 1.52941em;
        text-transform: initial;
        text-decoration: none;
    }

    :deep(.qodef-drop-down-second .sub-menu li a:before) {
        content: '';
        position: absolute;
        top: calc(50% - 3px);
        height: 6px;
        width: 6px;
        background: #ffffff;
        transition: left 0.2s ease-out;
        left: -15px;
    }

    :deep(.qodef-drop-down-second .sub-menu li:hover a) {
        padding-left: 15px;
        padding-right: 0;
    }

    :deep(.qodef-drop-down-second .sub-menu li:hover a:before) {
        left: 0;
    }

    /* Side Area Opener */
    .qodef-side-area-opener {
        display: flex;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }

    .qodef-opener-icon {
        display: inline-block;
        text-decoration: none;
        color: var(--qode-text-color);
        transition: transform 0.3s ease;
    }

    .qodef-opener-icon svg {
        display: block;
        width: 41.477px;
        height: 41.477px;
        fill: none;
        stroke: currentColor;
        transition: transform 0.3s ease;
    }

    .qodef-opener-icon:hover svg {
        transform: rotate(90deg);
    }

    /* Círculo del SVG */
    .qodef-opener-icon svg ellipse {
        fill: none;
        stroke: currentColor;
        stroke-width: 1.5;
    }

    /* Líneas de la cruz (paths) - mismo grosor que el círculo */
    .qodef-opener-icon svg path {
        stroke: currentColor;
        stroke-width: 2;
        fill: none;
        stroke-linecap: round;
    }

    .qodef-m-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 41.477px;
        height: 41.477px;
    }

    /* Switch (Dark/Light Mode) - Toggle tipo pill */
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

    /* Estilos base - ocultar outline por defecto */
    .qodef-path-outline {
        display: none;
    }

    .qodef-path-fill {
        fill: currentColor;
        stroke: none;
    }

    /* Cuando está checked (dark mode activo) - sol activo (blanco en dark, negro en light), luna outline */
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

    /* Cuando está unchecked (light mode activo) - luna activo (blanco en dark, negro en light), sol outline */
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


    /* Header Sticky */
    .qodef-header-sticky {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 99;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-100%);
        transition: transform 0.3s ease-out, opacity 0.3s ease-out, visibility 0.3s ease-out;
        height: var(--qode-header-height);
        background-color: #ffffff;
        border-bottom: 1px solid #000000;
        box-sizing: border-box;
        width: 100%;
        max-width: 100vw;
    }

    body.dark-mode .qodef-header-sticky {
        background-color: #171717;
        border-bottom: 1px solid #ffffff;
    }

    /* Sticky header siempre mantiene estilos normales independientemente del header transparente */
    .qodef-header-sticky--normal {
        background-color: #ffffff;
        border-bottom: 1px solid #000000;
    }

    body.dark-mode .qodef-header-sticky--normal {
        background-color: #171717;
        border-bottom: 1px solid #ffffff;
    }

    .qodef-header-sticky--normal.qodef-appearance--down {
        background-color: #ffffff;
        border-bottom: 1px solid #000000;
    }

    body.dark-mode .qodef-header-sticky--normal.qodef-appearance--down {
        background-color: #171717;
        border-bottom: 1px solid #ffffff;
    }

    .qodef-header-sticky.qodef-appearance--down {
        background-color: #ffffff;
        border-bottom: 1px solid #000000;
    }

    body.dark-mode .qodef-header-sticky.qodef-appearance--down {
        background-color: #171717;
        border-bottom: 1px solid #ffffff;
    }

    .qodef-header-sticky.qodef-appearance--down.qodef--sticky {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .qodef-header-sticky-inner {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        padding: 0 40px;
        box-sizing: border-box;
    }

    .qodef-header-sticky .qodef-header-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        height: 100%;
    }

    /* Sticky header mantiene estilos normales usando clase específica */
    .qodef-header-sticky--normal .qodef-header-navigation>ul>li>a>.qodef-menu-item-text,
    .qodef-header-sticky--normal :deep(.qodef-header-navigation>ul>li>a>.qodef-menu-item-text) {
        color: var(--qode-text-color);
    }

    .qodef-header-sticky--normal .qodef-header-navigation>ul>li>a:hover>.qodef-menu-item-text,
    .qodef-header-sticky--normal :deep(.qodef-header-navigation>ul>li>a:hover>.qodef-menu-item-text) {
        color: var(--qode-background-color);
    }

    .qodef-header-sticky--normal .qodef-header-navigation>ul>li>a:hover>.qodef-menu-item-text:before,
    .qodef-header-sticky--normal :deep(.qodef-header-navigation>ul>li>a:hover>.qodef-menu-item-text:before) {
        transform: scaleX(1);
    }

    .qodef-header-sticky--normal .qodef-header-navigation>ul>li>a>.qodef-menu-item-text:before,
    .qodef-header-sticky--normal :deep(.qodef-header-navigation>ul>li>a>.qodef-menu-item-text:before) {
        background-color: var(--qode-heading-color);
    }

    /* Estado activo - fondo negro, letra blanca (similar al hover pero permanente) */
    .qodef-header-sticky--normal .qodef-header-navigation>ul>li>a.router-link-active>.qodef-menu-item-text,
    .qodef-header-sticky--normal :deep(.qodef-header-navigation>ul>li>a.router-link-active>.qodef-menu-item-text) {
        color: var(--qode-background-color);
    }

    .qodef-header-sticky--normal .qodef-header-navigation>ul>li>a.router-link-active>.qodef-menu-item-text:before,
    .qodef-header-sticky--normal :deep(.qodef-header-navigation>ul>li>a.router-link-active>.qodef-menu-item-text:before) {
        transform: scaleX(1);
    }

    .qodef-header-sticky--normal .auth-nav-button {
        border-color: var(--qode-border-color);
        color: var(--qode-text-color);
    }

    body.dark-mode .qodef-header-sticky--normal .auth-nav-button {
        border-color: var(--qode-border-color);
        color: var(--qode-text-color);
    }

    .qodef-header-sticky--normal .auth-nav-button--ghost:hover {
        background-color: var(--qode-heading-color);
        color: var(--qode-background-color);
    }

    body.dark-mode .qodef-header-sticky--normal .auth-nav-button--ghost:hover {
        background-color: #ffffff;
        color: #171717;
    }

    .qodef-header-sticky--normal .auth-nav-button--primary {
        background-color: #DD3333;
        border-color: #DD3333;
        color: #ffffff;
    }

    .qodef-header-sticky--normal .auth-nav-button--primary:hover {
        background-color: #c42b2b;
        border-color: #c42b2b;
    }

    body.dark-mode .qodef-header-sticky--normal .auth-nav-button--primary {
        border-color: #ffffff;
    }

    .qodef-header-sticky--normal .qodef-mobile-menu-opener {
        color: var(--qode-text-color);
    }

    .qodef-header-sticky--normal .qodef-mobile-menu-opener svg {
        color: var(--qode-text-color);
    }

    /* Mobile Menu Opener */
    .qodef-mobile-menu-opener {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        color: var(--qode-text-color);
        transition: transform 0.3s ease;
    }

    .qodef-mobile-menu-opener:hover {
        transform: rotate(90deg);
    }

    .qodef-mobile-menu-opener .qodef-m-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 41.477px;
        height: 41.477px;
    }

    .qodef-mobile-menu-opener svg {
        display: block;
        width: 41.477px;
        height: 41.477px;
        fill: none;
        stroke: currentColor;
        transition: transform 0.3s ease;
    }

    .qodef-mobile-menu-opener svg ellipse {
        fill: none;
        stroke: currentColor;
        stroke-width: 1.5;
    }

    .qodef-mobile-menu-opener svg path {
        stroke: currentColor;
        stroke-width: 2;
        fill: none;
        stroke-linecap: round;
    }

    .qodef-desktop-only {
        display: flex;
    }

    /* Mobile Menu */
    .qodef-mobile-menu {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #DD3333;
        z-index: 1001;
        transform: translateX(-100%);
        transition: transform 0.3s ease-out;
        overflow-y: auto;
        padding: 80px 20px 40px;
    }

    .qodef-mobile-menu.qodef--opened {
        transform: translateX(0);
    }

    /* Close Button inside mobile menu */
    .qodef-mobile-menu-close {
        position: fixed;
        top: 20px;
        right: 20px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        width: 41.477px;
        height: 41.477px;
        color: #ffffff;
        z-index: 1002;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease;
    }

    .qodef-mobile-menu-close:hover {
        transform: rotate(90deg);
    }

    .qodef-mobile-menu-close svg {
        display: block;
        width: 100%;
        height: 100%;
        fill: none;
        stroke: currentColor;
    }

    .qodef-mobile-menu-navigation {
        margin-bottom: 40px;
    }

    .qodef-mobile-menu-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .qodef-mobile-menu-list>li {
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .qodef-mobile-menu-list>li:last-child {
        border-bottom: none;
    }

    .qodef-mobile-menu-list>li>a {
        display: block;
        padding: 20px 0;
        font-family: "IBM Plex Mono", sans-serif;
        font-size: 18px;
        font-weight: 500;
        text-transform: uppercase;
        color: #ffffff;
        text-decoration: none;
    }

    .qodef-mobile-submenu-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .qodef-mobile-submenu-header>a {
        flex: 1;
        display: block;
        padding: 20px 0;
        font-family: "IBM Plex Mono", sans-serif;
        font-size: 18px;
        font-weight: 500;
        text-transform: uppercase;
        color: #ffffff;
        text-decoration: none;
    }

    .qodef-submenu-toggle {
        display: inline-flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        background: transparent;
        color: #ffffff;
        cursor: pointer;
        transition: border-color 0.3s ease, background-color 0.3s ease;
    }

    .qodef-submenu-toggle span {
        display: block;
        width: 16px;
        height: 1.5px;
        background-color: currentColor;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .qodef-submenu-toggle span:last-child {
        margin-top: 6px;
    }

    .qodef-submenu-toggle:hover {
        border-color: #ffffff;
        background-color: rgba(255, 255, 255, 0.12);
    }

    .qodef-mobile-menu-list>li.qodef-has-submenu>a {
        position: relative;
    }

    .qodef-mobile-menu-list>li.qodef-has-submenu>a:after {
        content: '+';
        position: absolute;
        right: 0;
        font-size: 24px;
        transition: transform 0.3s ease;
    }

    .qodef-has-submenu.qodef--opened .qodef-submenu-toggle span:first-child {
        transform: translateY(3px) rotate(45deg);
    }

    .qodef-has-submenu.qodef--opened .qodef-submenu-toggle span:last-child {
        transform: translateY(-3px) rotate(-45deg);
    }

    .qodef-has-submenu.qodef--opened .qodef-submenu-toggle:hover {
        background-color: rgba(255, 255, 255, 0.25);
    }

    .qodef-mobile-menu-list>li.qodef-has-submenu.qodef--opened>a:after {
        transform: rotate(45deg);
    }

    .qodef-submenu {
        list-style: none;
        margin: 0;
        padding: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }

    .qodef-has-submenu.qodef--opened .qodef-submenu {
        max-height: 300px;
    }

    .qodef-submenu li a {
        display: block;
        padding: 12px 0 12px 20px;
        font-family: "Inter", sans-serif;
        font-size: 16px;
        color: #ffffff;
        text-decoration: none;
        text-transform: none;
    }

    /* Mobile Side Area Content */
    .qodef-mobile-side-area {
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        padding-top: 40px;
        text-align: center;
    }

    .qodef-mobile-logo {
        margin-bottom: 30px;
    }

    .qodef-mobile-logo img {
        max-width: 250px;
        height: auto;
    }

    .qodef-mobile-separator {
        width: 100%;
        height: 1px;
        background-color: #3b44cd;
        margin: 0 auto 30px;
        max-width: 300px;
        border: none;
    }

    .qodef-mobile-title {
        font-family: 'Lexend', sans-serif;
        font-size: 32px;
        font-weight: 700;
        text-transform: uppercase;
        color: #ffffff;
        margin: 0 0 20px 0;
    }

    .qodef-mobile-company {
        font-family: 'Inter', sans-serif;
        font-size: 16px;
        font-weight: 400;
        color: #ffffff;
        text-transform: uppercase;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .qodef-mobile-email a {
        font-family: 'Inter', sans-serif;
        font-size: 16px;
        font-weight: 400;
        color: #ffffff;
        text-decoration: none;
    }

    /* Auth Nav Items */
    .auth-nav-item {
        flex: 0 !important;
        margin-left: 18px;
        display: flex;
        align-items: center;
    }

    .auth-nav-group {
        display: flex;
        gap: 12px;
        margin-right: 8px;
    }

    .auth-nav-button {
        font-family: "IBM Plex Mono", sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 13px;
        padding: 10px 18px;
        border-radius: 999px;
        border: 1px solid var(--qode-text-color);
        background: transparent;
        color: var(--qode-text-color);
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .auth-nav-button--primary {
        background-color: #DD3333;
        border-color: #DD3333;
        color: #ffffff;
    }

    .auth-nav-button--primary:hover {
        background-color: #c42b2b;
        border-color: #c42b2b;
    }

    .auth-nav-button--ghost:hover {
        background-color: var(--qode-heading-color);
        color: var(--qode-background-color);
    }

    body.dark-mode .auth-nav-button {
        border-color: var(--qode-border-color);
        color: var(--qode-text-color);
    }

    body.dark-mode .auth-nav-button--ghost:hover {
        background-color: #ffffff;
        color: #171717;
    }

    body.dark-mode .auth-nav-button--primary {
        border-color: #ffffff;
    }

    /* Mobile Auth Items */
    .mobile-auth-item {
        border-bottom: none;
        margin-top: 20px;
    }

    .mobile-auth-button,
    .mobile-auth-link {
        width: 100%;
        display: block;
        text-align: center;
        font-family: "IBM Plex Mono", sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 15px;
        padding: 16px 0;
        border-radius: 999px;
        border: 1px solid #ffffff;
        color: #ffffff;
        background: transparent;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .mobile-auth-button--primary,
    .mobile-auth-link--primary {
        background-color: #ffffff;
        color: #DD3333;
    }

    .mobile-auth-button:hover,
    .mobile-auth-link:hover {
        background-color: rgba(255, 255, 255, 0.15);
    }

    .mobile-auth-button--primary:hover,
    .mobile-auth-link--primary:hover {
        background-color: #ffffff;
        color: #c42b2b;
    }

    /* Responsive */
    @media (max-width: 1024px) {

        /* Ocultar navegación desktop */
        .qodef-header-navigation {
            display: none;
        }

        /* Mostrar burger menu */
        .qodef-mobile-menu-opener {
            display: block;
        }

        /* Ocultar side area opener en móvil */
        .qodef-desktop-only {
            display: none;
        }
    }

    @media (max-width: 1024px) {

        #qodef-page-header-inner,
        .qodef-header-sticky-inner {
            padding: 0 30px;
        }
    }

    @media (max-width: 680px) {

        #qodef-page-header-inner,
        .qodef-header-sticky-inner {
            padding: 0 20px;
        }
    }
</style>
