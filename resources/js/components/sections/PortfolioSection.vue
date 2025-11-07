<template>
    <div class="portfolio-tagline">
        <div class="tagline-inner">
            <span class="tagline-icon"></span>
            <span class="tagline-text">portfolio</span>
        </div>
    </div>
    <h2 class="header-title">Tu Agencia de Diseño<br>Creativo</h2>
    <section class="portfolio-section" id="portfolio">
        <div class="portfolio-container">

            <div class="portfolio-header">
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
            class="portfolio-grid qodef-portfolio-list qodef-item-layout--info-follow qodef-grid qodef-layout--columns qodef-gutter--no qodef-vertical-gutter--custom qodef-col-num--2 qodef-hover-animation--follow">
            <div class="qodef-grid-inner">
                <article class="qodef-e qodef-grid-item qodef-item--full qodef-custom-margin portfolio-item"
                    v-for="item in portfolioItems" :key="item.id">
                    <div class="qodef-e-inner" :style="item.style">
                        <div class="qodef-e-media">
                            <div class="qodef-e-media-image">
                                <a itemprop="url" :href="item.link">
                                    <img :src="item.image" :alt="item.title" loading="lazy" />
                                </a>
                            </div>
                            <!-- Overlay con contenido -->
                            <div class="qodef-e-hover-overlay">
                                <div class="qodef-e-hover-content">
                                    <div class="qodef-e-hover-info-box">
                                        <div class="qodef-e-hover-info">
                                            <div class="qodef-e-hover-border-line line-top"></div>
                                            <div class="qodef-e-hover-info-inner">
                                                <template v-for="(category, index) in item.categories" :key="category">
                                                    <a :href="'/portfolio-category/' + category.toLowerCase()"
                                                        rel="tag">{{
                                                            category }}</a>
                                                    <span v-if="index !== item.categories.length - 1"
                                                        class="qodef-info-separator-single">, </span>
                                                </template>
                                            </div>
                                            <div class="qodef-e-hover-border-line line-bottom"></div>
                                        </div>
                                        <div class="qodef-e-hover-separator-line"></div>
                                        <div class="qodef-e-hover-title-box">
                                            <div class="qodef-e-hover-border-line line-top"></div>
                                            <h5 itemprop="name" class="qodef-e-hover-title">
                                                <a itemprop="url" class="qodef-e-title-link" :href="item.link">{{
                                                    item.title
                                                    }}</a>
                                            </h5>
                                            <div class="qodef-e-hover-border-line line-bottom"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="qodef-e-content">
                            <div class="qodef-e-top-holder">
                                <div class="qodef-e-info">
                                    <template v-for="(category, index) in item.categories" :key="category">
                                        <a :href="'/portfolio-category/' + category.toLowerCase()" rel="tag">{{
                                            category }}</a>
                                        <span v-if="index !== item.categories.length - 1"
                                            class="qodef-info-separator-single"></span>
                                    </template>
                                    <div class="qodef-info-separator-end"></div>
                                </div>
                            </div>
                            <div class="qodef-e-text">
                                <h5 itemprop="name" class="qodef-e-title entry-title">
                                    <a itemprop="url" class="qodef-e-title-link" :href="item.link">{{ item.title
                                        }}</a>
                                </h5>
                            </div>
                        </div>
                        <a itemprop="url" class="qodef-e-post-link" :href="item.link"></a>
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
    import { ref, onBeforeUnmount } from 'vue';

    const buttonTextRef = ref(null);
    const displayText = ref('Escrí_benos');
    const originalText = 'Escrí_benos';
    const isShuffling = ref(false);
    let animationFrameId = null;

    const getRandomChar = () => {
        const possible = "!\"#$%&'()*+,-./:;<=>?@[\\]^_`{|}~" +
            "0123456789" +
            "ABCDEFGHIJKLMNOPQRSTUVWXYZ" +
            "abcdefghijklmnopqrstuvwxyz";
        return possible.charAt(Math.floor(Math.random() * possible.length));
    };

    const mask = (chars, progress) => {
        const masked = [];
        for (let i = 0; i < chars.length; i++) {
            const position = (i + 1) / chars.length;
            if (position > progress) {
                masked.push(getRandomChar());
            } else {
                masked.push(chars[i]);
            }
        }
        return masked.join('');
    };

    const shuffleText = () => {
        if (isShuffling.value || !buttonTextRef.value) return;

        // Cancelar cualquier animación anterior
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }

        isShuffling.value = true;
        const chars = originalText.trim().split('');
        const duration = 500; // ms
        const delay = 100; // ms delay inicial
        const startTime = Date.now() + delay;

        const animate = () => {
            const elapsed = Math.max(0, Date.now() - startTime);
            const progress = Math.min(elapsed / duration, 1);

            // Easing: easeInQuad (progress^2)
            const easedProgress = progress * progress;

            displayText.value = mask(chars, easedProgress);

            if (progress < 1) {
                animationFrameId = requestAnimationFrame(animate);
            } else {
                displayText.value = originalText;
                isShuffling.value = false;
                animationFrameId = null;
            }
        };

        // Delay inicial de 100ms como en el original
        setTimeout(() => {
            if (isShuffling.value) {
                animationFrameId = requestAnimationFrame(animate);
            }
        }, delay);
    };

    const resetText = () => {
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }
        if (isShuffling.value) {
            displayText.value = originalText;
            isShuffling.value = false;
        }
    };

    onBeforeUnmount(() => {
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }
    });

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
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@400&display=swap');

    .portfolio-section {
        position: relative;
        background: transparent;
        padding-bottom: 60px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .portfolio-container {
        width: min(1620px, 100%);
        max-width: 100%;
        padding: 0 clamp(20px, 5vw, 60px);
        box-sizing: border-box;
    }

    .portfolio-tagline {
        padding-top: 20px;
        padding-bottom: 120px;
        padding-left: 0px;
        padding-right: 0px;
        border-top: 1px solid var(--qode-border-color);
        border-right: 0;
        border-bottom: 0;
        border-left: 0;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
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
        max-width: 100%;
        box-sizing: border-box;
    }

    .tagline-icon {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: var(--qode-text-color);
        display: inline-block;
    }

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
        padding-top: 0%;
        padding-bottom: 0%;
        padding-left: 6%;
        padding-right: 6%;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        word-wrap: break-word;
    }

    .header-button {
        position: absolute;
        top: -150px;
        right: -80px;
        display: inline-block;
        width: 207px;
        max-width: 207px;
        border-radius: 50%;
        background-color: var(--qode-text-color);
        color: var(--qode-background-color);
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
            right: calc((100vw - 1620px) / 2 - 380px);
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
        stroke: var(--qode-background-color);
        fill: none;
    }

    /* Portfolio Grid - Base styles matching qodef-portfolio-list */
    .portfolio-grid {
        position: relative;
        display: inline-block;
        width: 100%;
        max-width: 100%;
        vertical-align: top;
        --qode-columns: 2;
        --qode-columns-row-gap: 0px;
        --qode-columns-col-gap: 0px;
        box-sizing: border-box;
    }

    .portfolio-grid.qodef-grid>.qodef-grid-inner {
        position: relative;
        display: grid;
        grid-template-columns: repeat(var(--qode-columns), minmax(0, 1fr));
        row-gap: var(--qode-columns-row-gap);
        column-gap: var(--qode-columns-col-gap);
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .qodef-e {
        position: relative;
    }

    .qodef-grid-item {
        position: relative;
        display: inline-block;
        width: 100%;
        vertical-align: top;
        min-height: 1px;
    }

    .qodef-e-inner {
        position: relative;
        display: inline-block;
        width: 100%;
        vertical-align: top;
    }

    /* Custom margin items - width auto allows margins to reposition */
    .qodef-portfolio-list.qodef-custom-margin .qodef-e-inner,
    .qodef-custom-margin .qodef-e-inner {
        width: auto;
    }

    .qodef-e-media,
    .qodef-e-content {
        position: relative;
        display: inline-block;
        width: 100%;
        vertical-align: top;
    }

    .qodef-e-media-image {
        position: relative;
        display: inline-block;
        vertical-align: top;
        max-width: 100%;
        width: 100%;
        height: 100%;
    }

    .qodef-e-media-image a,
    .qodef-e-media-image img {
        display: block;
        width: 100%;
        height: auto;
    }

    .qodef-e-media-image img {
        transition: transform 0.5s ease;
    }

    .qodef-e:hover .qodef-e-media-image img {
        transform: scale(1.04);
    }



    .qodef-item-layout--info-follow.qodef-hover-animation--follow .qodef-e-media {
        height: 100%;
        width: 100%;
    }

    .qodef-item-layout--info-follow.qodef-hover-animation--follow .qodef-e-content {
        margin: 0;
        display: none;
        /* Hidden by default, shown via hover overlay */
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

    .qodef-e-inner:hover .qodef-e-hover-overlay {
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

    .qodef-e-inner:hover .qodef-e-hover-info .qodef-e-hover-border-line.line-top {
        animation: drawLineLeftToRight 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .qodef-e-inner:hover .qodef-e-hover-info .qodef-e-hover-border-line.line-bottom {
        animation: drawLineLeftToRight 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.1s forwards;
    }

    .qodef-e-inner:hover .qodef-e-hover-title-box .qodef-e-hover-border-line.line-top {
        animation: drawLineLeftToRight 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.3s forwards;
    }

    .qodef-e-inner:hover .qodef-e-hover-title-box .qodef-e-hover-border-line.line-bottom {
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

    .qodef-e-inner:hover .qodef-e-hover-separator-line {
        width: 100%;
    }

    .qodef-e-hover-info,
    .qodef-e-hover-title-box {
        position: relative;
        background-color: #171717 !important;
        border-left: 1px solid #fff;
        border-right: 1px solid #fff;
        padding: 12px 20px;
        margin: 10px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateX(-20px);
        transition: opacity 0.4s ease 0.2s, transform 0.4s ease 0.2s, visibility 0.4s ease 0.2s, border-left 0s 0.6s, border-right 0s 0.6s;
    }

    .qodef-e-inner:hover .qodef-e-hover-info,
    .qodef-e-inner:hover .qodef-e-hover-title-box {
        opacity: 1;
        visibility: visible;
        transform: translateX(0);
        border-left: 1px solid #fff;
        border-right: 1px solid #fff;
        transition: opacity 0.4s ease 0.2s, transform 0.4s ease 0.2s, visibility 0.4s ease 0.2s, border-left 0s, border-right 0s;
    }

    .qodef-e-inner:not(:hover) .qodef-e-hover-border-line {
        width: 0;
    }

    .qodef-e-inner:not(:hover) .qodef-e-hover-separator-line {
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
    }

    .qodef-e-hover-info a {
        color: #fff !important;
        text-decoration: none;
    }

    .qodef-e-hover-title-box {
        margin-top: 15px;
    }

    .qodef-e-hover-title {
        margin: 0;
        font-family: 'Space Mono', monospace;
        font-size: 18px;
        font-weight: 700;
        text-transform: uppercase;
        color: #fff !important;
        position: relative;
        z-index: 1;
    }

    .qodef-e-hover-title a {
        color: #fff !important;
        text-decoration: none;
    }

    .qodef-e-title {
        margin: 0;
        color: #fff !important;
    }

    .qodef-e-title a {
        color: inherit;
    }

    .qodef-e-info>* {
        color: #fff !important;
    }

    .qodef-e-post-link {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
    }

    .qodef-info-separator-single {
        margin: 0 8px;
    }

    .qodef-info-separator-end {
        display: none;
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

    @media (max-width: 1368px) {
        .header-button {
            position: static;
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

    @media (max-width: 1200px) {
        .tagline-inner {
            padding-left: 40px;
        }

        .header-title {
            padding-left: 5%;
            padding-right: 5%;
        }

        .portfolio-full-width-image {
            padding: 80px 0 0 40px;
        }
    }

    @media (max-width: 880px) {
        .portfolio-grid.qodef-grid>.qodef-grid-inner {
            --qode-columns: 1;
        }

        .tagline-inner {
            padding-left: 30px;
        }

        .header-title {
            font-size: clamp(36px, 8vw, 48px);
            padding-left: 5%;
            padding-right: 5%;
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

        .portfolio-tagline {
            padding-top: 15px;
            padding-bottom: 60px;
        }

        .tagline-inner {
            padding-left: 20px;
            font-size: 12px;
        }

        .header-title {
            font-size: clamp(32px, 10vw, 42px);
            padding-left: 20px;
            padding-right: 20px;
        }

        .portfolio-header {
            margin-bottom: 40px;
        }

        .portfolio-grid.qodef-grid>.qodef-grid-inner {
            padding: 0;
        }

        /* Reset custom margins on mobile */
        .qodef-e-inner {
            width: 100% !important;
            margin: 0 !important;
        }

        .qodef-custom-margin .qodef-e-media-image {
            width: 100% !important;
            min-width: 100% !important;
        }

        .portfolio-full-width-image {
            padding: 40px 0 0 20px;
        }
    }
</style>
