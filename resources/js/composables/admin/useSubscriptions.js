import { reactive, computed } from 'vue';
import axios from 'axios';

const state = reactive({
    initialized: false,
    loading: false,
    saving: false,
    subscriptions: [],
    meta: {
        current_page: 1,
        per_page: 20,
        total: 0,
        last_page: 1,
    },
    error: null,
});

const handleError = (error) => {
    if (error.response) {
        if (import.meta.env.DEV) {
            console.error('[subscriptions][error][response]', error.response);
        }

        return error.response.data;
    }

    if (import.meta.env.DEV) {
        console.error('[subscriptions][error]', error);
    }

    return { message: 'Unexpected error' };
};

export function useAdminSubscriptions() {
    const fetchSubscriptions = async (params = {}) => {
        state.loading = true;
        state.error = null;

        try {
            const response = await axios.get('/api/admin/subscriptions', {
                params: {
                    page: params.page ?? state.meta.current_page ?? 1,
                },
            });

            state.subscriptions = response.data.data ?? [];

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

    const cancelSubscription = async (subscriptionId) => {
        state.saving = true;
        state.error = null;

        try {
            const response = await axios.post(`/api/admin/subscriptions/${subscriptionId}/cancel`);
            const subscription = response.data.data;
            state.subscriptions = state.subscriptions.map((item) => (item.id === subscription.id ? subscription : item));
            return subscription;
        } catch (error) {
            state.error = handleError(error);
            throw error;
        } finally {
            state.saving = false;
        }
    };

    const subscriptions = computed(() => state.subscriptions);
    const pagination = computed(() => state.meta);
    const isLoading = computed(() => state.loading && !state.initialized);
    const isRefreshing = computed(() => state.loading && state.initialized);
    const isSaving = computed(() => state.saving);

    return {
        state,
        subscriptions,
        pagination,
        isLoading,
        isRefreshing,
        isSaving,
        fetchSubscriptions,
        cancelSubscription,
    };
}
