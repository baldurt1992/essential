<template>
    <div class="services-marquee-container">
        <div
            class="qodef-shortcode qodef-m qodef-text-marquee qodef-text-marquee-services qodef-layout--default qodef-direction--left qodef-separator--icon qodef--init">
            <div class="qodef-m-content">
                <!-- Primera copia -->
                <div class="qodef-m-content-inner">
                    <template v-for="(service, index) in services" :key="`service-${index}`">
                        <span class="qodef-m-text" :class="`qodef--${(index % 5) + 1}`">
                            {{ service.title.toLowerCase() }}
                        </span>
                        <span class="qodef-m-separator">
                            <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33" viewBox="0 0 33 33">
                                <g transform="translate(-8505 1420)">
                                    <g transform="translate(8506 -1419)" fill="#fff" stroke="#171717" stroke-width="1">
                                        <circle cx="15.5" cy="15.5" r="15.5" stroke="none"></circle>
                                        <circle cx="15.5" cy="15.5" r="16" fill="none"></circle>
                                    </g>
                                    <g transform="translate(8513 -1412)" fill="#fff" stroke="#171717" stroke-width="1">
                                        <circle cx="8.5" cy="8.5" r="8.5" stroke="none"></circle>
                                        <circle cx="8.5" cy="8.5" r="9" fill="none"></circle>
                                    </g>
                                    <g transform="translate(8517.008 -1407.224)">
                                        <circle cx="1.28" cy="1.28" r="1.28" transform="translate(0 0)" fill="#171717">
                                        </circle>
                                        <circle cx="1.28" cy="1.28" r="1.28" transform="translate(6.144 0)"
                                            fill="#171717"></circle>
                                        <path d="M9798.811-904.1s-.047,3.248,4.084,3.248,4.1-3.248,4.1-3.248"
                                            transform="translate(-9798.428 909.219)" fill="none" stroke="#171717"
                                            stroke-width="1"></path>
                                    </g>
                                </g>
                            </svg>
                        </span>
                    </template>
                </div>
                <!-- Segunda copia para el loop infinito -->
                <div class="qodef-m-content-inner qodef--copy">
                    <template v-for="(service, index) in services" :key="`service-copy-${index}`">
                        <span class="qodef-m-text" :class="`qodef--${(index % 5) + 1}`">
                            {{ service.title.toLowerCase() }}
                        </span>
                        <span class="qodef-m-separator">
                            <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33" viewBox="0 0 33 33">
                                <g transform="translate(-8505 1420)">
                                    <g transform="translate(8506 -1419)" fill="#fff" stroke="#171717" stroke-width="1">
                                        <circle cx="15.5" cy="15.5" r="15.5" stroke="none"></circle>
                                        <circle cx="15.5" cy="15.5" r="16" fill="none"></circle>
                                    </g>
                                    <g transform="translate(8513 -1412)" fill="#fff" stroke="#171717" stroke-width="1">
                                        <circle cx="8.5" cy="8.5" r="8.5" stroke="none"></circle>
                                        <circle cx="8.5" cy="8.5" r="9" fill="none"></circle>
                                    </g>
                                    <g transform="translate(8517.008 -1407.224)">
                                        <circle cx="1.28" cy="1.28" r="1.28" transform="translate(0 0)" fill="#171717">
                                        </circle>
                                        <circle cx="1.28" cy="1.28" r="1.28" transform="translate(6.144 0)"
                                            fill="#171717"></circle>
                                        <path d="M9798.811-904.1s-.047,3.248,4.084,3.248,4.1-3.248,4.1-3.248"
                                            transform="translate(-9798.428 909.219)" fill="none" stroke="#171717"
                                            stroke-width="1"></path>
                                    </g>
                                </g>
                            </svg>
                        </span>
                    </template>
                </div>
                <!-- Tercera copia para asegurar que siempre haya contenido visible -->
                <div class="qodef-m-content-inner qodef--copy">
                    <template v-for="(service, index) in services" :key="`service-copy2-${index}`">
                        <span class="qodef-m-text" :class="`qodef--${(index % 5) + 1}`">
                            {{ service.title.toLowerCase() }}
                        </span>
                        <span class="qodef-m-separator">
                            <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33" viewBox="0 0 33 33">
                                <g transform="translate(-8505 1420)">
                                    <g transform="translate(8506 -1419)" fill="#fff" stroke="#171717" stroke-width="1">
                                        <circle cx="15.5" cy="15.5" r="15.5" stroke="none"></circle>
                                        <circle cx="15.5" cy="15.5" r="16" fill="none"></circle>
                                    </g>
                                    <g transform="translate(8513 -1412)" fill="#fff" stroke="#171717" stroke-width="1">
                                        <circle cx="8.5" cy="8.5" r="8.5" stroke="none"></circle>
                                        <circle cx="8.5" cy="8.5" r="9" fill="none"></circle>
                                    </g>
                                    <g transform="translate(8517.008 -1407.224)">
                                        <circle cx="1.28" cy="1.28" r="1.28" transform="translate(0 0)" fill="#171717">
                                        </circle>
                                        <circle cx="1.28" cy="1.28" r="1.28" transform="translate(6.144 0)"
                                            fill="#171717"></circle>
                                        <path d="M9798.811-904.1s-.047,3.248,4.084,3.248,4.1-3.248,4.1-3.248"
                                            transform="translate(-9798.428 909.219)" fill="none" stroke="#171717"
                                            stroke-width="1"></path>
                                    </g>
                                </g>
                            </svg>
                        </span>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { computed, onMounted } from 'vue';
    import { useSiteServices } from '../../composables/useSiteServices.js';

    const { services: servicesData, fetchServices } = useSiteServices();

    // Obtener servicios populares
    onMounted(async () => {
        await fetchServices({ popular: true });
    });

    // Computed para obtener los servicios o usar valores por defecto
    const services = computed(() => {
        if (servicesData.value && servicesData.value.length > 0) {
            return servicesData.value;
        }
        // Valores por defecto basados en la imagen
        return [
            { title: 'Motion Design' },
            { title: 'Art Design' },
            { title: 'Graphic Design' },
            { title: 'Illustrations' },
            { title: 'UI UX Design' },
        ];
    });

