<template>
    <article class="template-card">
        <div class="template-card__media">
            <Image :src="template.previewUrl" :alt="template.title" class="template-card__image" preview />

            <div class="template-card__badges">
                <span v-if="isTemplatePopular(template)" class="essential-template-badge essential-template-badge--popular">
                    Popular
                </span>
                <span v-if="isTemplateFresh(template)" class="essential-template-badge essential-template-badge--fresh">
                    Nuevo
                </span>
            </div>

            <div class="template-card__overlay">
                <div class="template-card__body">
                    <div class="template-card__header">
                        <h2 class="template-card__title" :title="template.title">
                            {{ template.title }}
                        </h2>
                        <p class="template-card__price">
                            <span>{{ formatPrice(template.price, template.currency) }}</span>
                        </p>
                    </div>

                    <div class="template-card__tags" v-if="template.tags.length">
                        <span v-for="tag in template.tags" :key="tag" class="essential-template-tag">
                            {{ tag }}
                        </span>
                    </div>

                    <div class="template-card__actions">
                        <button type="button" class="essential-template-cta"
                            :disabled="isDownloading && downloadingTemplateId === template.id"
                            @click="$emit('primary-action', template)">
                            <i v-if="isDownloading && downloadingTemplateId === template.id"
                                class="pi pi-spin pi-spinner"></i>
                            <span v-else>{{ template.isAccessible ? 'Descargar' : 'Comprar' }}</span>
                        </button>
                        <button v-if="!isAuthenticated" type="button" class="essential-template-link"
                            @click="$emit('open-auth')">
                            Iniciar sesión
                            <i class="pi pi-arrow-right essential-template-link-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="button" class="template-card__preview" @click="$emit('preview', template)">
                    <i class="pi pi-eye"></i>
                    <span>Ver detalle</span>
                </button>
            </div>
        </div>
    </article>
</template>

<script setup>
import Image from 'primevue/image';

const props = defineProps({
    template: {
        type: Object,
        required: true,
    },
    isDownloading: {
        type: Boolean,
        default: false,
    },
    downloadingTemplateId: {
        type: [Number, String, null],
        default: null,
    },
    isAuthenticated: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['primary-action', 'preview', 'open-auth']);

const formatPrice = (price, currency = 'USD') => {
    if (price == null) {
        return '—';
    }
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency,
        minimumFractionDigits: 0,
    }).format(price);
};

const isTemplatePopular = (template) => {
    if (typeof template.isPopular === 'boolean') {
        return template.isPopular;
    }

    const flags = template.metadata?.flags ?? {};
    return !!(flags.popular ?? flags.is_popular ?? template.metadata?.is_popular);
};

const isTemplateFresh = (template) => {
    if (typeof template.isNew === 'boolean') {
        return template.isNew;
    }

    const flags = template.metadata?.flags ?? {};
    return !!(flags.is_new ?? template.metadata?.is_new);
};
</script>

<style scoped>
.template-card {
    background: rgba(255, 255, 255, 0.94);
    border: 1px solid rgba(23, 23, 23, 0.08);
    border-radius: 18px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    width: 248px;
    height: 441px;
    box-shadow: 0 18px 32px rgba(0, 0, 0, 0.06);
}

body.dark-mode .template-card {
    background: rgba(17, 17, 17, 0.9);
    border-color: rgba(255, 255, 255, 0.06);
    box-shadow: 0 20px 35px rgba(0, 0, 0, 0.4);
}

.template-card__media {
    position: relative;
    overflow: hidden;
    width: 100%;
    height: 100%;
    flex-shrink: 0;
}

.template-card__image {
    width: 100%;
    height: 100%;
}

.template-card__image :deep(img) {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.35s ease;
}

.template-card__media:hover .template-card__image :deep(img) {
    transform: scale(1.04);
}

.template-card__badges {
    position: absolute;
    top: 16px;
    left: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.template-card__overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.6) 30%, rgba(0, 0, 0, 0.95) 100%);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 16px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    pointer-events: none;
}

.template-card:hover .template-card__overlay {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

/* En móvil, mostrar el overlay siempre para que sea accesible */
@media (max-width: 767px) {
    .template-card__overlay {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
}

.template-card__body {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
    overflow: hidden;
    min-height: 0;
}

.template-card__preview {
    align-self: flex-end;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: none;
    background: rgba(23, 23, 23, 0.85);
    color: #ffffff;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 9px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 6px 12px;
    border-radius: 999px;
    cursor: pointer;
    transition: background 0.2s ease;
    flex-shrink: 0;
}

.template-card__preview:hover {
    background: #dd3333;
}

.template-card__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
}

.template-card__title {
    font-family: 'Space Mono', monospace;
    font-size: 14px;
    text-transform: uppercase;
    margin: 0;
    line-height: 1.3;
    line-clamp: 2;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
    color: #ffffff;
    transition: color 0.3s ease;
}

.template-card__price {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin: 0;
    color: #ff6b6b;
    white-space: nowrap;
    transition: color 0.3s ease;
}

.template-card__tags {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    flex-shrink: 0;
}

/* Asegurar que los tags dentro del overlay siempre tengan el mismo color */
.template-card__overlay .essential-template-tag {
    background: rgba(255, 255, 255, 0.15) !important;
    color: rgba(255, 255, 255, 0.9) !important;
}

.template-card__actions {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-shrink: 0;
    padding-top: 8px;
}

@media (min-width: 768px) {
    .template-card__actions {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}
</style>

