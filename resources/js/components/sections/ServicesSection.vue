<template>
    <div class="portfolio-tagline">
        <div class="tagline-inner">
            <span class="tagline-icon"></span>
            <span class="tagline-text">links</span>
        </div>
    </div>
    <section class="services-section">
        <div class="services-container">
            <!-- Título -->
            <div class="portfolio-header">
                <h1 class="header-title">Servicios<br>Populares</h1>
            </div>

            <!-- Awards List (Services List) -->
            <div class="qodef-awards-list qodef-grid qodef-layout--columns qodef-gutter--no qodef-layout--text-below">
                <div class="qodef-grid-inner clear">
                    <article class="qodef-e qodef-grid-item" v-for="(service, index) in services" :key="service.id">
                        <div class="qodef-e-inner">
                            <div class="qodef-e-title-wrapper">
                                <h5 class="qodef-e-title">
                                    <a itemprop="url" :href="service.link" target="_self">{{ service.title }}</a>
                                </h5>
                            </div>
                            <div class="qodef-e-text">
                                <span>{{ service.description }}</span>
                            </div>
                            <div class="qodef-e-image" :data-distort-id="`distort-${index}`">
                                <a itemprop="url" :href="service.link" target="_self">
                                    <img loading="lazy" decoding="async" :src="service.image" :alt="service.title"
                                        class="attachment-full size-full" :data-filter-id="`filter-distort-${index}`" />
                                </a>
                                <svg class="qodef-svg--distort" width="100" height="100" viewBox="0 0 100 100"
                                    :data-strength="25">
                                    <defs>
                                        <filter :id="`filter-distort-${index}`" x="-0%" y="-0%" width="100%"
                                            height="100%" filterUnits="objectBoundingBox"
                                            primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                            <feTurbulence type="fractalNoise" baseFrequency="0.01 0.02" numOctaves="3"
                                                seed="2" result="turbulencebase"></feTurbulence>
                                            <feColorMatrix in="turbulencebase" type="hueRotate" values="0"
                                                result="turbulence"></feColorMatrix>
                                            <feDisplacementMap in="SourceGraphic" in2="turbulence" scale="0"
                                                xChannelSelector="R" yChannelSelector="B" result="displacement1">
                                            </feDisplacementMap>
                                            <feMerge result="merge">
                                                <feMergeNode in="SourceGraphic"></feMergeNode>
                                                <feMergeNode in="displacement1"></feMergeNode>
                                            </feMerge>
                                        </filter>
                                    </defs>
                                </svg>
                            </div>
                            <div class="qodef-e-button">
                                <a class="qodef-shortcode qodef-m qodef-button qodef-layout--filled-rounded qodef-html--link"
                                    :href="service.link" target="_self">
                                    <span class="qodef-m-icon">
                                        <svg class="qodef-svg--simple-arrow" xmlns="http://www.w3.org/2000/svg"
                                            width="14.634" height="14.554">
                                            <path d="M1.414 14.553 0 13.139 12.847.292l1.414 1.414Z"></path>
                                            <path d="M14.634 10.429h-2V2H4.009V0h10.625Z"></path>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { ref, onMounted, onBeforeUnmount } from 'vue';

    const animationFrames = ref({});
    const isAnimating = ref({});

    const startDistortion = (index) => {
        if (isAnimating.value[index]) return;

        const filter = document.querySelector(`#filter-distort-${index}`);
        if (!filter) return;

        const displacementMap = filter.querySelector('feDisplacementMap');
        const colorMatrix = filter.querySelector('feColorMatrix');
        if (!displacementMap || !colorMatrix) return;

        isAnimating.value[index] = true;
        const strength = 25;
        const duration = 600;
        const startTime = Date.now();

        const img = document.querySelector(`[data-filter-id="filter-distort-${index}"]`);
        if (img) {
            img.style.filter = `url(#filter-distort-${index})`;
        }

        const animate = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);

            let scale;
            let hueRotate;

            if (progress < 0.25) {
                const phaseProgress = progress / 0.25;
                scale = strength * phaseProgress;
                hueRotate = 0;
            } else if (progress < 0.75) {
                scale = strength;
                const phaseProgress = (progress - 0.25) / 0.5;
                hueRotate = 360 * phaseProgress;
            } else {
                const phaseProgress = (progress - 0.75) / 0.25;
                scale = strength * (1 - phaseProgress);
                hueRotate = 360;
            }

            displacementMap.setAttribute('scale', scale);
            colorMatrix.setAttribute('values', hueRotate);

            if (progress < 1) {
                animationFrames.value[index] = requestAnimationFrame(animate);
            } else {
                if (img) {
                    img.style.filter = 'none';
                }
                displacementMap.setAttribute('scale', 0);
                colorMatrix.setAttribute('values', 0);
                isAnimating.value[index] = false;
            }
        };

        animationFrames.value[index] = requestAnimationFrame(animate);
    };

    const stopDistortion = (index) => {
        if (animationFrames.value[index]) {
            cancelAnimationFrame(animationFrames.value[index]);
            animationFrames.value[index] = null;
        }

        const filter = document.querySelector(`#filter-distort-${index}`);
        if (filter) {
            const displacementMap = filter.querySelector('feDisplacementMap');
            const colorMatrix = filter.querySelector('feColorMatrix');
            if (displacementMap) {
                displacementMap.setAttribute('scale', 0);
            }
            if (colorMatrix) {
                colorMatrix.setAttribute('values', 0);
            }
        }

        const img = document.querySelector(`[data-filter-id="filter-distort-${index}"]`);
        if (img) {
            img.style.filter = 'none';
        }

        isAnimating.value[index] = false;
    };

    onMounted(() => {
        // Calcular la posición de la primera imagen para que todas usen la misma posición
        const firstImage = document.querySelector(`[data-distort-id="distort-0"]`);
        let baseTop = 35; // Valor por defecto

        if (firstImage) {
            const firstArticle = firstImage.closest('.qodef-grid-item');
            if (firstArticle) {
                const rect = firstArticle.getBoundingClientRect();
                const gridInner = firstArticle.closest('.qodef-grid-inner');
                if (gridInner) {
                    const gridRect = gridInner.getBoundingClientRect();
                    baseTop = rect.top - gridRect.top + (rect.height / 2);
                }
            }
        }

        // Aplicar la misma posición a todas las imágenes
        services.value.forEach((service, index) => {
            const item = document.querySelector(`[data-distort-id="distort-${index}"]`);
            if (item) {
                item.style.top = `${baseTop}px`;
                item.style.transform = 'translateY(-50%)';

                const article = item.closest('.qodef-e');
                if (article) {
                    article.addEventListener('mouseenter', () => startDistortion(index));
                    article.addEventListener('mouseleave', () => stopDistortion(index));
                }
            }
        });
    });

    onBeforeUnmount(() => {
        Object.keys(animationFrames.value).forEach(index => {
            if (animationFrames.value[index]) {
                cancelAnimationFrame(animationFrames.value[index]);
            }
        });
    });

    const services = ref([
        {
            id: 1,
            title: 'Flyers',
            image: '/images/IPHONES_MAQUETA-removebg-preview.png',
            description: 'Diseño único',
            link: '/plantillas'
        },
        {
            id: 2,
            title: 'Animación',
            image: '/images/11318709-removebg-preview.png',
            description: 'Atractivo inmediato',
            link: '/plantillas'
        },
        {
            id: 3,
            title: 'Gestión RRSS',
            image: '/images/IPHONES_MAQUETA-removebg-preview.png',
            description: 'Calidad Creativa',
            link: '/plantillas'
        },
    ]);
