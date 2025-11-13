<template>
    <div class="services-page qodef-content-behind-header">
        <!-- Video de fondo -->
        <section class="services-hero-video">
            <div class="services-hero-video-container">
                <video class="services-hero-video-element" autoplay muted playsinline loop :src="videoSrc"
                    ref="videoElement"></video>
            </div>
        </section>

        <!-- Services Marquee -->
        <ServicesMarquee />

        <!-- Services Intro Section -->
        <section class="services-intro">
            <div class="services-intro__container">
                <div class="services-intro__hero">
                    <div class="services-hero__eyebrow">
                        <span class="services-hero__dot"></span>
                        <span class="services-hero__label">portafolio</span>
                    </div>
                    <h1 class="services-hero__title">Servicios<br>que elevan tu marca</h1>
                </div>
                <div class="services-intro__content">
                    <p class="services-hero__description">Diseños pensados para generar impacto inmediato. Combina
                        branding, contenido y activaciones digitales con un equipo que entiende el lenguaje visual de
                        hoy.</p>
                </div>
            </div>
        </section>

        <!-- Services Image Gallery -->
        <section class="services-image-gallery">
            <div class="services-image-gallery__container">
                <div class="services-image-gallery__grid">
                    <div v-for="(service, index) in services" :key="service.id || index"
                        class="services-image-gallery__item">
                        <Image v-if="service.imageUrl" :src="service.imageUrl" 
                            :alt="service.title || 'Service image'"
                            class="services-image-gallery__image" preview />
                    </div>
                </div>
            </div>
        </section>
        <div class="services-gallery__divider"></div>

        <!-- Contact Form & Services Section -->
        <section class="services-contact-section">
            <div class="services-contact-section__container">
                <div class="services-contact-section__form">
                    <h2 class="services-contact-section__title">¿En qué podemos ayudarte?</h2>
                    <form @submit.prevent="handleContactSubmit" class="services-contact-form">
                        <div class="services-contact-form__field">
                            <input v-model="contactForm.email" type="email" name="email" placeholder="E-mail" required
                                class="services-contact-form__input" />
                        </div>
                        <div class="services-contact-form__row">
                            <div class="services-contact-form__field">
                                <input v-model="contactForm.firstName" type="text" name="first-name"
                                    placeholder="Nombre" required class="services-contact-form__input" />
                            </div>
                            <div class="services-contact-form__field">
                                <input v-model="contactForm.lastName" type="text" name="last-name"
                                    placeholder="Apellido" required class="services-contact-form__input" />
                            </div>
                        </div>
                        <div class="services-contact-form__field">
                            <textarea v-model="contactForm.message" name="message" placeholder="Mensaje" rows="5"
                                required class="services-contact-form__textarea"></textarea>
                        </div>
                        <button type="submit" class="services-contact-form__submit" :disabled="contactForm.submitting">
                            <span v-if="!contactForm.submitting">enviar_mensaje</span>
                            <span v-else>Enviando...</span>
                        </button>
                    </form>
                </div>
                <div class="services-contact-section__services">
                    <div v-if="isLoading" class="services-gallery__state services-gallery__state--loading">
                        <span class="services-spinner"></span>
                        <p>Cargando portafolio…</p>
                    </div>

                    <div v-else-if="hasError" class="services-gallery__state services-gallery__state--error">
                        <p>No pudimos cargar los servicios. ¿Intentamos de nuevo?</p>
                        <button type="button" class="services-retry-button" @click="retryFetch">
                            Reintentar
                        </button>
                    </div>

                    <div v-else-if="!services.length" class="services-gallery__state services-gallery__state--empty">
                        <p>No hay servicios publicados todavía. Vuelve pronto.</p>
                    </div>

                    <div v-else class="services-gallery__wrapper">
                        <div class="services-gallery__eyebrow">
                            <span class="services-gallery__dot"></span>
                            <span class="services-gallery__label">Nuestros servicios</span>
                        </div>
                        <div class="services-gallery__list">
                            <article v-for="(service, index) in services" :key="service.uuid" class="services-card"
                                :class="{
                                    'services-card--popular': service.isPopular,
                                }">
                                <div class="services-card__header">
                                    <span class="services-card__index">{{ formatIndex(index) }}</span>
                                    <div class="services-card__meta">
                                        <span v-if="service.isPopular" class="services-card__badge">Popular</span>
                                        <span class="services-card__title">{{ service.title }}</span>
                                    </div>
                                </div>

                                <div class="services-card__body">
                                    <p class="services-card__description">
                                        {{ service.description }}
                                    </p>
                                </div>

                                <div class="services-card__footer">
                                    <RouterLink :to="serviceTarget(service)" class="services-card__cta">
                                        <span>Ver proyecto</span>
                                        <i class="pi pi-arrow-right"></i>
                                    </RouterLink>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
    import { computed, onMounted, reactive, ref } from 'vue';
    import { RouterLink } from 'vue-router';
    import { useSiteServices } from '../../composables/useSiteServices';
    import ServicesMarquee from '../sections/ServicesMarquee.vue';
    import axios from 'axios';
    import { useToast } from 'primevue/usetoast';
    import Image from 'primevue/image';

    const toast = useToast();
    const servicesStore = useSiteServices();

    const activeFilter = ref('all');
    const videoElement = ref(null);
    const videoSrc = '/videos/main-home-video-2.mp4';

    const services = computed(() => servicesStore.services.value);
    const isLoading = computed(() => servicesStore.isLoading.value);
    const hasError = computed(() => !!servicesStore.error.value);

    const contactForm = reactive({
        email: '',
        firstName: '',
        lastName: '',
        message: '',
        submitting: false,
    });

    const handleContactSubmit = async () => {
        if (contactForm.submitting) return;

        contactForm.submitting = true;

        try {
            await axios.post('/api/contact-messages', {
                email: contactForm.email,
                name: `${contactForm.firstName} ${contactForm.lastName}`.trim(),
                message: contactForm.message,
            });

            toast.add({
                severity: 'success',
                summary: 'Mensaje enviado',
                detail: 'Tu mensaje ha sido enviado correctamente. Te responderemos pronto.',
                life: 5000,
            });

            // Reset form
            contactForm.email = '';
            contactForm.firstName = '';
            contactForm.lastName = '';
            contactForm.message = '';
        } catch (error) {
            const message = error.response?.data?.message || 'Error al enviar el mensaje. Por favor intenta de nuevo.';
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: message,
                life: 5000,
            });
        } finally {
            contactForm.submitting = false;
        }
    };

    const fetchServices = async (filter) => {
        const popular = filter === 'popular';
        await servicesStore.fetchServices({ popular });
    };

    const selectFilter = async (filter) => {
        if (filter === activeFilter.value) {
            return;
        }

        activeFilter.value = filter;
        await fetchServices(filter);
    };

    const retryFetch = async () => {
        await fetchServices(activeFilter.value);
    };

    const serviceTarget = (service) => {
        const resolved = service.linkUrl ?? `/servicios/${service.slug}`;

        try {
            const resolvedUrl = new URL(resolved, window.location.origin);
            if (resolvedUrl.origin === window.location.origin) {
                return resolvedUrl.pathname + resolvedUrl.search + resolvedUrl.hash;
            }
        } catch (error) {
            console.warn('[ServicesPage] Invalid service link. Falling back to slug.', error);
        }

        return resolved.startsWith('http') ? resolved : `/servicios/${service.slug}`;
    };

    const formatIndex = (index) => String(index + 1).padStart(2, '0');

    onMounted(async () => {
        // Asegurar que el video se reproduzca
        if (videoElement.value) {
            videoElement.value.play().catch(err => {
                console.warn('Error al reproducir video:', err);
            });
        }

        await fetchServices(activeFilter.value);
    });
