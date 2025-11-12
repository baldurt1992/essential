import { reactive, computed, readonly } from 'vue';
import axios from 'axios';

const state = reactive({
    subscriptions: [],
    loading: false,
    error: null,
});

const subscriptions = computed(() => state.subscriptions);
const activeSubscriptions = computed(() => 
    state.subscriptions.filter(sub => sub.is_active)
);
const isLoading = computed(() => state.loading);
const hasError = computed(() => !!state.error);

async function fetchSubscriptions() {
    state.loading = true;
    state.error = null;

    try {
        const response = await axios.get('/api/client/subscriptions');
        state.subscriptions = response.data.data || response.data;
    } catch (error) {
        state.error = error.response?.data?.message || 'Error al cargar suscripciones';
        console.error('[useClientSubscriptions] Error fetching subscriptions:', error);
    } finally {
        state.loading = false;
    }
}

async function cancelSubscription(subscriptionUuid) {
    try {
        await axios.post(`/api/client/subscriptions/${subscriptionUuid}/cancel`);
        await fetchSubscriptions(); // Refresh list
        return { success: true };
    } catch (error) {
        const message = error.response?.data?.message || 'Error al cancelar la suscripción';
        console.error('[useClientSubscriptions] Error canceling subscription:', error);
        return { success: false, error: message };
    }
}

export function useClientSubscriptions() {
    return {
        subscriptions: readonly(subscriptions),
        activeSubscriptions: readonly(activeSubscriptions),
        isLoading: readonly(isLoading),
        hasError: readonly(hasError),
        state: readonly(state),
        fetchSubscriptions,
        cancelSubscription,
    };
}

