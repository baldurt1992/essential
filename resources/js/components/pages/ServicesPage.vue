<template>
    <div class="services-page essential-content-behind-header">
        <!-- Video de fondo -->
        <ServicesHeroVideo :video-src="videoSrc" />

        <!-- Services Marquee -->
        <ServicesMarquee />

        <!-- Services Intro Section -->
        <ServicesIntro eyebrow="portafolio" title="Servicios<br>que elevan tu marca"
            description="Diseños pensados para generar impacto inmediato. Combina branding, contenido y activaciones digitales con un equipo que entiende el lenguaje visual de hoy." />

        <!-- Services Image Gallery -->
        <ServicesImageGallery :services="services" />
        <div class="essential-divider"></div>

        <!-- Contact Form & Services Section -->
        <section class="services-contact-section">
            <div class="services-contact-section__container">
                <ServicesContactForm />
                <ServicesGallery :services="services" :is-loading="isLoading" :has-error="hasError"
                    :service-target="serviceTarget" :format-index="formatIndex" @retry="retryFetch" />
            </div>
        </section>
    </div>
</template>

<script setup>
    import { onMounted } from 'vue';
    import ServicesMarquee from '../sections/ServicesMarquee.vue';
    import ServicesHeroVideo from '../services/ui/ServicesHeroVideo.vue';
    import ServicesIntro from '../services/ui/ServicesIntro.vue';
    import ServicesImageGallery from '../services/ui/ServicesImageGallery.vue';
    import ServicesContactForm from '../services/forms/ServicesContactForm.vue';
    import ServicesGallery from '../services/ui/ServicesGallery.vue';
    import { useServicesPage } from '@/composables/useServicesPage';

    const videoSrc = '/videos/main-home-video-2.mp4';

    const {
        services,
        isLoading,
        hasError,
        fetchServices,
        retryFetch,
        serviceTarget,
        formatIndex,
    } = useServicesPage();

    onMounted(async () => {
        await fetchServices('all');
    });
</script>

<style scoped>
    .services-page {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 0;
        padding: 0;
        margin: 0;
        margin-top: -96px;
        background: var(--essential-background-color);
        color: var(--essential-text-color);
    }

    .services-contact-section {
        position: relative;
        width: 100%;
        padding-top: 120px;
        padding-bottom: 120px;
        padding-left: 0;
        padding-right: 0;
        background: var(--essential-background-color);
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

    @media (min-width: 768px) {
        .services-page {
            padding: 0;
            gap: 0;
        }
    }

    @media (max-width: 1024px) {
        .services-contact-section__container {
            flex-direction: column;
            gap: 60px;
        }
    }

    @media (max-width: 758px) {
        .services-contact-section {
            padding-top: 80px;
            padding-bottom: 80px;
        }
    }
</style>
