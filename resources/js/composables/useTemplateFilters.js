import { computed } from 'vue';
import { useSiteTemplates } from './useSiteTemplates';

/**
 * Composable para manejar la lógica de filtros de plantillas
 */
export function useTemplateFilters() {
    const siteTemplates = useSiteTemplates();

    const filters = computed(() => siteTemplates.filters.value);
    const templates = computed(() => siteTemplates.templates.value);

    const normalizeCategory = (category) => {
        if (typeof category === 'string') {
            const trimmed = category.trim();
            return trimmed.length ? trimmed : null;
        }
        return category ?? null;
    };

    const categories = computed(() => {
        const set = new Set();
        templates.value.forEach((template) => {
            const templateCategories = template.metadata?.categories ?? template.tags ?? [];
            templateCategories.forEach((category) => {
                const normalized = normalizeCategory(category);
                if (normalized) {
                    set.add(normalized);
                }
            });
        });
        const activeCategory = normalizeCategory(filters.value.category);
        if (activeCategory && !set.has(activeCategory)) {
            set.add(activeCategory);
        }
        const sortedCategories = Array.from(set).sort((a, b) => a.localeCompare(b, 'es'));
        return [
            { label: 'Todos', value: null },
            ...sortedCategories.map((value) => ({ label: value, value })),
        ];
    });

    return {
        filters,
        categories,
        normalizeCategory,
    };
}

