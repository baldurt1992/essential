import { computed, reactive } from 'vue';
import axios from 'axios';

const state = reactive({
    collection: [],
    pagination: {
        currentPage: 1,
        perPage: 12,
        total: 0,
        lastPage: 1,
    },
    filters: {
        category: null,
        popular: false,
        fresh: false,
    },
    loading: false,
    error: null,
    initialized: false,
    lastQueryKey: null,
});

const mapTemplate = (template) => {
    const metadata = template.metadata ?? {};
    const flags = metadata.flags ?? {};

    return {
        uuid: template.uuid,
        slug: template.slug,
        title: template.title,
        description: template.description,
        price: template.price,
        currency: template.currency ?? 'USD',
        previewUrl: template.preview_url,
        tags: template.tags ?? [],
        metadata,
        isPopular: template.is_popular ?? flags.popular ?? metadata.is_popular ?? false,
        isNew: template.is_new ?? flags.is_new ?? metadata.is_new ?? false,
        isAccessible: template.is_accessible ?? false,
        createdAt: template.created_at,
    };
};

export const useSiteTemplates = () => {
    const templates = computed(() => state.collection);
    const isLoading = computed(() => state.loading);
    const error = computed(() => state.error);
    const pagination = computed(() => state.pagination);
    const filters = computed(() => state.filters);

    const buildQuery = (overrides = {}) => {
        const resolve = (key, fallback) => {
            if (Object.prototype.hasOwnProperty.call(overrides, key)) {
                return overrides[key];
            }
            return fallback;
        };

        return {
            page: resolve('page', state.pagination.currentPage),
            per_page: resolve('perPage', state.pagination.perPage),
            category: resolve('category', state.filters.category),
            popular: resolve('popular', state.filters.popular),
            fresh: resolve('fresh', state.filters.fresh),
        };
    };

    const fetchTemplates = async (overrides = {}) => {
        const query = buildQuery(overrides);
        const queryKey = JSON.stringify(query);

        if (state.initialized && state.lastQueryKey === queryKey && !overrides.force) {
            return;
        }

        state.loading = true;
        state.error = null;

        try {
            const response = await axios.get('/api/templates', {
                params: {
                    page: query.page,
                    per_page: query.per_page,
                    ...(query.category !== null && query.category !== undefined && query.category !== ''
                        ? { category: query.category }
                        : {}),
                    ...(query.popular ? { popular: true } : {}),
                    ...(query.fresh ? { fresh: true } : {}),
                },
            });

            const payload = response.data?.data ?? [];
            state.collection = payload.map(mapTemplate);
            state.pagination = {
                currentPage: response.data?.meta?.current_page ?? query.page,
                perPage: response.data?.meta?.per_page ?? query.per_page,
                total: response.data?.meta?.total ?? payload.length,
                lastPage: response.data?.meta?.last_page ?? query.page,
            };

            state.filters = {
                category: query.category ?? null,
                popular: !!query.popular,
                fresh: !!query.fresh,
            };

            state.initialized = true;
            state.lastQueryKey = queryKey;
        } catch (err) {
            console.error('[useSiteTemplates] Error fetching templates', err);
            state.error = err;
        } finally {
            state.loading = false;
        }
    };

    const setFilters = async (newFilters = {}) => {
        state.filters = {
            ...state.filters,
            ...newFilters,
        };

        await fetchTemplates({
            page: 1,
            category: state.filters.category,
            popular: state.filters.popular,
            fresh: state.filters.fresh,
            force: true,
        });
    };

    const setPage = async (page) => {
        await fetchTemplates({
            page,
            force: true,
        });
    };

    const clearError = () => {
        state.error = null;
    };

    return {
        templates,
        isLoading,
        error,
        pagination,
        filters,
        fetchTemplates,
        setFilters,
        setPage,
        clearError,
    };
};

