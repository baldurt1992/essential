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
    state.loading = true;
    state.error = null;
    
    try {
        const response = await axios.post(`/api/client/subscriptions/${subscriptionUuid}/cancel`);
        
        // Update the subscription in the local state with the response data
        const updatedSubscription = response.data.data;
        const index = state.subscriptions.findIndex(sub => sub.uuid === subscriptionUuid);
        if (index !== -1 && updatedSubscription) {
            state.subscriptions[index] = updatedSubscription;
        } else {
            // If not found, refresh the entire list
            await fetchSubscriptions();
        }
        
        return { success: true };
    } catch (error) {
        const message = error.response?.data?.message || 'Error al cancelar la suscripción';
        state.error = message;
        console.error('[useClientSubscriptions] Error canceling subscription:', error);
        return { success: false, error: message };
    } finally {
        state.loading = false;
    }
}

async function reactivateSubscription(subscriptionUuid) {
    state.loading = true;
    state.error = null;
    
    try {
        const response = await axios.post(`/api/client/subscriptions/${subscriptionUuid}/reactivate`);
        
        // Update the subscription in the local state with the response data
        const updatedSubscription = response.data.data;
        const index = state.subscriptions.findIndex(sub => sub.uuid === subscriptionUuid);
        if (index !== -1 && updatedSubscription) {
            state.subscriptions[index] = updatedSubscription;
        } else {
            // If not found, refresh the entire list
            await fetchSubscriptions();
        }
        
        return { success: true };
    } catch (error) {
        const message = error.response?.data?.message || 'Error al reactivar la suscripción';
        state.error = message;
        console.error('[useClientSubscriptions] Error reactivating subscription:', error);
        return { success: false, error: message };
    } finally {
        state.loading = false;
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
        reactivateSubscription,
    };
}

