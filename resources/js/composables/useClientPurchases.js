import { reactive, computed, readonly } from 'vue';
import axios from 'axios';

const state = reactive({
    purchases: [],
    loading: false,
    error: null,
});

const purchases = computed(() => state.purchases);
const isLoading = computed(() => state.loading);
const hasError = computed(() => !!state.error);

async function fetchPurchases() {
    state.loading = true;
    state.error = null;

    try {
        const response = await axios.get('/api/client/purchases');
        state.purchases = response.data.data || response.data;
    } catch (error) {
        state.error = error.response?.data?.message || 'Error al cargar compras';
        console.error('[useClientPurchases] Error fetching purchases:', error);
    } finally {
        state.loading = false;
    }
}

async function resendPurchaseCode(purchaseUuid) {
    try {
        await axios.post(`/api/client/purchases/${purchaseUuid}/resend-code`);
        return { success: true };
    } catch (error) {
        const message = error.response?.data?.message || 'Error al reenviar el código';
        console.error('[useClientPurchases] Error resending code:', error);
        return { success: false, error: message };
    }
}

async function validatePurchaseCode(purchaseUuid, code) {
    try {
        const response = await axios.post(`/api/client/purchases/${purchaseUuid}/validate-code`, {
            code,
        });
        return {
            success: true,
            download_url: response.data.download_url,
        };
    } catch (error) {
        const message = error.response?.data?.message || 'Error al validar el código';
        console.error('[useClientPurchases] Error validating code:', error);
        return { success: false, error: message };
    }
}

export function useClientPurchases() {
    return {
        purchases: readonly(purchases),
        isLoading: readonly(isLoading),
        hasError: readonly(hasError),
        state: readonly(state),
        fetchPurchases,
        resendPurchaseCode,
        validatePurchaseCode,
    };
}

