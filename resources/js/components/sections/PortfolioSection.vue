<template>
    <SectionTagline text="portfolio" />
    <SectionTitle tag="h2" :padded="true">Tu Agencia de Diseño<br>Creativo</SectionTitle>
    <section class="portfolio-section" id="portfolio">
        <div class="portfolio-container essential-section-container">

            <div class="portfolio-header essential-section-header">
                <a href="/contacto" class="header-button" @mouseenter="shuffleText" @mouseleave="resetText">
                    <span class="button-inner">
                        <span class="button-text" ref="buttonTextRef">{{ displayText }}</span>
                        <span class="button-icon" aria-hidden="true">
                            <svg class="rounded-arrow" xmlns="http://www.w3.org/2000/svg" width="15.427" height="15.427"
                                viewBox="0 0 15.427 15.427">
                                <path d="m1.65619982 13.77101763 7.96131525-7.96131525"></path>
                                <path d="M9.61822218 14.42721272V5.80899527H1.00000473"></path>
                            </svg>
                        </span>
                    </span>
                </a>
            </div>
        </div>

        <!-- Grid full width -->
        <div
            class="portfolio-grid essential-portfolio-list essential-item-layout--info-follow essential-grid essential-layout--columns essential-gutter--no essential-vertical-gutter--custom essential-col-num--2 essential-hover-animation--follow">
            <div class="essential-grid-inner">
                <article
                    class="essential-e essential-grid-item essential-item--full essential-custom-margin portfolio-item"
                    v-for="item in portfolioItems" :key="item.id">
                    <div class="essential-e-inner" :style="item.style">
                        <div class="essential-e-media">
                            <div class="essential-e-media-image">
                                <a itemprop="url" :href="item.link">
                                    <img :src="item.image" :alt="item.title" loading="lazy" />
                                </a>
                            </div>
                            <!-- Overlay con contenido -->
                            <HoverOverlay>
                                <template #category>
                                    <template v-for="(category, index) in item.categories" :key="category">
                                        <a :href="'/portfolio-category/' + category.toLowerCase()" rel="tag">
                                            {{ category }}
                                        </a>
                                        <span v-if="index !== item.categories.length - 1"
                                            class="essential-info-separator-single">, </span>
                                    </template>
                                </template>
                                <template #title>
                                    <a itemprop="url" class="essential-e-title-link" :href="item.link">
                                        {{ item.title }}
                                    </a>
                                </template>
                            </HoverOverlay>
                        </div>
                        <div class="essential-e-content">
                            <div class="essential-e-top-holder">
                                <div class="essential-e-info">
                                    <template v-for="(category, index) in item.categories" :key="category">
                                        <a :href="'/portfolio-category/' + category.toLowerCase()" rel="tag">{{
                                            category }}</a>
                                        <span v-if="index !== item.categories.length - 1"
                                            class="essential-info-separator-single"></span>
                                    </template>
                                    <div class="essential-info-separator-end"></div>
                                </div>
                            </div>
                            <div class="essential-e-text">
                                <h5 itemprop="name" class="essential-e-title entry-title">
                                    <a itemprop="url" class="essential-e-title-link" :href="item.link">{{ item.title
                                        }}</a>
                                </h5>
                            </div>
                        </div>
                        <a itemprop="url" class="essential-e-post-link" :href="item.link"></a>
                    </div>
                </article>
            </div>
        </div>

        <!-- Full Width Image -->
        <div class="portfolio-full-width-image">
            <img loading="lazy" decoding="async" src="/images/sala-oficina-2048x1467.jpg" alt="Oficina" />
        </div>
    </section>
</template>

