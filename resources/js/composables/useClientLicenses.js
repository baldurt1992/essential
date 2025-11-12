import { reactive, computed, readonly } from 'vue';
import axios from 'axios';

const state = reactive({
    licenses: [],
    loading: false,
    error: null,
});

const licenses = computed(() => state.licenses);
const activeLicenses = computed(() => 
    state.licenses.filter(license => license.can_download)
);
const isLoading = computed(() => state.loading);
const hasError = computed(() => !!state.error);

async function fetchLicenses() {
    state.loading = true;
    state.error = null;

    try {
        const response = await axios.get('/api/client/licenses');
        state.licenses = response.data.data || response.data;
    } catch (error) {
        state.error = error.response?.data?.message || 'Error al cargar licencias';
        console.error('[useClientLicenses] Error fetching licenses:', error);
    } finally {
        state.loading = false;
    }
}

export function useClientLicenses() {
    return {
        licenses: readonly(licenses),
        activeLicenses: readonly(activeLicenses),
        isLoading: readonly(isLoading),
        hasError: readonly(hasError),
        state: readonly(state),
        fetchLicenses,
    };
}

