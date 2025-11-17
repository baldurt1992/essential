<template>
    <section class="contact-hero" ref="heroContainer" @mousemove="handleHeroMouseMove"
        @mouseleave="resetHeroOffsets">
        <div class="contact-hero__layout">
            <div class="contact-hero__text">
                <div class="essential-eyebrow">
                    <span class="essential-eyebrow__dot"></span>
                    <span class="essential-eyebrow__label">Contacto directo</span>
                </div>
                <h1 class="contact-hero__title">
                    Hablemos de tu próximo<br>
                    proyecto extraordinario
                </h1>
                <p class="contact-hero__description">
                    Estamos listos para acompañarte desde el primer boceto hasta el lanzamiento.
                    Compártenos tu idea y te respondemos en menos de 24 horas hábiles.
                </p>
                <div class="contact-hero__actions">
                    <button type="button" class="essential-contact-cta essential-contact-cta--primary"
                        @click="$emit('open-dialog')">
                        Enviar mensaje ahora
                    </button>
                    <a v-if="primaryWhatsAppLink" :href="primaryWhatsAppLink" target="_blank" rel="noopener"
                        class="essential-contact-cta essential-contact-cta--ghost">
                        Escríbenos por WhatsApp
                    </a>
                </div>
            </div>
            <div class="contact-hero__visual">
                <div class="contact-hero__image" :style="heroImageStyle">
                    <img src="/images/liquid-removebg-preview.png" alt="Visual Essential Contact" loading="lazy">
                </div>
                <div class="contact-hero__glow"></div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { useContactHero } from '@/composables/useContactHero';

defineProps({
    primaryWhatsAppLink: {
        type: String,
        default: null,
    },
});

defineEmits(['open-dialog']);

const { heroContainer, heroImageStyle, handleHeroMouseMove, resetHeroOffsets } = useContactHero();
</script>

<style scoped>
.contact-hero {
    position: relative;
    overflow: hidden;
}

.contact-hero__layout {
    display: flex;
    flex-direction: column;
    gap: clamp(28px, 8vw, 64px);
    align-items: flex-start;
}

.contact-hero__text {
    display: flex;
    flex-direction: column;
    gap: clamp(16px, 5vw, 32px);
    max-width: 720px;
    width: 100%;
    z-index: 2;
    box-sizing: border-box;
}

.contact-hero__title {
    margin: 0;
    font-family: 'Lexend', sans-serif;
    font-size: clamp(28px, 8vw, 72px);
    letter-spacing: -0.015em;
    line-height: 1.05;
    text-transform: uppercase;
    word-wrap: break-word;
    overflow-wrap: break-word;
    color: var(--essential-heading-color);
}

.contact-hero__description {
    margin: 0;
    font-family: 'Inter', sans-serif;
    font-size: clamp(16px, 3.6vw, 18px);
    line-height: 1.75;
    max-width: 52ch;
    color: rgba(23, 23, 23, 0.78);
}

body.dark-mode .contact-hero__description {
    color: rgba(243, 243, 243, 0.78);
}

.contact-hero__actions {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.contact-hero__visual {
    position: relative;
    align-self: center;
    width: min(420px, 70vw);
    aspect-ratio: 1;
}

.contact-hero__image {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    will-change: transform;
    transition: transform 0.12s linear;
}

.contact-hero__image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    pointer-events: none;
    filter: saturate(110%);
    animation: contact-hero-float 8s ease-in-out infinite;
}

.contact-hero__glow {
    position: absolute;
    inset: 12%;
    border-radius: 50%;
    background: radial-gradient(circle at 50% 50%, rgba(221, 51, 51, 0.32) 0%, rgba(221, 51, 51, 0) 72%);
    z-index: 1;
}

@keyframes contact-hero-float {
    0%,
    100% {
        transform: translate3d(0, 0, 0) scale(1);
    }

    50% {
        transform: translate3d(0, -16px, 0) scale(1.015);
    }
}

@media (min-width: 960px) {
    .contact-hero__layout {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }

    .contact-hero__visual {
        width: clamp(320px, 36vw, 540px);
        margin-right: clamp(0px, 4vw, 48px);
    }

    .contact-hero__actions {
        flex-direction: row;
        align-items: center;
    }
}

@media (max-width: 480px) {
    .contact-hero__title {
        font-size: clamp(24px, 7vw, 32px);
        line-height: 1.1;
    }

    .contact-hero__description {
        font-size: 15px;
    }
}
</style>

