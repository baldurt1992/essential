import { reactive, computed } from 'vue';
import axios from 'axios';

const state = reactive({
    initialized: false,
    loading: false,
    saving: false,
    plans: [],
    meta: {
        current_page: 1,
        per_page: 15,
        total: 0,
        last_page: 1,
    },
    error: null,
});

const handleError = (error) => {
    if (error.response) {
        if (import.meta.env.DEV) {
            console.error('[plans][error][response]', error.response);
        }

        return error.response.data;
    }

    if (import.meta.env.DEV) {
        console.error('[plans][error]', error);
    }

    return { message: 'Unexpected error' };
};

export function useAdminPlans() {
    const fetchPlans = async (params = {}) => {
        state.loading = true;
        state.error = null;

        try {
            const response = await axios.get('/api/admin/plans', {
                params: {
                    page: params.page ?? state.meta.current_page ?? 1,
                },
            });

            state.plans = response.data.data ?? [];

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

    const createPlan = async (payload) => {
        state.saving = true;
        state.error = null;

        try {
            const response = await axios.post('/api/admin/plans', payload);
            const plan = response.data.data;
            state.plans = [plan, ...state.plans];
            state.meta.total += 1;
            return plan;
        } catch (error) {
            state.error = handleError(error);
            throw error;
        } finally {
            state.saving = false;
        }
    };

    const updatePlan = async (planUuid, payload) => {
        state.saving = true;
        state.error = null;

        try {
            const response = await axios.put(`/api/admin/plans/${planUuid}`, payload);
            const plan = response.data.data;
            state.plans = state.plans.map((item) => (item.uuid === plan.uuid ? plan : item));
            return plan;
        } catch (error) {
            state.error = handleError(error);
            throw error;
        } finally {
            state.saving = false;
        }
    };

    const deletePlan = async (planUuid) => {
        state.saving = true;
        state.error = null;

        try {
            await axios.delete(`/api/admin/plans/${planUuid}`);
            state.plans = state.plans.filter((item) => item.uuid !== planUuid);
            state.meta.total = Math.max(0, state.meta.total - 1);
        } catch (error) {
            state.error = handleError(error);
            throw error;
        } finally {
            state.saving = false;
        }
    };

    const plans = computed(() => state.plans);
    const pagination = computed(() => state.meta);
    const isLoading = computed(() => state.loading && !state.initialized);
    const isRefreshing = computed(() => state.loading && state.initialized);
    const isSaving = computed(() => state.saving);

    return {
        state,
        plans,
        pagination,
        isLoading,
        isRefreshing,
        isSaving,
        fetchPlans,
        createPlan,
        updatePlan,
        deletePlan,
    };
}
