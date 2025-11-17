<template>
    <section class="templates-hero">
        <div class="templates-hero__intro">
            <div class="essential-eyebrow">
                <span class="essential-eyebrow__dot"></span>
                <span class="essential-eyebrow__label">Catálogo creativo</span>
            </div>
            <h1 class="templates-hero__title">
                Plantillas listas<br />
                para convertir
            </h1>
            <p class="templates-hero__description">
                Descarga recursos profesionales, optimizados para campañas, eventos y redes. Pensados para
                diseñadores
                que necesitan velocidad sin ceder calidad.
            </p>
        </div>

        <div class="templates-filters">
            <div class="templates-filters__group templates-filters__group--primary">
                <button v-for="category in categories" :key="category.value ?? 'all'" type="button"
                    class="essential-filter-chip"
                    :class="{ 'essential-filter-chip--active': filters.category === category.value }"
                    @click="$emit('toggle-category', category.value)">
                    {{ category.label }}
                </button>
            </div>

            <div class="templates-filters__group templates-filters__group--secondary">
                <button type="button" class="essential-filter-toggle"
                    :class="{ 'essential-filter-toggle--active': filters.popular }"
                    @click="$emit('toggle-flag', 'popular')">
                    <span class="essential-filter-toggle__indicator"></span>
                    <span>Populares</span>
                </button>
                <button type="button" class="essential-filter-toggle"
                    :class="{ 'essential-filter-toggle--active': filters.fresh }"
                    @click="$emit('toggle-flag', 'fresh')">
                    <span class="essential-filter-toggle__indicator"></span>
                    <span>Nuevas</span>
                </button>
            </div>
        </div>
    </section>
</template>

<script setup>
    defineProps({
        categories: {
            type: Array,
            required: true,
        },
        filters: {
            type: Object,
            required: true,
        },
    });

    defineEmits(['toggle-category', 'toggle-flag']);
</script>

<style scoped>
    .templates-hero {
        display: flex;
        flex-direction: column;
        gap: 28px;
        max-width: var(--templates-max-width, 1200px);
        width: 100%;
    }

    .templates-hero__intro {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .templates-hero__title {
        font-family: 'Lexend', sans-serif;
        font-size: clamp(32px, 8vw, 52px);
        font-weight: 400;
        line-height: 1.05;
        letter-spacing: -0.01em;
        text-transform: uppercase;
        margin: 0;
        color: var(--essential-heading-color);
    }

    .templates-hero__description {
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        line-height: 1.6;
        margin: 0;
        color: rgba(23, 23, 23, 0.78);
    }

    body.dark-mode .templates-hero__description {
        color: rgba(243, 243, 243, 0.78);
    }

    .templates-filters {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .templates-filters__group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
</style>
