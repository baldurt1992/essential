import { computed, reactive } from 'vue';
import axios from 'axios';

const state = reactive({
    collection: [],
    loading: false,
    error: null,
    initialized: false,
    lastQueryKey: null,
});

const mapService = (service) => ({
    uuid: service.uuid,
    title: service.title,
    slug: service.slug,
    description: service.description,
    linkUrl: service.link_url,
    imageUrl: service.image_url,
    isPopular: service.is_popular,
    sortOrder: service.sort_order,
    metadata: service.metadata ?? {},
});

export const useSiteServices = () => {
    const services = computed(() => state.collection);
    const isLoading = computed(() => state.loading);
    const error = computed(() => state.error);

    const fetchServices = async (options = {}) => {
        const query = {
            popular: options.popular ?? false,
        };

        const queryKey = JSON.stringify(query);

        if (state.initialized && state.lastQueryKey === queryKey && !options.force) {
            return;
        }

        state.loading = true;
        state.error = null;

        try {
            const response = await axios.get('/api/services', {
                params: query.popular ? { popular: true } : {},
            });

            const payload = Array.isArray(response.data?.data) ? response.data.data : [];
            state.collection = payload.map(mapService);
            state.initialized = true;
            state.lastQueryKey = queryKey;
        } catch (err) {
            console.error('[useSiteServices] Error fetching services', err);
            state.error = err;
        } finally {
            state.loading = false;
        }
    };

    const clearError = () => {
        state.error = null;
    };

    return {
        services,
        isLoading,
        error,
        fetchServices,
        clearError,
    };
};

