<template>
    <section class="services-image-gallery">
        <div class="services-image-gallery__container">
            <div class="services-image-gallery__grid">
                <div v-for="(service, index) in services" :key="service.id || index"
                    class="services-image-gallery__item">
                    <Image v-if="service.imageUrl" :src="service.imageUrl" :alt="service.title || 'Service image'"
                        class="services-image-gallery__image" preview />
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import Image from 'primevue/image';

    defineProps({
        services: {
            type: Array,
            required: true,
        },
    });
</script>

<style scoped>
    .services-image-gallery {
        position: relative;
        width: 100%;
        padding-top: 225px;
        padding-bottom: 100px;
        padding-left: 0;
        padding-right: 0;
        background: var(--essential-background-color);
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

    @media (max-width: 1200px) {
        .services-image-gallery__grid {
            row-gap: 60px;
            column-gap: 60px;
        }
    }

    @media (max-width: 1024px) {
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
</style>

