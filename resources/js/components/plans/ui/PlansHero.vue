<template>
    <section class="plans-hero" ref="heroContainer" @mousemove="handleHeroMouseMove" @mouseleave="resetHeroOffsets">
        <div class="plans-hero__layout">
            <div class="plans-hero__text">
                <div class="essential-eyebrow">
                    <span class="essential-eyebrow__dot"></span>
                    <span class="essential-eyebrow__label">Suscripciones esenciales</span>
                </div>

                <h1 class="plans-hero__title">
                    Planes que escalan<br>
                    contigo
                </h1>

                <p class="plans-hero__description">
                    Diseñamos tiers pensados para artistas, marcas y agencias que necesitan producción creativa
                    constante.
                    Escoge el plan que refleja tu ritmo actual o cuéntanos qué necesitas y lo personalizamos.
                </p>

                <div class="plans-hero__actions">
                    <button type="button" class="essential-contact-cta essential-contact-cta--primary"
                        @click="$emit('scroll-to-plans')">
                        Comparar planes
                    </button>
                    <RouterLink :to="{ name: 'contact', query: { from: 'plans' } }"
                        class="essential-contact-cta essential-contact-cta--ghost">
                        Habla con nosotros
                    </RouterLink>
                </div>
            </div>
            <div class="plans-hero__visual">
                <div class="plans-hero__image" :style="heroImageStyle">
                    <img src="/images/liquid-removebg-preview.png" alt="Visual Essential Plans" loading="lazy">
                </div>
                <div class="plans-hero__glow"></div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { RouterLink } from 'vue-router';
    import { useContactHero } from '@/composables/useContactHero';

    defineEmits(['scroll-to-plans']);

    const { heroContainer, heroImageStyle, handleHeroMouseMove, resetHeroOffsets } = useContactHero();
</script>

<style scoped>
    .plans-hero {
        position: relative;
        overflow: hidden;
    }

    .plans-hero__layout {
        display: flex;
        flex-direction: column;
        gap: clamp(28px, 8vw, 64px);
        align-items: flex-start;
    }

    .plans-hero__text {
        display: flex;
        flex-direction: column;
        gap: clamp(16px, 5vw, 32px);
        max-width: 720px;
        width: 100%;
        z-index: 2;
        box-sizing: border-box;
    }

    .plans-hero__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(42px, 8vw, 68px);
        letter-spacing: -0.015em;
        line-height: 1.05;
        text-transform: uppercase;
        color: var(--essential-heading-color);
    }

    .plans-hero__description {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: clamp(16px, 3.6vw, 18px);
        line-height: 1.75;
        max-width: 58ch;
        color: rgba(23, 23, 23, 0.78);
    }

    body.dark-mode .plans-hero__description {
        color: rgba(243, 243, 243, 0.78);
    }

    .plans-hero__actions {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .plans-hero__visual {
        position: relative;
        align-self: center;
        width: min(420px, 70vw);
        aspect-ratio: 1;
    }

    .plans-hero__image {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        will-change: transform;
        transition: transform 0.12s linear;
    }

    .plans-hero__image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        pointer-events: none;
        filter: saturate(110%);
        animation: plans-hero-float 8s ease-in-out infinite;
    }

    .plans-hero__glow {
        position: absolute;
        inset: 12%;
        border-radius: 50%;
        background: radial-gradient(circle at 50% 50%, rgba(221, 51, 51, 0.32) 0%, rgba(221, 51, 51, 0) 72%);
        z-index: 1;
    }

    @keyframes plans-hero-float {

        0%,
        100% {
            transform: translate3d(0, 0, 0) scale(1);
        }

        50% {
            transform: translate3d(0, -16px, 0) scale(1.015);
        }
    }

    @media (min-width: 960px) {
        .plans-hero__layout {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .plans-hero__visual {
            width: clamp(320px, 36vw, 540px);
            margin-right: clamp(0px, 4vw, 48px);
        }

        .plans-hero__actions {
            flex-direction: row;
            align-items: center;
        }
    }

    @media (max-width: 480px) {
        .plans-hero__title {
            font-size: clamp(28px, 7vw, 42px);
            line-height: 1.1;
        }

        .plans-hero__description {
            font-size: 15px;
        }
    }
</style>
