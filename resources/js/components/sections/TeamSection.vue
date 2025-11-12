<template>
    <div class="portfolio-tagline">
        <div class="tagline-inner">
            <span class="tagline-icon"></span>
            <span class="tagline-text">Essential team</span>
        </div>
    </div>
    <section class="team-section">
        <!-- Portfolio Masonry -->
        <div class="portfolio-masonry">
            <div class="masonry-item" v-for="item in teamItems" :key="item.id" :class="item.class">
                <div class="masonry-image">
                    <a itemprop="url" :href="item.link">
                        <img :src="item.image" :alt="item.title" />
                    </a>
                    <!-- Overlay con contenido -->
                    <div class="qodef-e-hover-overlay">
                        <div class="qodef-e-hover-content">
                            <div class="qodef-e-hover-info-box">
                                <div class="qodef-e-hover-info">
                                    <div class="qodef-e-hover-border-line line-top"></div>
                                    <div class="qodef-e-hover-info-inner">
                                        <span>{{ item.category }}</span>
                                    </div>
                                    <div class="qodef-e-hover-border-line line-bottom"></div>
                                </div>
                                <div class="qodef-e-hover-separator-line"></div>
                                <div class="qodef-e-hover-title-box">
                                    <div class="qodef-e-hover-border-line line-top"></div>
                                    <h5 class="qodef-e-hover-title">
                                        <a itemprop="url" class="qodef-e-title-link" :href="item.link">{{ item.title
                                        }}</a>
                                    </h5>
                                    <div class="qodef-e-hover-border-line line-bottom"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a itemprop="url" class="qodef-e-post-link" :href="item.link"></a>
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

    .portfolio-tagline {
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

    /* Hover Overlay */
    .qodef-e-hover-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 50%;
        height: 100%;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding-left: 40px;
    }

    .qodef-e-post-link {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
        pointer-events: auto;
    }

    .qodef-e-hover-overlay a {
        position: relative;
        z-index: 4;
        pointer-events: auto;
    }

    .masonry-item:hover .qodef-e-hover-overlay {
        opacity: 1;
        visibility: visible;
    }

    .qodef-e-hover-content {
        position: relative;
        width: 100%;
    }

    .qodef-e-hover-info-box {
        position: relative;
    }

    .qodef-e-hover-border-line {
        position: absolute;
        left: 0;
        width: 0;
        height: 1px;
        background-color: #fff !important;
    }

    .qodef-e-hover-border-line.line-top {
        top: 0;
        transform-origin: left;
    }

    .qodef-e-hover-border-line.line-bottom {
        bottom: 0;
        transform-origin: left;
    }

    .masonry-item:hover .qodef-e-hover-info .qodef-e-hover-border-line.line-top {
        animation: drawLineLeftToRight 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .masonry-item:hover .qodef-e-hover-info .qodef-e-hover-border-line.line-bottom {
        animation: drawLineLeftToRight 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.1s forwards;
    }

    .masonry-item:hover .qodef-e-hover-title-box .qodef-e-hover-border-line.line-top {
        animation: drawLineLeftToRight 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.3s forwards;
    }

    .masonry-item:hover .qodef-e-hover-title-box .qodef-e-hover-border-line.line-bottom {
        animation: drawLineLeftToRight 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.4s forwards;
    }

    @keyframes drawLineLeftToRight {
        from {
            width: 0;
        }

        to {
            width: 100%;
        }
    }

    .qodef-e-hover-separator-line {
        width: 0;
        height: 1px;
        background-color: #fff !important;
        margin: 10px 0;
        transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.2s;
    }

    .masonry-item:hover .qodef-e-hover-separator-line {
        width: 100%;
    }

    .qodef-e-hover-info,
    .qodef-e-hover-title-box {
        position: relative;
        background-color: #171717;
        border-left: 1px solid #fff;
        border-right: 1px solid #fff;
        padding: 12px 20px;
        margin: 10px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateX(-20px);
        transition: opacity 0.4s ease 0.2s, transform 0.4s ease 0.2s, visibility 0.4s ease 0.2s, border-left 0s 0.6s, border-right 0s 0.6s;
        z-index: 4;
        color: #ffffff;
    }

    .masonry-item:hover .qodef-e-hover-info,
    .masonry-item:hover .qodef-e-hover-title-box {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
        border-left: 1px solid #fff;
        border-right: 1px solid #fff;
        transition: opacity 0.4s ease 0.2s, transform 0.4s ease 0.2s, visibility 0.4s ease 0.2s, border-left 0s, border-right 0s;
    }

    .qodef-e-hover-title a {
        position: relative;
        z-index: 5;
    }

    .masonry-item:not(:hover) .qodef-e-hover-border-line {
        width: 0;
    }

    .masonry-item:not(:hover) .qodef-e-hover-separator-line {
        width: 0;
    }

    .qodef-e-hover-info {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 14px;
        font-weight: 500;
        text-transform: uppercase;
        color: #fff !important;
        letter-spacing: 0.05em;
    }

    .qodef-e-hover-info-inner {
        position: relative;
        z-index: 1;
        color: #fff !important;
    }

    .qodef-e-hover-title-box {
        margin-top: 15px;
    }

    .qodef-e-hover-title {
        margin: 0;
        font-family: 'Space Mono', monospace;
        font-size: 18px;
        font-weight: 700;
        color: inherit;
    }

    .qodef-e-hover-title a {
        color: inherit;
        text-decoration: none;
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

        .qodef-e-hover-overlay {
            display: none !important;
        }

        .qodef-item-layout--info-follow.qodef-hover-animation--follow .qodef-e-content {
            display: block;
            margin-top: 16px;
        }

        .qodef-e-info {
            display: inline-flex;
            gap: 8px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(23, 23, 23, 0.76);
        }

        .qodef-e-info a {
            color: rgba(23, 23, 23, 0.76);
            text-decoration: none;
        }

        .qodef-e-title {
            margin: 10px 0 0;
            font-family: 'Space Mono', monospace;
            font-size: 20px;
            text-transform: uppercase;
            color: #171717;
        }

        .qodef-e-title a {
            color: inherit;
            text-decoration: none;
        }

        .team-card-info {
            display: flex !important;
            flex-direction: column;
            gap: 6px;
            margin-top: 14px;
            padding: 0 4px 14px;
        }

        .team-card-info__category {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.75);
        }

        .team-card-info__title {
            margin: 0;
            font-family: 'Space Mono', monospace;
            font-size: 20px;
            text-transform: uppercase;
            color: #ffffff;
        }

        .team-card-info__title a {
            color: inherit;
            text-decoration: none;
        }

        .qodef-e-post-link {
            display: none;
        }

        body.dark-mode .qodef-e-info,
        body.dark-mode .qodef-e-info a {
            color: rgba(243, 243, 243, 0.75);
        }

        body.dark-mode .qodef-e-title,
        body.dark-mode .qodef-e-title a {
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