</script>

<style scoped>
    .services-marquee-container {
        width: 100%;
        overflow: hidden;
        position: relative;
        padding-top: 30px;
        padding-bottom: 185px;
        padding-left: 0;
        padding-right: 0;
        background: var(--qode-background-color);
        z-index: 1;
    }

    .qodef-text-marquee {
        position: relative;
        width: 100%;
        overflow: hidden;
    }

    .qodef-m-content {
        display: flex;
        width: fit-content;
        animation: marquee-left 18s linear infinite;
        will-change: transform;
    }

    .qodef-m-content-inner {
        display: flex;
        align-items: center;
        white-space: nowrap;
        flex-shrink: 0;
        gap: 0;
        margin: 0;
        padding: 0;
    }

    .qodef-m-text {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 13px;
        font-weight: 500;
        text-transform: uppercase;
        color: var(--qode-text-color);
        padding: 9px 30px;
        letter-spacing: 0.05em;
        border: 1px solid currentColor;
        border-radius: 17px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        box-sizing: border-box;
        line-height: 1.2;
        flex-shrink: 0;
    }

    .qodef-m-separator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin: 0 12px;
    }

    .qodef-m-separator svg {
        width: 33px;
        height: 33px;
        display: block;
    }

    /* Animación del marquee - con 3 copias, se mueve exactamente 33.333% para loop infinito perfecto */
    @keyframes marquee-left {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(-33.333%);
        }
    }

    /* Dark mode */
    body.dark-mode .qodef-m-text {
        color: var(--qode-text-color);
    }

    /* SVG del marquee siempre mantiene fondo blanco, no se afecta por dark mode */
    .qodef-m-separator svg {
        background-color: #ffffff;
        border-radius: 50%;
    }
</style>
