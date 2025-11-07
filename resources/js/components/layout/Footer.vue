<template>
    <footer id="qodef-page-footer" class="qodef-custom-cursor--activate" role="contentinfo">
        <!-- Footer Top Area -->
        <div id="qodef-page-footer-top-area">
            <div id="qodef-page-footer-top-area-inner" class="qodef-content-grid">
                <div
                    class="qodef-grid qodef-layout--columns qodef-responsive--custom qodef-col-num--4 qodef-col-num--1200--2 qodef-col-num--1024--2 qodef-col-num--880--2 qodef-col-num--680--1 qodef-layout--predefined">
                    <div class="qodef-grid-inner">
                        <!-- Column 1: PLANTILLAS -->
                        <div class="qodef-grid-item">
                            <div class="widget widget_block">
                                <h6 class="wp-block-heading">PLANTILLAS</h6>
                            </div>
                            <div class="widget widget_block">
                                <ul class="wp-block-list">
                                    <li><a href="/plantillas">Flyers</a></li>
                                    <li><a href="/plantillas">Novedades</a></li>
                                    <li><a href="/plantillas">Populares</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Column 2: INFORMACIÓN -->
                        <div class="qodef-grid-item">
                            <div class="widget widget_block">
                                <h6 class="wp-block-heading">INFORMACIÓN</h6>
                            </div>
                            <div class="widget widget_block">
                                <ul class="wp-block-list">
                                    <li><a href="/precios">Precios</a></li>
                                    <li><a href="/servicios">Servicios</a></li>
                                    <li><a href="/contacto">Contactar</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Column 3: LEGAL -->
                        <div class="qodef-grid-item">
                            <div class="widget widget_block">
                                <h6 class="wp-block-heading">LEGAL</h6>
                            </div>
                            <div class="widget widget_block">
                                <ul class="wp-block-list">
                                    <li><a href="/cookies">Cookies</a></li>
                                    <li><a href="/politica_privacidad">Política de Privacidad</a></li>
                                    <li><a href="/terminosdeuso">Términos de uso</a></li>
                                    <li><a href="/copright">Copyright</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Column 4: REDES Y CONTACTO -->
                        <div class="qodef-grid-item">
                            <div class="widget widget_block">
                                <h6 class="wp-block-heading">REDES Y CONTACTO</h6>
                            </div>
                            <div class="widget widget_block">
                                <ul class="wp-block-list">
                                    <li><a href="mailto:infoessential@innovation.com">Correo</a></li>
                                    <li><a href="https://www.instagram.com/essential.innovation/?hl=es" target="_blank"
                                            rel="nofollow noopener">Instagram</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Area -->
        <div id="qodef-page-footer-bottom-area">
            <div id="qodef-page-footer-bottom-area-inner" class="qodef-content-full-width">
                <div class="qodef-footer-bottom-content">
                    <div class="qodef-custom-text-holder">
                        <p>© 2025 <a href="/" target="_blank" rel="nofollow noopener">ESSENTIAL INNOVATION</a>, ALL
                            RIGHTS RESERVED</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
    // Footer component
</script>

