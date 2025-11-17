<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)" modal :header="template?.title ?? 'Plantilla'"
        class="template-preview-dialog" :style="{ width: dialogWidth }" :breakpoints="breakpoints">
        <div v-if="template" class="template-preview">
            <div class="template-preview__media">
                <Image :src="template.previewUrl" :alt="template.title" preview />
            </div>

            <div class="template-preview__content">
                <div class="template-preview__meta">
                    <span class="template-preview__price">
                        {{ formatPrice(template.price, template.currency) }}
                    </span>
                    <div class="template-preview__flags">
                        <span v-if="isTemplatePopular(template)"
                            class="essential-template-badge essential-template-badge--popular">Popular</span>
                        <span v-if="isTemplateFresh(template)"
                            class="essential-template-badge essential-template-badge--fresh">Nuevo</span>
                    </div>
                </div>

                <p class="template-preview__description">
                    {{ template.description }}
                </p>

                <div v-if="template.tags.length" class="template-preview__tags">
                    <span v-for="tag in template.tags" :key="tag" class="essential-template-tag">
                        {{ tag }}
                    </span>
                </div>

                <div class="template-preview__actions">
                    <button type="button" class="template-preview__buy"
                        :disabled="isDownloading && downloadingTemplateId === template.id"
                        @click="$emit('primary-action', template)">
                        <i v-if="isDownloading && downloadingTemplateId === template.id"
                            class="pi pi-spin pi-spinner template-preview__buy-spinner"></i>
                        <span v-else>{{ template.isAccessible ? 'Descargar' : 'Comprar ahora' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </Dialog>
</template>

<script setup>
import Dialog from 'primevue/dialog';
import Image from 'primevue/image';

const props = defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
    template: {
        type: Object,
        default: null,
    },
    dialogWidth: {
        type: String,
        default: '92vw',
    },
    breakpoints: {
        type: Object,
        default: () => ({
            '1280px': '720px',
            '960px': '92vw',
        }),
    },
    isDownloading: {
        type: Boolean,
        default: false,
    },
    downloadingTemplateId: {
        type: [Number, String, null],
        default: null,
    },
});

defineEmits(['primary-action', 'update:visible']);

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
.template-preview-dialog :deep(.p-dialog-content) {
    padding: 0;
    background: transparent;
}

.template-preview {
    display: flex;
    flex-direction: column;
    gap: 18px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px;
    overflow: hidden;
}

body.dark-mode .template-preview {
    background: rgba(17, 17, 17, 0.95);
}

.template-preview__media {
    width: 100%;
    background: rgba(23, 23, 23, 0.06);
}

.template-preview__media :deep(img) {
    width: 100%;
    height: auto;
    display: block;
}

.template-preview__content {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 20px 24px 24px;
}

.template-preview__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.template-preview__price {
    font-family: 'Space Mono', monospace;
    font-size: 22px;
    text-transform: uppercase;
    color: #dd3333;
}

.template-preview__flags {
    display: flex;
    gap: 8px;
}

.template-preview__description {
    font-family: 'Inter', sans-serif;
    font-size: 15px;
    line-height: 1.7;
    margin: 0;
    color: rgba(23, 23, 23, 0.8);
}

body.dark-mode .template-preview__description {
    color: rgba(243, 243, 243, 0.8);
}

.template-preview__tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.template-preview__actions {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-top: 10px;
}

.template-preview__buy {
    position: relative;
    border: none;
    background: #dd3333;
    color: #ffffff;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 12px 22px;
    border-radius: 999px;
    cursor: pointer;
    transition: background 0.2s ease, opacity 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.template-preview__buy:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
}

.template-preview__buy:hover:not(:disabled) {
    background: #c42b2b;
}

.template-preview__buy-spinner {
    margin-right: 8px;
}

@media (min-width: 768px) {
    .template-preview {
        flex-direction: row;
        gap: 0;
    }

    .template-preview__media {
        flex: 1 1 55%;
    }

    .template-preview__content {
        flex: 1 1 45%;
    }

    .template-preview__actions {
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
    }
}
</style>

