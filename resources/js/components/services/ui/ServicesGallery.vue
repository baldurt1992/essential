<template>
    <div class="services-contact-section__services">
        <div v-if="isLoading" class="essential-state">
            <span class="essential-spinner"></span>
            <p>Cargando portafolio…</p>
        </div>

        <div v-else-if="hasError" class="essential-state">
            <p>No pudimos cargar los servicios. ¿Intentamos de nuevo?</p>
            <button type="button" class="essential-retry-button" @click="$emit('retry')">
                Reintentar
            </button>
        </div>

        <div v-else-if="!services.length" class="essential-state">
            <p>No hay servicios publicados todavía. Vuelve pronto.</p>
        </div>

        <div v-else class="services-gallery__wrapper">
            <div class="essential-eyebrow">
                <span class="essential-eyebrow__dot"></span>
                <span class="essential-eyebrow__label">Nuestros servicios</span>
            </div>
            <div class="services-gallery__list">
                <ServicesCard v-for="(service, index) in services" :key="service.uuid"
                    :index="formatIndex(index)"
                    :title="service.title"
                    :description="service.description"
                    :target="serviceTarget(service)"
                    :is-popular="service.isPopular" />
            </div>
        </div>
    </div>
</template>

<script setup>
    import ServicesCard from './ServicesCard.vue';

    defineProps({
        services: {
            type: Array,
            required: true,
        },
        isLoading: {
            type: Boolean,
            default: false,
        },
        hasError: {
            type: Boolean,
            default: false,
        },
        serviceTarget: {
            type: Function,
            required: true,
        },
        formatIndex: {
            type: Function,
            required: true,
        },
    });

    defineEmits(['retry']);
</script>

<style scoped>
    .services-contact-section__services {
        flex: 1;
        width: 50%;
    }

    .services-gallery__wrapper {
        display: flex;
        flex-direction: column;
        gap: 40px;
    }

    .services-gallery__list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 40px 60px;
        padding-top: 0;
    }

    @media (max-width: 1024px) {
        .services-contact-section__services {
            width: 100%;
        }
    }

    @media (min-width: 1024px) {
        .services-gallery__list {
            grid-template-columns: repeat(2, 1fr);
            gap: 32px 40px;
        }
    }

    @media (min-width: 1280px) {
        .services-gallery__list {
            grid-template-columns: repeat(3, 1fr);
            gap: 48px 60px;
        }
    }

    @media (min-width: 1600px) {
        .services-gallery__list {
            grid-template-columns: repeat(3, 1fr);
            gap: 48px 80px;
        }
    }
</style>

