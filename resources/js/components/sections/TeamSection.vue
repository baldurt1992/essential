<template>
    <SectionTagline text="Essential team" />
    <section class="team-section">
        <!-- Portfolio Masonry -->
        <div class="portfolio-masonry">
            <div class="masonry-item" v-for="item in teamItems" :key="item.id" :class="item.class">
                <div class="masonry-image">
                    <a itemprop="url" :href="item.link">
                        <img :src="item.image" :alt="item.title" />
                    </a>
                    <!-- Overlay con contenido -->
                    <div class="essential-e-hover-overlay">
                        <div class="essential-e-hover-content">
                            <div class="essential-e-hover-info-box">
                                <div class="essential-e-hover-info">
                                    <div class="essential-e-hover-border-line line-top"></div>
                                    <div class="essential-e-hover-info-inner">
                                        <span>{{ item.category }}</span>
                                    </div>
                                    <div class="essential-e-hover-border-line line-bottom"></div>
                                </div>
                                <div class="essential-e-hover-separator-line"></div>
                                <div class="essential-e-hover-title-box">
                                    <div class="essential-e-hover-border-line line-top"></div>
                                    <h5 class="essential-e-hover-title">
                                        <a itemprop="url" class="essential-e-title-link" :href="item.link">{{ item.title
                                            }}</a>
                                    </h5>
                                    <div class="essential-e-hover-border-line line-bottom"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a itemprop="url" class="essential-e-post-link" :href="item.link"></a>
                </div>

                <div class="team-card-info">
                    <span class="team-card-info__category">{{ item.category }}</span>
                    <h5 class="team-card-info__title">
                        <RouterLink :to="item.link">{{ item.title }}</RouterLink>
                    </h5>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { ref } from 'vue';
    import { RouterLink } from 'vue-router';
    import SectionTagline from '../home/ui/SectionTagline.vue';

    const teamItems = ref([
        {
            id: 1,
            title: 'Fresh Design',
            image: '/images/POST-MOVIE-650x650.jpg',
            category: 'Visual Concept',
            link: '/plantillas',
            class: 'masonry-square'
        },
        {
            id: 2,
            title: 'INNOVATION',
            image: '/images/mac-alex-2-1300x650.jpg',
            category: 'Visual Concept',
            link: '/plantillas',
            class: 'masonry-landscape'
        },
        {
            id: 3,
            title: 'BRANDING',
            image: '/images/flyers-feria-1-650x650.jpg',
            category: 'Visual Concept',
            link: '/plantillas',
            class: 'masonry-square'
        },
        {
            id: 4,
            title: 'Art Creations',
            image: '/images/post-03-650x650.jpg',
            category: 'Visual Concept',
            link: '/plantillas',
            class: 'masonry-square'
        },
        {
            id: 5,
            title: 'Blue Project',
            image: '/images/POST-JUANKI-650x650.jpg',
            category: 'Visual Concept',
            link: '/plantillas',
            class: 'masonry-square'
        },
        {
            id: 6,
            title: 'Imagination',
            image: '/images/mac-alex-3-1300x650.jpg',
            category: 'Visual Concept',
            link: '/plantillas',
            class: 'masonry-landscape'
        },
    ]);
</script>

<style scoped>


    .portfolio-masonry {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
        grid-auto-rows: 476px;
    }

    .masonry-item {
        position: relative;
        overflow: hidden;
    }

    .masonry-square {
        grid-row: span 1;
    }

    .masonry-landscape {
        grid-column: span 2;
    }

    .masonry-image {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .masonry-image>a {
        display: block;
        width: 100%;
        height: 100%;
    }

    .masonry-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Hover Overlay - Estilos específicos solo para masonry */
    .masonry-item .essential-e-post-link {
        pointer-events: auto;
    }

    .team-card-info {
        display: none;
    }

    @media (max-width: 1024px) {
        .portfolio-masonry {
            grid-template-columns: 1fr;
            grid-auto-rows: auto;
            gap: 0;
        }

        .masonry-item {
            min-height: 0;
            overflow: visible;
            display: flex;
            flex-direction: column;
        }

        .masonry-image {
            position: relative;
            overflow: hidden;
        }

        .masonry-image img {
            width: 100%;
            height: auto;
            aspect-ratio: 4 / 3;
        }

        .masonry-square,
        .masonry-landscape {
            grid-column: span 1;
            grid-row: span 1;
        }

        .essential-e-hover-overlay {
            display: none !important;
        }

        .essential-item-layout--info-follow.essential-hover-animation--follow .essential-e-content {
            display: block;
            margin-top: 16px;
        }

        .essential-e-info {
            display: inline-flex;
            gap: 8px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(23, 23, 23, 0.76);
        }

        .essential-e-info a {
            color: rgba(23, 23, 23, 0.76);
            text-decoration: none;
        }

        .essential-e-title {
            margin: 10px 0 0;
            font-family: 'Space Mono', monospace;
            font-size: 20px;
            text-transform: uppercase;
            color: #171717;
        }

        .essential-e-title a {
            color: inherit;
            text-decoration: none;
        }

        .team-card-info {
            display: flex !important;
            flex-direction: column;
            gap: 6px;
            margin-top: 14px;
            padding: 0 16px 20px;
            position: relative;
            z-index: 1;
            visibility: visible;
            opacity: 1;
        }

        .team-card-info__category {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(23, 23, 23, 0.76);
        }

        .team-card-info__title {
            margin: 0;
            font-family: 'Space Mono', monospace;
            font-size: 20px;
            text-transform: uppercase;
            color: #171717;
        }

        .team-card-info__title a {
            color: inherit;
            text-decoration: none;
        }

        .essential-e-post-link {
            display: none;
        }

        body.dark-mode .essential-e-info,
        body.dark-mode .essential-e-info a {
            color: rgba(243, 243, 243, 0.75);
        }

        body.dark-mode .essential-e-title,
        body.dark-mode .essential-e-title a {
            color: #f3f3f3;
        }

        body.dark-mode .team-card-info__category {
            color: rgba(243, 243, 243, 0.7);
        }

        body.dark-mode .team-card-info__title,
        body.dark-mode .team-card-info__title a {
            color: #f3f3f3;
        }
    }

    @media (max-width: 680px) {
        .portfolio-masonry {
            gap: 0;
        }

        .masonry-image img {
            aspect-ratio: 1 / 1;
        }
    }
</style>