<style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@400;500&display=swap');

    /* Footer Base */
    #qodef-page-footer {
        position: relative;
        display: inline-block;
        width: 100%;
        vertical-align: top;
    }

    #qodef-page-footer>* {
        position: relative;
        display: inline-block;
        width: 100%;
        vertical-align: top;
    }

    /* Footer Widgets */
    #qodef-page-footer .widget {
        margin-bottom: 0;
    }

    #qodef-page-footer .widget .qodef-widget-title,
    #qodef-page-footer .widget h6.wp-block-heading {
        font-family: 'Lexend', sans-serif;
        font-size: 20px;
        font-weight: 500;
        line-height: 1.5625em;
        text-transform: uppercase;
        color: var(--qode-text-color);
        display: block;
        margin: 0 0 20px 0;
    }

    #qodef-page-footer .widget .qodef-custom-text-holder a,
    #qodef-page-footer .widget .qodef-custom-text-holder p {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 14px;
        line-height: 1.21429em;
        font-weight: 500;
        text-transform: uppercase;
        margin: 0;
    }

    /* Footer Bottom también usa IBM Plex Mono */
    #qodef-page-footer-bottom-area .qodef-custom-text-holder a,
    #qodef-page-footer-bottom-area .qodef-custom-text-holder p {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 14px;
    }

    #qodef-page-footer .widget .qodef-custom-text-holder a {
        text-decoration: none;
    }

    #qodef-page-footer .widget .qodef-custom-text-holder a:hover {
        text-decoration: underline;
    }

    /* Footer Lists */
    #qodef-page-footer .wp-block-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    #qodef-page-footer .wp-block-list li {
        margin: 0 0 10px 0;
    }

    #qodef-page-footer .wp-block-list li:last-child {
        margin-bottom: 0;
    }

    #qodef-page-footer .wp-block-list a {
        font-family: 'Inter', sans-serif;
        font-size: 17px;
        font-weight: 400;
        line-height: 1.52941em;
        color: var(--qode-text-color);
        text-decoration: none;
        cursor: pointer;
    }

    /* Footer Top Area */
    #qodef-page-footer-top-area {
        background-color: #171717 !important;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        border-top: 0 solid var(--qode-heading-color);
    }

    #qodef-page-footer-top-area-inner {
        padding-top: 136px;
        padding-bottom: 200px;
    }

    #qodef-page-footer-top-area-inner .qodef-grid {
        --qode-columns-row-gap: 50px;
    }

    /* Footer Top Area - Text Colors (Dark Background) */
    #qodef-page-footer-top-area .widget h6.wp-block-heading,
    #qodef-page-footer-top-area .wp-block-list a,
    #qodef-page-footer-top-area .qodef-custom-text-holder p,
    #qodef-page-footer-top-area .qodef-custom-text-holder a {
        color: #ffffff;
    }

    /* Footer Bottom Area */
    #qodef-page-footer-bottom-area {
        background-color: #171717 !important;
        border-top: 0 solid var(--qode-heading-color);
    }

    #qodef-page-footer-bottom-area-inner {
        padding-top: 36px;
        padding-bottom: 36px;
    }

    /* Footer Bottom Area - Text Colors (Dark Background) */
    #qodef-page-footer-bottom-area .qodef-footer-bottom-content {
        text-align: right;
        width: 100%;
    }

    #qodef-page-footer-bottom-area .qodef-custom-text-holder p,
    #qodef-page-footer-bottom-area .qodef-custom-text-holder a {
        color: #ffffff !important;
    }

    #qodef-page-footer-bottom-area .qodef-custom-text-holder {
        text-align: right;
        display: inline-block;
    }

    #qodef-page-footer-bottom-area .qodef-custom-text-holder a:hover {
        text-decoration: underline;
    }

    /* Grid Layout */
    .qodef-grid {
        display: grid;
        gap: var(--qode-columns-row-gap, 50px);
    }

    .qodef-grid-inner {
        display: contents;
    }

    .qodef-grid-item {
        display: block;
    }

    /* Responsive Grid - Footer Top (4 columns) */
    .qodef-col-num--4 {
        grid-template-columns: repeat(4, 1fr);
    }

    @media (max-width: 1200px) {
        .qodef-col-num--1200--2 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 1024px) {
        .qodef-col-num--1024--2 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 880px) {
        .qodef-col-num--880--2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .qodef-col-num--880--1 {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 680px) {
        .qodef-col-num--680--1 {
            grid-template-columns: 1fr;
        }
    }

    /* Responsive Grid - Footer Bottom (2 columns) */
    .qodef-col-num--2 {
        grid-template-columns: repeat(2, 1fr);
    }

    .qodef-alignment--right .qodef-grid-inner {
        justify-items: end;
    }

    .qodef-alignment--right .qodef-grid-item:last-child {
        text-align: right;
        justify-self: end;
    }

    @media (min-width: 1201px) {
        #qodef-page-footer-top-area-inner .qodef-grid.qodef-layout--predefined>.qodef-grid-inner {
            grid-template-columns: minmax(0, 4fr) minmax(0, 4fr) minmax(0, 4fr) minmax(0, 6fr);
        }
    }


    /* Content Grid */
    .qodef-content-grid {
        max-width: var(--qode-content-max-width, 1400px);
        margin: 0 auto;
        padding: 0 40px;
    }

    .qodef-content-full-width {
        width: 100%;
        padding: 0 40px;
    }

    @media (max-width: 1200px) {

        .qodef-content-grid,
        .qodef-content-full-width {
            padding: 0 30px;
        }
    }

    @media (max-width: 680px) {

        .qodef-content-grid,
        .qodef-content-full-width {
            padding: 0 20px;
        }
    }
</style>