<script setup>
    import { ref } from 'vue';
    import SectionTagline from '../home/ui/SectionTagline.vue';
    import SectionTitle from '../home/ui/SectionTitle.vue';
    import HoverOverlay from '../home/ui/HoverOverlay.vue';
    import { useShuffleText } from '@/composables/home/useShuffleText';

    const { displayText, buttonTextRef, shuffleText, resetText } = useShuffleText('Escrí_benos');

    const portfolioItems = ref([
        {
            id: 1,
            title: 'Gráficos Creativos',
            image: '/images/11318709-scaled.jpg',
            categories: ['Creative', 'Graphic'],
            link: '/plantillas',
            style: 'margin: 0% 26% 0% 0%;'
        },
        {
            id: 2,
            title: 'Diseño Conceptual',
            image: '/images/IPHONES-MAQUETA-scaled.jpg',
            categories: ['Creative', 'Graphic'],
            link: '/plantillas',
            style: 'margin: 11.5% 21% 0% -7%;'
        },
        {
            id: 3,
            title: 'Essentials Desings',
            image: '/images/storie-iphone-piedra.jpg',
            categories: ['Creative', 'Graphic'],
            link: '/plantillas',
            style: 'margin: 30% -7% 0% 21%;'
        },
        {
            id: 4,
            title: 'Design',
            image: '/images/galeria-scaled.jpg',
            categories: ['Creative', 'Graphic'],
            link: '/plantillas',
            style: 'margin: -6% 0% 0% 27%;'
        },
    ]);
</script>

<style scoped>

    .portfolio-section {
        position: relative;
        background: transparent;
        padding-bottom: 60px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .portfolio-container {
        padding: 0 clamp(20px, 5vw, 60px);
    }

    @media (max-width: 680px) {
        .portfolio-container {
            padding: 0 20px;
        }
    }


    .header-button {
        position: absolute;
        top: -150px;
        right: clamp(40px, 8vw, 0px);
        display: inline-block;
        width: 207px;
        max-width: 207px;
        border-radius: 50%;
        background-color: var(--essential-text-color);
        color: var(--essential-background-color);
        text-decoration: none;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 16px;
        font-weight: 500;
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        text-align: center;
        padding: 0;
        border: 1px solid transparent;
        z-index: 10;
        box-sizing: border-box;
        flex-shrink: 0;
    }

    @media (min-width: 1620px) {
        .header-button {
            right: calc((64vw - 1620px) / 2 + -34px);
        }
    }

    .header-button:before {
        content: '';
        display: block;
        padding-bottom: 100%;
    }

    .button-inner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        white-space: nowrap;
    }

    .header-button:hover .rounded-arrow {
        transform: rotate(45deg);
    }

    .rounded-arrow {
        transition: transform 0.37s cubic-bezier(0.44, 0.73, 0.35, 0.97);
        transform-origin: 50% 50%;
        stroke-width: 1.8;
        height: 16px;
        width: auto;
    }

    .button-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .rounded-arrow path {
        stroke: var(--essential-background-color);
        fill: none;
    }

    /* Portfolio Grid - Solo estilos específicos */
    .portfolio-grid {
        --essential-columns: 2;
        --essential-columns-row-gap: 0px;
        --essential-columns-col-gap: 0px;
    }

    /* Full Width Image */
    .portfolio-full-width-image {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 150px 0 0 70px;
        display: block;
        box-sizing: border-box;
    }

    .portfolio-full-width-image img {
        width: 100%;
        max-width: 100%;
        height: auto;
        display: block;
        box-sizing: border-box;
    }

    @media (max-width: 1180px) {
        .header-button {
            position: relative;
            margin-top: 35px;
            margin-left: auto;
            margin-right: auto;
            display: block;
            right: auto;
            top: auto;
        }

        .portfolio-full-width-image {
            padding: 100px 0 0 50px;
        }
    }

    @media (max-width: 1024px) {
        .portfolio-header {
            display: flex;
            justify-content: center;
        }

        .header-button {
            top: auto;
            left: auto;
            right: auto;
            margin: 40px auto 0;
        }

    }

    @media (max-width: 1180px) {
        .portfolio-full-width-image {
            padding: 80px 0 0 40px;
        }
    }

    @media (max-width: 880px) {
        .portfolio-grid.essential-grid>.essential-grid-inner {
            --essential-columns: 1;
        }

        .portfolio-full-width-image {
            padding: 60px 0 0 30px;
        }
    }

    @media (max-width: 680px) {
        .portfolio-container {
            padding: 0 20px;
        }

        .portfolio-section {
            padding-top: 100px;
            padding-bottom: 40px;
        }


        .portfolio-grid.essential-grid>.essential-grid-inner {
            padding: 0;
        }

        .essential-custom-margin .essential-e-media-image {
            width: 100% !important;
            min-width: 100% !important;
        }

        .portfolio-full-width-image {
            padding: 40px 0 0 20px;
        }
    }
</style>