</script>

<style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@400&display=swap');

    .services-section {
        position: relative;
        background: transparent;
        padding-top: 150px;
        padding-bottom: 150px;
        padding-left: 6%;
        padding-right: 6%;
    }

    .services-container {
        width: min(1620px, 100%);
    }

    .portfolio-tagline {
        border-top: 1px solid var(--qode-border-color);
        border-right: 0;
        border-bottom: 0;
        border-left: 0;
        padding-top: 20px;
    }

    .tagline-inner {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 14px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--qode-text-color);
        padding: 0px 0px 0px 55px;
    }

    .tagline-icon {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: var(--qode-text-color);
        display: inline-block;
    }

    /* Header - igual que PortfolioSection */
    .portfolio-header {
        position: relative;
        margin-bottom: 85px;
    }

    .header-title {
        font-family: 'Lexend', sans-serif;
        font-size: clamp(48px, 6vw, 56px);
        font-weight: 400;
        color: var(--qode-text-color);
        margin: 0;
        line-height: 1.07143em;
        text-transform: uppercase;
    }

    /* Awards List */
    .qodef-awards-list {
        position: relative;
    }

    .qodef-awards-list .qodef-grid-inner {
        position: relative;
        counter-reset: item;
        gap: 0;
        display: grid;
        grid-template-columns: 1fr;
    }

    .qodef-awards-list article.qodef-grid-item {
        border-top: 1px solid var(--qode-border-color);
        position: static;
        counter-increment: item;
    }

    .qodef-awards-list article.qodef-grid-item:last-of-type {
        border-bottom: 1px solid var(--qode-border-color);
    }

    .qodef-awards-list article.qodef-grid-item .qodef-e-inner {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        min-height: 70px;
        padding: 20px 0;
        gap: 20px;
    }

    .qodef-awards-list .qodef-e-title-wrapper {
        flex: 0 0 33%;
        max-width: 33%;
        margin: 0;
        position: relative;
        display: flex;
        align-items: flex-start;
        padding-top: 0;
    }

    .qodef-awards-list .qodef-e-title-wrapper:before {
        display: inline-block;
        font-size: 23px;
        line-height: 1.73913;
        font-weight: 500;
        content: counter(item);
        margin-right: 46px;
        min-width: 24px;
        color: var(--qode-text-color);
        flex-shrink: 0;
        margin-top: 0;
        vertical-align: top;
    }

    .qodef-awards-list .qodef-e-title-wrapper .qodef-e-title {
        margin: 0;
        padding: 0;
        position: relative;
        z-index: 1;
        display: inline-block;
        font-family: 'Space Mono', monospace;
        font-size: 23px;
        font-weight: 400;
        color: var(--qode-text-color);
        line-height: 1.73913;
        flex-shrink: 0;
        vertical-align: top;
    }

    .qodef-awards-list .qodef-e-title-wrapper .qodef-e-title a {
        color: var(--qode-text-color);
        text-decoration: none;
    }

    .qodef-awards-list .qodef-e-image {
        position: absolute;
        left: calc(33% + 80px + 50px);
        top: 35px;
        transform: translateY(-50%);
        pointer-events: none;
        z-index: 10;
        opacity: 0;
        transition: opacity 0.1s ease-out;
        width: 600px;
        max-width: 700px;
        height: auto;
    }

    .qodef-awards-list .qodef-e-image img {
        width: 100%;
        height: auto;
        display: block;
        transform-origin: center;
    }

    .qodef-awards-list .qodef-e:hover .qodef-e-image {
        opacity: 1;
    }

    .qodef-awards-list .qodef-svg--distort {
        position: absolute;
        width: 0;
        height: 0;
        overflow: hidden;
    }

    .qodef-awards-list .qodef-e-text {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 14px;
        line-height: 1.21429em;
        font-weight: 500;
        text-transform: uppercase;
        flex: 1 1 auto;
        margin: 0;
        padding: 0;
        text-align: left;
        color: var(--qode-text-color);
        align-self: flex-start;
    }

    .qodef-awards-list .qodef-e-text span {
        display: inline-block;
        vertical-align: top;
    }

    .qodef-awards-list .qodef-e-button {
        flex-shrink: 0;
        width: 35px;
        height: 35px;
        margin: 0;
        align-self: flex-start;
    }

    .qodef-awards-list .qodef-e-button .qodef-button {
        position: relative;
        display: inline-block;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background-color: var(--qode-text-color);
        padding: 0;
        text-align: center;
    }

    .qodef-awards-list .qodef-e-button .qodef-button:before {
        content: '';
        display: block;
        padding-bottom: 100%;
    }

    .qodef-awards-list .qodef-e-button .qodef-m-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qodef-awards-list .qodef-e-button .qodef-svg--simple-arrow {
        width: 14.634px;
        height: 14.554px;
        stroke: #171717 !important;
        fill: #171717 !important;
        stroke-width: 1.2;
        transition: transform 0.37s cubic-bezier(0.44, 0.73, 0.35, 0.97);
        transform-origin: center;
    }

    .qodef-awards-list .qodef-e-button .qodef-svg--simple-arrow path {
        stroke: #171717 !important;
        fill: #171717 !important;
        stroke-width: 1.2;
    }

    .qodef-awards-list .qodef-e:hover .qodef-svg--simple-arrow {
        transform: rotate(45deg);
    }

    /* Responsive */
    @media only screen and (max-width: 1200px) {
        .qodef-awards-list .qodef-e-title-wrapper {
            flex-basis: 100%;
        }

        .qodef-awards-list .qodef-e-title-wrapper .qodef-e-image {
            right: 5%;
        }

        .qodef-awards-list .qodef-e-text {
            flex-basis: 100%;
        }

        .qodef-awards-list .qodef-e-button {
            margin: 18px 0 30px;
        }
    }

    @media only screen and (max-width: 880px) {
        .services-section {
            padding-left: 4%;
            padding-right: 4%;
            padding-top: 80px;
            padding-bottom: 80px;
        }

        .qodef-awards-list .qodef-grid-item {
            width: 100% !important;
            position: relative !important;
        }

        .qodef-awards-list .qodef-e-inner {
            flex-direction: column;
        }

        .qodef-awards-list .qodef-e-title-wrapper:before {
            margin-right: 0;
        }

        .qodef-awards-list .qodef-e-title-wrapper .qodef-e-image {
            right: 0;
            max-width: 40%;
            position: relative;
            top: auto;
            transform: none;
            margin-top: 14px;
        }

        .qodef-awards-list .qodef-e-text {
            margin: 14px 0 0;
            text-align: left;
        }
    }

    @media (max-width: 680px) {
        .services-section {
            padding-left: 20px;
            padding-right: 20px;
            padding-top: 60px;
            padding-bottom: 60px;
        }

        .header-title {
            font-size: clamp(32px, 8vw, 42px);
        }
    }
</style>
