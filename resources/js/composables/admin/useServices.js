import { reactive, computed } from 'vue';
import axios from 'axios';

const state = reactive({
    initialized: false,
    loading: false,
    saving: false,
    services: [],
    meta: {
        current_page: 1,
        per_page: 15,
        total: 0,
        last_page: 1,
    },
    error: null,
});

const ensureFormData = (data) => {
    if (data instanceof FormData) {
        return data;
    }

    const formData = new FormData();

    Object.entries(data).forEach(([key, value]) => {
        if (value === undefined || value === null) {
            return;
        }

        if (Array.isArray(value)) {
            value.forEach((item) => formData.append(`${key}[]`, item));
            return;
        }

        if (value instanceof File) {
            formData.append(key, value, value.name);
            return;
        }

        formData.append(key, value);
    });

    return formData;
};

const handleError = (error) => {
    if (error.response) {
        if (import.meta.env.DEV) {
            console.error('[services][error][response]', error.response);
        }

        return error.response.data;
    }

    if (import.meta.env.DEV) {
        console.error('[services][error]', error);
    }

    return { message: 'Unexpected error' };
};

export function useAdminServices() {
    const fetchServices = async (params = {}) => {
        state.loading = true;
        state.error = null;

        try {
            const response = await axios.get('/api/admin/services', {
                params: {
                    page: params.page ?? state.meta.current_page ?? 1,
                },
            });

            state.services = response.data.data ?? [];

            if (response.data.meta) {
                state.meta = {
                    current_page: response.data.meta.current_page,
                    per_page: response.data.meta.per_page,
                    total: response.data.meta.total,
                    last_page: response.data.meta.last_page,
                };
            }

            state.initialized = true;
        } catch (error) {
            state.error = handleError(error);
        } finally {
            state.loading = false;
        }
    };

    const createService = async (payload) => {
        state.saving = true;
        state.error = null;

        try {
            const response = await axios.post('/api/admin/services', ensureFormData(payload));
            const service = response.data.data;
            state.services = [service, ...state.services];
            state.meta.total += 1;
            return service;
        } catch (error) {
            state.error = handleError(error);
            throw error;
        } finally {
            state.saving = false;
        }
    };

    const updateService = async (serviceId, payload) => {
        state.saving = true;
        state.error = null;

        try {
            const response = await axios.post(`/api/admin/services/${serviceId}`, ensureFormData({ ...payload, _method: 'PUT' }));
            const service = response.data.data;
            state.services = state.services.map((item) => (item.id === service.id ? service : item));
            return service;
        } catch (error) {
            state.error = handleError(error);
            throw error;
        } finally {
            state.saving = false;
        }
    };

    const deleteService = async (serviceId) => {
        state.saving = true;
        state.error = null;

        try {
            await axios.delete(`/api/admin/services/${serviceId}`);
            state.services = state.services.filter((item) => item.id !== serviceId);
            state.meta.total = Math.max(0, state.meta.total - 1);
        } catch (error) {
            state.error = handleError(error);
            throw error;
        } finally {
            state.saving = false;
        }
    };

    const services = computed(() => state.services);
    const pagination = computed(() => state.meta);
    const isLoading = computed(() => state.loading && !state.initialized);
    const isRefreshing = computed(() => state.loading && state.initialized);
    const isSaving = computed(() => state.saving);

    return {
        state,
        services,
        pagination,
        isLoading,
        isRefreshing,
        isSaving,
        fetchServices,
        createService,
        updateService,
        deleteService,
    };
}
