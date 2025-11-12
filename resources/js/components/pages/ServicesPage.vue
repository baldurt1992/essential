<template>
    <div class="services-page">
        <section class="services-hero">
            <div class="services-hero__eyebrow">
                <span class="services-hero__dot"></span>
                <span class="services-hero__label">Portafolio</span>
            </div>

            <div class="services-hero__header">
                <div class="services-hero__title-group">
                    <h1 class="services-hero__title">
                        Servicios<br>
                        que elevan tu marca
                    </h1>
                    <p class="services-hero__description">
                        Diseños pensados para generar impacto inmediato. Combina branding, contenido y activaciones
                        digitales con un equipo que entiende el lenguaje visual de hoy.
                    </p>
                </div>

                <div class="services-hero__actions">
                    <button type="button" class="services-filter-button" :class="{
                        'services-filter-button--active': activeFilter === 'all',
                    }" @click="selectFilter('all')" :disabled="isLoading">
                        Todos
                    </button>
                    <button type="button" class="services-filter-button" :class="{
                        'services-filter-button--active': activeFilter === 'popular',
                    }" @click="selectFilter('popular')" :disabled="isLoading">
                        Populares
                    </button>
                </div>
            </div>
        </section>

        <section class="services-gallery">
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

            <div v-else class="services-gallery__list">
                <article v-for="(service, index) in services" :key="service.uuid"
                    class="services-card services-card--interactive" :class="{
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
                        <div class="services-card__media" :data-distort-id="`distort-${index}`">
                            <Image preview :src="service.imageUrl" :alt="service.title"
                                class="services-card__image-wrapper" image-class="services-card__image"
                                :pt="getImagePassThrough(index)" />
                            <svg class="services-card__distort" viewBox="0 0 100 100" :data-strength="25">
                                <defs>
                                    <filter :id="`filter-distort-${index}`" x="-0%" y="-0%" width="100%" height="100%"
                                        filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse"
                                        color-interpolation-filters="sRGB">
                                        <feTurbulence type="fractalNoise" baseFrequency="0.01 0.02" numOctaves="3"
                                            seed="2" result="turbulencebase" />
                                        <feColorMatrix in="turbulencebase" type="hueRotate" values="0"
                                            result="turbulence" />
                                        <feDisplacementMap in="SourceGraphic" in2="turbulence" scale="0"
                                            xChannelSelector="R" yChannelSelector="B" result="displacement1" />
                                        <feMerge result="merge">
                                            <feMergeNode in="SourceGraphic" />
                                            <feMergeNode in="displacement1" />
                                        </feMerge>
                                    </filter>
                                </defs>
                            </svg>
                        </div>
                    </div>

                    <div class="services-card__footer">
                        <RouterLink :to="serviceTarget(service)" class="services-card__cta">
                            <span>Ver proyecto</span>
                            <i class="pi pi-arrow-right"></i>
                        </RouterLink>
                    </div>
                </article>
            </div>
        </section>
    </div>
</template>