</script>

<style scoped>
    :root {
        --services-page-max-width: 1200px;
    }

    .services-page {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 0;
        padding: 0;
        margin: 0;
        margin-top: -96px;
        /* Compensar altura del header para que el video empiece desde top: 0 del viewport */
        background: var(--qode-background-color);
        color: var(--qode-text-color);
    }

    /* Video Hero Section - Full Screen */
    .services-hero-video {
        position: relative;
        width: 100%;
        height: 100vh;
        min-height: 645px;
        margin: 0;
        padding: 0;
        overflow: hidden;
        z-index: 0;
        /* Debajo del header que tiene z-index: 1000 */
    }

    .services-hero-video-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
    }

    .services-hero-video-element {
        position: absolute;
        top: -120px;
        left: 0;
        width: 100%;
        height: 125%;
        object-fit: cover;
        margin: 0;
        padding: 0;
    }

    /* Services Intro Section */
    .services-intro {
        position: relative;
        width: 100%;
        margin: 0;
        padding: 0;
        background: var(--qode-background-color);
        z-index: 1;
    }

    .services-intro__container {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        gap: clamp(24px, 6vw, 80px);
        padding-top: 0;
        padding-bottom: 0;
        padding-left: 10.5%;
        padding-right: 10%;
        max-width: 100%;
        box-sizing: border-box;

    }

    .services-intro__hero {
        flex: 0 0 auto;
        margin: 0;
        padding: 0;
        width: 50%;
    }

    .services-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--qode-text-color);
    }

    .services-hero__dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        flex-shrink: 0;
    }

    .services-hero__label {
        margin: 0;
        padding: 0;
    }

    .services-hero__title {
        font-family: 'Lexend', sans-serif;
        font-size: clamp(32px, 8vw, 56px);
        font-weight: 400;
        line-height: 1.05;
        letter-spacing: -0.01em;
        text-transform: uppercase;
        color: var(--qode-heading-color);
        margin: 0;
        padding: 0;
    }

    .services-intro__content {
        flex: 1;
        display: flex;
        flex-direction: column;
        margin: 0;
        padding: 0;
        width: 50%;
    }

    .services-hero__description {
        font-family: 'Inter', sans-serif;
        font-size: 17px;
        line-height: 1.6;
        color: rgba(23, 23, 23, 0.78);
        margin: 0;
        padding: 0;
        max-width: 100%;
        padding-left: 9%;
    }

    body.dark-mode .services-hero__description {
        color: rgba(243, 243, 243, 0.78);
    }

    /* Services Image Gallery */
    .services-image-gallery {
        position: relative;
        width: 100%;
        padding-top: 225px;
        padding-bottom: 100px;
        padding-left: 0;
        padding-right: 0;
        background: var(--qode-background-color);
        z-index: 1;
    }

    .services-image-gallery__container {
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        padding: 0;
        box-sizing: border-box;
        display: flex;
        justify-content: center;
    }

    .services-image-gallery__grid {
        position: relative;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(293px, 1fr));
        row-gap: 75px;
        column-gap: 75px;
        width: 100%;
        max-width: 1400px;
        justify-items: center;
    }

    .services-image-gallery__item {
        position: relative;
        width: 100%;
        max-width: 293px;
        margin: 0;
        padding: 0;
        aspect-ratio: 293 / 604;
        overflow: hidden;
        border-radius: 50px;
    }

    .services-image-gallery__link {
        display: block;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        text-decoration: none;
        overflow: hidden;
    }

    .services-image-gallery__image {
        width: 100%;
        height: 100%;
        display: block;
    }

    .services-image-gallery__image :deep(img) {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.3s ease;
        border-radius: 50px;
    }

    .services-image-gallery__image:hover :deep(img) {
        transform: scale(1.05);
    }

    .services-gallery {
        position: relative;
        max-width: var(--services-page-max-width);
        width: 100%;
        margin: 0 auto;
        padding: 24px 20px 80px;
        z-index: 1;
        background: var(--qode-background-color);
    }

    .services-gallery__state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
        padding: 60px 20px;
        font-family: 'Inter', sans-serif;
        border: 1px dashed rgba(0, 0, 0, 0.12);
        border-radius: 16px;
        text-align: center;
        color: rgba(23, 23, 23, 0.72);
    }

    body.dark-mode .services-gallery__state {
        border-color: rgba(255, 255, 255, 0.15);
        color: rgba(243, 243, 243, 0.72);
    }

    .services-gallery__state--loading .services-spinner {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 3px solid rgba(23, 23, 23, 0.15);
        border-top-color: #dd3333;
        animation: services-spin 0.9s linear infinite;
    }

    body.dark-mode .services-gallery__state--loading .services-spinner {
        border-color: rgba(255, 255, 255, 0.15);
        border-top-color: #ff6666;
    }

    @keyframes services-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .services-retry-button {
        border: 1px solid currentColor;
        background: transparent;
        color: inherit;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 10px 18px;
        border-radius: 999px;
        cursor: pointer;
        transition: background 0.25s ease, color 0.25s ease;
    }

    .services-retry-button:hover {
        background: #dd3333;
        color: #ffffff;
        border-color: #dd3333;
    }

    .services-gallery__wrapper {
        display: flex;
        flex-direction: column;
        gap: 40px;
    }

    .services-gallery__divider {
        width: 100%;
        height: 1px;
        background: rgba(0, 0, 0, 0.08);
        margin: 0;
        padding: 0;
    }

    body.dark-mode .services-gallery__divider {
        background: rgba(255, 255, 255, 0.12);
    }

    .services-gallery__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--qode-text-color);
        margin: 0;
        padding: 0;
    }

    .services-gallery__dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        flex-shrink: 0;
    }

    .services-gallery__label {
        margin: 0;
        padding: 0;
    }

    .services-gallery__list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 40px 60px;
        padding-top: 0;
    }

    .services-card {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 16px;
        padding: 0;
        border-bottom: none;
    }

    .services-card__header {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .services-card__index {
        font-family: 'Space Mono', monospace;
        font-size: 18px;
        letter-spacing: 0.1em;
        color: rgba(23, 23, 23, 0.44);
        min-width: 42px;
    }

    body.dark-mode .services-card__index {
        color: rgba(243, 243, 243, 0.38);
    }

    .services-card__meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .services-card__badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #dd3333;
    }

    .services-card__badge::before {
        content: '';
        width: 20px;
        height: 1px;
        background: currentColor;
    }

    body.dark-mode .services-card__badge {
        color: #ff6666;
    }

    .services-card__title {
        font-family: 'Space Mono', monospace;
        font-size: 20px;
        text-transform: uppercase;
    }

    .services-card__body {
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex: 1;
    }

    .services-card__description {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: rgba(23, 23, 23, 0.74);
        max-width: 60ch;
    }

    body.dark-mode .services-card__description {
        color: rgba(243, 243, 243, 0.78);
    }


    .services-card__footer {
        display: flex;
    }

    .services-card__cta {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        text-decoration: none;
        color: inherit;
        position: relative;
        padding-bottom: 4px;
    }

    .services-card__cta::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 1px;
        background: currentColor;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .services-card__cta:hover::after {
        transform: scaleX(1);
    }

    .services-card__cta .pi {
        font-size: 12px;
    }

    @media (min-width: 768px) {
        .services-page {
            padding: 0;
            gap: 0;
        }

        .services-hero__header {
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-end;
        }

        .services-hero__actions {
            align-self: flex-end;
        }

        .services-card__body {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }

    @media (max-width: 1200px) {
        .services-image-gallery__grid {
            row-gap: 60px;
            column-gap: 60px;
        }
    }

    @media (max-width: 1024px) {
        .services-intro__container {
            flex-direction: row;
            gap: clamp(20px, 4vw, 40px);
            padding-left: 5%;
            padding-right: 5%;
        }

        .services-hero__title {
            font-size: clamp(28px, 6vw, 44px);
        }

        .services-hero__description {
            font-size: 14px;
        }

        .services-image-gallery__grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            row-gap: 30px;
            column-gap: 30px;
        }
    }

    @media (max-width: 880px) {
        .services-image-gallery__grid {
            row-gap: 30px;
            column-gap: 30px;
        }
    }

    @media (max-width: 758px) {
        .services-intro__container {
            flex-direction: column;
            align-items: flex-start;
            gap: 24px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .services-intro__hero {
            width: 100%;
        }

        .services-hero__title {
            font-size: clamp(28px, 8vw, 40px);
        }

        .services-hero__description {
            font-size: 14px;
        }

        .services-intro__content {
            width: 100%;
        }

        .services-image-gallery {
            padding-top: 120px;
            padding-bottom: 120px;
        }

        .services-image-gallery__container {
            padding: 0 20px;
        }

        .services-image-gallery__grid {
            max-width: 100%;
        }

    }

    @media (min-width: 1513px) {
        .services-image-gallery__container {
            max-width: 1400px;
        }
    }

    @media (min-width: 1024px) {
        .services-gallery__list {
            grid-template-columns: repeat(2, 1fr);
            gap: 32px 40px;
        }
    }

    @media (min-width: 1280px) {
        .services-page {
            padding: 0;
        }

        .services-gallery__list {
            grid-template-columns: repeat(3, 1fr);
            gap: 48px 60px;
        }
    }

    @media (min-width: 1600px) {
        .services-page {
            padding: 0;
        }

        .services-gallery__list {
            grid-template-columns: repeat(3, 1fr);
            gap: 48px 80px;
        }
    }

    /* Services Contact Section */
    .services-contact-section {
        position: relative;
        width: 100%;
        padding-top: 120px;
        padding-bottom: 120px;
        padding-left: 0;
        padding-right: 0;
        background: var(--qode-background-color);
        z-index: 1;
    }

    .services-contact-section__container {
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        padding: 0 20px;
        box-sizing: border-box;
        display: flex;
        flex-direction: row;
        gap: clamp(40px, 8vw, 120px);
        align-items: flex-start;
    }

    .services-contact-section__form {
        flex: 0 0 auto;
        width: 50%;
        max-width: 600px;
    }

    .services-contact-section__title {
        font-family: 'Lexend', sans-serif;
        font-weight: 400;
        color: var(--qode-heading-color);
        font-size: 56px;
        line-height: 1.07143em;
        text-transform: uppercase;
        margin: 25px 0 40px 0;
        word-wrap: break-word;
    }

    .services-contact-form {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .services-contact-form__row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .services-contact-form__field {
        position: relative;
        width: 100%;
    }

    .services-contact-form__input,
    .services-contact-form__textarea {
        width: 100%;
        border: none;
        border-bottom: 1px solid rgba(23, 23, 23, 0.6);
        background: transparent;
        font-family: 'Inter', sans-serif;
        font-size: 16px;
        color: var(--qode-text-color);
        padding: 12px 0;
        outline: none;
        transition: border-color 0.3s ease;
    }

    body.dark-mode .services-contact-form__input,
    body.dark-mode .services-contact-form__textarea {
        border-bottom-color: rgba(255, 255, 255, 0.6);
    }

    .services-contact-form__input:focus,
    .services-contact-form__textarea:focus {
        border-bottom-color: var(--qode-text-color);
    }

    .services-contact-form__input::placeholder,
    .services-contact-form__textarea::placeholder {
        color: rgba(23, 23, 23, 0.8);
        font-family: 'Inter', sans-serif;
    }

    body.dark-mode .services-contact-form__input::placeholder,
    body.dark-mode .services-contact-form__textarea::placeholder {
        color: rgba(255, 255, 255, 0.8);
    }

    .services-contact-form__textarea {
        resize: vertical;
        min-height: 120px;
        font-family: 'Inter', sans-serif;
    }

    .services-contact-form__submit {
        align-self: flex-start;
        border: none;
        background: #171717;
        color: #dd3333;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: lowercase;
        padding: 10px 20px;
        border-radius: 999px;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.2s ease;
        margin-top: 8px;
    }

    body.dark-mode .services-contact-form__submit {
        background: #ffffff;
    }

    .services-contact-form__submit:hover:not(:disabled) {
        background: #2a2a2a;
        transform: translateY(-2px);
    }

    body.dark-mode .services-contact-form__submit:hover:not(:disabled) {
        background: #e5e5e5;
    }

    .services-contact-form__submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .services-contact-section__services {
        flex: 1;
        width: 50%;
    }

    .services-contact-services__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 32px;
    }

    .services-contact-services__item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .services-contact-services__title {
        font-family: 'Space Mono', monospace;
        font-size: 18px;
        font-weight: 400;
        text-transform: uppercase;
        color: var(--qode-heading-color);
        margin: 0;
        padding: 0;
        line-height: 1.3;
    }

    .services-contact-services__description {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: rgba(23, 23, 23, 0.78);
        margin: 0;
        padding: 0;
    }

    body.dark-mode .services-contact-services__description {
        color: rgba(243, 243, 243, 0.78);
    }

    @media (max-width: 1024px) {
        .services-contact-section__container {
            flex-direction: column;
            gap: 60px;
        }

        .services-contact-section__form {
            width: 100%;
            max-width: 100%;
        }

        .services-contact-section__title {
            font-size: clamp(32px, 8vw, 48px);
        }

        .services-contact-section__services {
            width: 100%;
        }

        .services-contact-services__grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 758px) {
        .services-contact-section {
            padding-top: 80px;
            padding-bottom: 80px;
        }

        .services-contact-form__row {
            grid-template-columns: 1fr;
            gap: 32px;
        }

        .services-contact-services__grid {
            grid-template-columns: 1fr;
        }
    }
</style>
