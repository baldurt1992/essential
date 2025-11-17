<template>
    <section class="hero-section" @mousemove="handleMouseMove" @mouseleave="resetOffsets">
        <div class="hero-container" ref="heroContainer">
            <!-- Texto Principal "ESSENTIAL" -->
            <h1 class="hero-title">ESSENTIAL</h1>

            <!-- Imagen Central -->
            <div class="hero-image" :style="heroImageStyle">
                <img src="/images/liquid-removebg-preview.png" alt="Essential" />
            </div>

            <!-- Texto Lateral Izquierdo -->
            <div class="hero-text-left">
                <p>-Diseño Gráfico - Artistas & Djs</p>
                <p>-Motion Graphics - Eventos</p>
                <p>-Community Manager</p>
            </div>

            <!-- Texto Lateral Derecho -->
            <div class="hero-text-right">
                <p class="hero-subtitle">Tu sitio creativo</p>
                <a href="mailto:infoessential.innovation@gmail.com" class="hero-email">
                    infoessential.innovation@gmail.com
                </a>
            </div>

            <!-- Link Instagram -->
            <div class="hero-social">
                <a href="https://www.instagram.com/essential.innovation/?hl=es" target="_blank" rel="nofollow noopener">
                    Instagram
                </a>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { useParallax } from '@/composables/home/useParallax';

    const { containerRef: heroContainer, imageStyle: heroImageStyle, handleMouseMove, resetOffsets } = useParallax({
        maxOffset: 40,
        easing: 0.12,
        breakpoint: 1024,
    });
</script>

<style scoped>
    .hero-section {
        position: relative;
        height: 1000px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    .hero-container {
        position: relative;
        width: 100%;
        max-width: 1920px;
        height: 100%;
        margin: 0 auto;
    }

    .hero-title {
        font-family: 'Space Mono', monospace;
        font-size: clamp(220px, 18vw, 260px);
        font-weight: 700;
        color: var(--essential-text-color);
        text-transform: uppercase;
        letter-spacing: -10px;
        line-height: 25px;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        top: 216px;
        margin: 0;
        z-index: 8;
    }

    .hero-image {
        position: absolute;
        width: 750px;
        height: 750px;
        left: 50%;
        top: 190px;
        z-index: 9;
        will-change: transform;
    }

    .hero-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        animation: heroFloat 8s ease-in-out infinite;
        pointer-events: none;
    }

    .hero-text-left {
        position: absolute;
        left: 43px;
        top: 390px;
        width: 300px;
        z-index: 11;
    }

    .hero-text-left p {
        font-family: 'Inter', sans-serif;
        font-size: 17px;
        font-weight: 400;
        color: var(--essential-text-color);
        line-height: 26px;
        margin: 0;
    }

    .hero-text-right {
        position: absolute;
        right: 50px;
        top: 390px;
        text-align: right;
        z-index: 10;
    }

    .hero-subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 17px;
        font-weight: 400;
        color: var(--essential-text-color);
        line-height: 26px;
    }

    .hero-email {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 14px;
        font-weight: 500;
        color: var(--essential-text-color);
        text-transform: uppercase;
        text-decoration: none;
        line-height: 25px;
    }

    .hero-social {
        position: absolute;
        left: 43px;
        bottom: 50px;
        z-index: 12;
    }

    .hero-social a {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 14px;
        font-weight: 500;
        color: var(--essential-text-color);
        text-transform: uppercase;
        text-decoration: none;
        line-height: 25px;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .hero-container {
            transform: translateY(-25px);
        }

        .hero-title {
            font-size: 244px;
        }
    }

    @media (max-width: 1024px) {
        .hero-section {
            height: auto;
            min-height: 700px;
            padding-bottom: 200px;
        }

        .hero-container {
            transform: none;
            padding-bottom: 0;
        }

        .hero-title {
            font-size: clamp(120px, 18vw, 200px);
            position: relative;
            left: auto;
            top: auto;
            text-align: center;
            transform: none;
            margin: 40px 0 30px;
        }

        .hero-image {
            position: relative !important;
            left: auto !important;
            top: auto !important;
            transform: none !important;
            width: clamp(400px, 50vw, 550px);
            height: clamp(400px, 50vw, 550px);
            margin: 0 auto 40px;
            display: block;
        }

        .hero-image img {
            animation: heroFloat 8s ease-in-out infinite;
            transform: none !important;
        }

        .hero-text-left {
            position: static;
            width: 100%;
            text-align: center;
            margin: 0 0 20px;
            padding: 0 20px;
        }

        .hero-text-left p {
            font-size: 16px;
            line-height: 24px;
            margin: 5px 0;
        }

        .hero-text-right {
            position: static;
            width: 100%;
            text-align: center;
            margin: 0 0 20px;
            padding: 0 20px;
        }

        .hero-subtitle {
            font-size: 16px;
            line-height: 24px;
            margin: 0 0 10px;
        }

        .hero-email {
            font-size: 14px;
            display: inline-block;
        }

        .hero-social {
            position: static;
            width: 100%;
            text-align: center;
            margin: 20px 0 0;
            padding: 0 20px;
        }

        .hero-social a {
            font-size: 14px;
        }
    }

    @media (max-width: 680px) {
        .hero-section {
            min-height: 600px;
            padding-bottom: 150px;
        }

        .hero-title {
            font-size: clamp(80px, 15vw, 140px);
            margin: 30px 0 20px;
        }

        .hero-image {
            position: relative !important;
            left: auto !important;
            top: auto !important;
            transform: none !important;
            width: clamp(300px, 70vw, 400px);
            height: clamp(300px, 70vw, 400px);
            margin: 0 auto 30px;
            display: block;
        }

        .hero-image img {
            animation: heroFloat 8s ease-in-out infinite;
            transform: none !important;
        }

        .hero-text-left p {
            font-size: 14px;
            line-height: 22px;
        }

        .hero-subtitle {
            font-size: 14px;
            line-height: 22px;
        }

        .hero-email {
            font-size: 12px;
        }

        .hero-social a {
            font-size: 12px;
        }
    }

    @keyframes heroFloat {
        0% {
            transform: translateY(-12px);
        }

        50% {
            transform: translateY(12px);
        }

        100% {
            transform: translateY(-12px);
        }
    }
</style>
