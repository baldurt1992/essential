<template>
    <section class="templates-collection">
        <div v-if="isLoading" class="essential-state">
            <span class="essential-spinner"></span>
            <p>Cargando plantillas…</p>
        </div>

        <div v-else-if="hasError" class="essential-state essential-state--error">
            <p>No pudimos cargar las plantillas. ¿Intentamos de nuevo?</p>
            <button type="button" class="essential-retry-button" @click="$emit('retry')">
                Reintentar
            </button>
        </div>

        <div v-else-if="!templates.length" class="essential-state">
            <p>No encontramos plantillas con los filtros seleccionados.</p>
            <button type="button" class="essential-retry-button" @click="$emit('clear-filters')">
                Limpiar filtros
            </button>
        </div>

        <div v-else class="templates-grid">
            <TemplateCard v-for="template in templates" :key="template.uuid" :template="template"
                :is-downloading="isDownloading" :downloading-template-id="downloadingTemplateId"
                :is-authenticated="isAuthenticated" @primary-action="$emit('primary-action', $event)"
                @preview="$emit('preview', $event)" @open-auth="$emit('open-auth')" />
        </div>

        <div v-if="showPaginator" class="templates-pagination">
            <Paginator :rows="pagination.perPage" :total-records="pagination.total"
                :first="(pagination.currentPage - 1) * pagination.perPage" @page="$emit('page-change', $event)" />
        </div>
    </section>
</template>

<script setup>
    import { computed } from 'vue';
    import TemplateCard from './TemplateCard.vue';
    import Paginator from 'primevue/paginator';

    const props = defineProps({
        templates: {
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
        pagination: {
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

    defineEmits(['retry', 'clear-filters', 'primary-action', 'preview', 'open-auth', 'page-change']);

    const showPaginator = computed(() => {
        return props.pagination.total > props.pagination.perPage;
    });
</script>

<style scoped>
    .templates-collection {
        max-width: 1320px;
        width: 100%;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 36px;
    }

    .templates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(248px, 248px));
        gap: clamp(20px, 4.5vw, 32px);
        padding: 0 clamp(10px, 2vw, 24px);
        justify-content: center;
        max-width: calc(5 * 248px + 4 * clamp(20px, 4.5vw, 32px));
        margin: 0 auto;
    }

    @media (max-width: 1400px) {
        .templates-grid {
            max-width: calc(4 * 248px + 3 * clamp(20px, 4.5vw, 32px));
        }
    }

    @media (max-width: 1120px) {
        .templates-grid {
            max-width: calc(3 * 248px + 2 * clamp(20px, 4.5vw, 32px));
        }
    }

    @media (max-width: 840px) {
        .templates-grid {
            max-width: calc(2 * 248px + 1 * clamp(20px, 4.5vw, 32px));
        }
    }

    @media (max-width: 560px) {
        .templates-grid {
            max-width: 248px;
        }
    }

    .templates-pagination {
        display: flex;
        justify-content: center;
    }
</style>