<script setup>
    import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';
    import { RouterLink } from 'vue-router';
    import { useSiteServices } from '../../composables/useSiteServices';
    import Image from 'primevue/image';

    const servicesStore = useSiteServices();

    const activeFilter = ref('all');
    const effectController = reactive({
        animationFrames: new Map(),
        listeners: new Map(),
    });
    const shouldUseDistort = ref(false);

    const services = computed(() => servicesStore.services.value);
    const isLoading = computed(() => servicesStore.isLoading.value);
    const hasError = computed(() => !!servicesStore.error.value);

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
        await refreshDistortEffects();
    };

    const retryFetch = async () => {
        await fetchServices(activeFilter.value);
        await refreshDistortEffects();
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

    const updateDistortPreference = () => {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const hasFinePointer = window.matchMedia('(pointer: fine)').matches;
        const isDesktop = window.matchMedia('(min-width: 1280px)').matches;

        shouldUseDistort.value = !prefersReducedMotion && hasFinePointer && isDesktop;
    };

    const startDistortion = (index) => {
        const filter = document.querySelector(`#filter-distort-${index}`);
        if (!filter) return;

        const displacementMap = filter.querySelector('feDisplacementMap');
        const colorMatrix = filter.querySelector('feColorMatrix');
        if (!displacementMap || !colorMatrix) return;

        const img = document.querySelector(`[data-filter-id=\"filter-distort-${index}\"]`);
        if (img) {
            img.style.filter = `url(#filter-distort-${index})`;
        }

        const strength = 25;
        const duration = 600;
        const startTimestamp = performance.now();

        const animate = (timestamp) => {
            const elapsed = timestamp - startTimestamp;
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

            displacementMap.setAttribute('scale', String(scale));
            colorMatrix.setAttribute('values', String(hueRotate));

            if (progress < 1) {
                effectController.animationFrames.set(index, requestAnimationFrame(animate));
            } else {
                if (img) {
                    img.style.filter = 'none';
                }
                displacementMap.setAttribute('scale', '0');
                colorMatrix.setAttribute('values', '0');
            }
        };

        effectController.animationFrames.set(index, requestAnimationFrame(animate));
    };

    const stopDistortion = (index) => {
        const frame = effectController.animationFrames.get(index);
        if (frame) {
            cancelAnimationFrame(frame);
            effectController.animationFrames.delete(index);
        }

        const filter = document.querySelector(`#filter-distort-${index}`);
        if (filter) {
            const displacementMap = filter.querySelector('feDisplacementMap');
            const colorMatrix = filter.querySelector('feColorMatrix');
            displacementMap?.setAttribute('scale', '0');
            colorMatrix?.setAttribute('values', '0');
        }

        const img = document.querySelector(`[data-filter-id=\"filter-distort-${index}\"]`);
        if (img) {
            img.style.filter = 'none';
        }
    };

    const teardownDistortEffects = () => {
        effectController.animationFrames.forEach((frame) => cancelAnimationFrame(frame));
        effectController.animationFrames.clear();

        effectController.listeners.forEach(({ element, handlers }) => {
            element.removeEventListener('mouseenter', handlers.enter);
            element.removeEventListener('mouseleave', handlers.leave);
        });
        effectController.listeners.clear();
    };

    const refreshDistortEffects = async () => {
        teardownDistortEffects();

        if (!shouldUseDistort.value || !services.value.length) {
            return;
        }

        await nextTick();

        const firstWrapper = document.querySelector('[data-distort-id=\"distort-0\"]');
        let baseTop = 35;

        if (firstWrapper) {
            const firstArticle = firstWrapper.closest('.services-card');
            const list = firstWrapper.closest('.services-gallery__list');
            if (firstArticle && list) {
                const articleRect = firstArticle.getBoundingClientRect();
                const listRect = list.getBoundingClientRect();
                baseTop = articleRect.top - listRect.top + articleRect.height / 2;
            }
        }

        services.value.forEach((_service, index) => {
            const wrapper = document.querySelector(`[data-distort-id=\"distort-${index}\"]`);
            if (!wrapper) return;

            wrapper.style.top = `${baseTop}px`;
            wrapper.style.transform = 'translateY(-50%)';

            const card = wrapper.closest('.services-card');
            if (!card) return;

            const enter = () => startDistortion(index);
            const leave = () => stopDistortion(index);

            card.addEventListener('mouseenter', enter);
            card.addEventListener('mouseleave', leave);

            effectController.listeners.set(index, {
                element: card,
                handlers: { enter, leave },
            });
        });
    };

    const handleResize = () => {
        const previous = shouldUseDistort.value;
        updateDistortPreference();
        if (previous !== shouldUseDistort.value) {
            refreshDistortEffects();
        }
    };

    watch(services, async () => {
        await refreshDistortEffects();
    });

    watch(shouldUseDistort, async () => {
        await refreshDistortEffects();
    });

    onMounted(async () => {
        updateDistortPreference();
        window.addEventListener('resize', handleResize, { passive: true });

        await fetchServices(activeFilter.value);
        await refreshDistortEffects();
    });

    onBeforeUnmount(() => {
        window.removeEventListener('resize', handleResize);
        teardownDistortEffects();
    });

    const getImagePassThrough = (index) => ({
        image: {
            loading: 'lazy',
            decoding: 'async',
            'data-filter-id': `filter-distort-${index}`,
        },
        pcPreviewButton: {
            class: 'services-preview-trigger',
        },
    });
</script>

<style scoped>
    :root {
        --services-page-max-width: 1200px;
    }

    .services-page {
        display: flex;
        flex-direction: column;
        gap: 48px;
        padding: 24px 20px 80px;
        background: var(--qode-background-color);
        color: var(--qode-text-color);
    }

    .services-hero {
        display: flex;
        flex-direction: column;
        gap: 24px;
        max-width: var(--services-page-max-width);
        width: 100%;
        margin: 0 auto;
    }

    .services-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .services-hero__dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .services-hero__header {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .services-hero__title-group {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .services-hero__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(36px, 9vw, 60px);
        font-weight: 400;
        letter-spacing: -0.01em;
        line-height: 1.05;
        text-transform: uppercase;
    }

    .services-hero__description {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        line-height: 1.6;
        max-width: 28ch;
        color: rgba(23, 23, 23, 0.78);
    }

    body.dark-mode .services-hero__description {
        color: rgba(243, 243, 243, 0.78);
    }

    .services-hero__actions {
        display: inline-flex;
        gap: 10px;
    }

    .services-filter-button {
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
        transition: background 0.25s ease, color 0.25s ease, transform 0.25s ease;
    }

    .services-filter-button:disabled {
        opacity: 0.5;
        cursor: wait;
    }

    .services-filter-button--active {
        background: #dd3333;
        border-color: #dd3333;
        color: #ffffff;
    }

    .services-filter-button:not(.services-filter-button--active):hover {
        transform: translateY(-1px);
        background: rgba(23, 23, 23, 0.06);
    }

    body.dark-mode .services-filter-button:not(.services-filter-button--active):hover {
        background: rgba(243, 243, 243, 0.08);
    }

    .services-gallery {
        max-width: var(--services-page-max-width);
        width: 100%;
        margin: 0 auto;
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

    .services-gallery__list {
        display: flex;
        flex-direction: column;
        border-top: 1px solid rgba(0, 0, 0, 0.08);
    }

    body.dark-mode .services-gallery__list {
        border-top-color: rgba(255, 255, 255, 0.12);
    }

    .services-card {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 22px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    }

    body.dark-mode .services-card {
        border-bottom-color: rgba(255, 255, 255, 0.12);
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
        gap: 24px;
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

    .services-card__media {
        position: relative;
        width: min(100%, 320px);
        overflow: hidden;
        isolation: isolate;
        aspect-ratio: 4 / 5;
        background: rgba(0, 0, 0, 0.02);
    }

    .services-card__image-wrapper {
        display: block;
        width: 100%;
        height: 100%;
    }

    .services-card__image {
        display: block;
        width: 100%;
        height: auto;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .services-card__media :deep(.p-image) {
        display: block;
        width: 100%;
        height: 100%;
    }

    .services-card__media :deep(.p-image img) {
        width: 100%;
        height: auto;
    }

    .services-card__media :deep(.p-image-preview-indicator) {
        background: rgba(23, 23, 23, 0.85);
        color: #f5f5f5;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        transition: transform 0.25s ease;
    }

    .services-card__media :deep(.p-image-preview-indicator:hover) {
        transform: scale(1.05);
    }

    .services-card__media :deep(.p-image-preview-content) {
        background: rgba(0, 0, 0, 0.85);
        padding: 0;
    }

    .services-card__media :deep(.p-image-preview-container) {
        width: min(90vw, 520px);
        max-height: min(80vh, 620px);
    }

    .services-card__media :deep(.p-image-preview-container img) {
        width: 100%;
        height: auto;
        object-fit: contain;
    }

    .services-card--interactive:hover .services-card__image {
        transform: scale(1.02);
    }

    .services-card__distort {
        display: none;
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
            padding: 40px 40px 120px;
            gap: 64px;
        }

        .services-hero__header {
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-end;
        }

        .services-hero__description {
            max-width: 36ch;
        }

        .services-hero__actions {
            align-self: flex-end;
        }

        .services-card__body {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }

        .services-card__media {
            width: clamp(260px, 45vw, 420px);
        }
    }

    @media (max-width: 758px) {
        .services-card__media {
            position: relative;
            top: auto;
            right: auto;
            opacity: 1;
            pointer-events: auto;
        }
    }

    @media (min-width: 1024px) {
        .services-card__body {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: clamp(24px, 6vw, 48px);
        }

        .services-card__media {
            position: relative;
            right: auto;
            top: auto;
            opacity: 1;
            pointer-events: auto;
            margin-left: auto;
        }

        .services-card__description {
            max-width: 44ch;
        }
    }

    @media (min-width: 1280px) {
        .services-page {
            padding: 60px 80px 140px;
        }

        .services-gallery__list {
            border-top-width: 2px;
        }

        .services-card {
            padding: 36px 0;
            border-bottom-width: 2px;
        }

        .services-card__distort {
            display: block;
            position: absolute;
            inset: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        .services-card__media {
            position: absolute;
            right: clamp(300px, 9vw, 300px);
            top: 40px;
            width: clamp(220px, 32vw, 320px);
            opacity: 0;
            transition: opacity 0.2s ease-out;
            pointer-events: none;
        }

        .services-card--interactive:hover .services-card__media {
            opacity: 1;
            pointer-events: auto;
        }
    }

    @media (min-width: 1600px) {
        .services-page {
            padding: 60px 80px 140px;
        }

        .services-gallery__list {
            border-top-width: 2px;
        }

        .services-card {
            padding: 36px 0;
            border-bottom-width: 2px;
        }

        .services-card__distort {
            display: block;
            position: absolute;
            inset: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        .services-card__media {
            position: absolute;
            right: clamp(600px, 9vw, 300px);
            top: 40px;
            width: clamp(220px, 32vw, 320px);
            opacity: 0;
            transition: opacity 0.2s ease-out;
            pointer-events: none;
        }

        .services-card--interactive:hover .services-card__media {
            opacity: 1;
            pointer-events: auto;
        }
    }
</style>
