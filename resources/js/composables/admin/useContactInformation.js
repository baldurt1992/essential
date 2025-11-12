import { reactive, computed } from 'vue';
import axios from 'axios';

const state = reactive({
    loading: false,
    saving: false,
    initialized: false,
    contact: null,
    error: null,
});

const ensurePayload = (data) => {
    if (!data) {
        return {};
    }

    return {
        email: data.email ?? null,
        phone: data.phone ?? null,
        whatsapp: data.whatsapp ?? null,
        location_line_one: data.location_line_one ?? null,
        location_line_two: data.location_line_two ?? null,
        support_hours: data.support_hours ?? null,
        contact_note: data.contact_note ?? null,
        social_links: Array.isArray(data.social_links)
            ? data.social_links
                .filter((link) => link.network && link.url)
                .map((link) => ({
                    network: link.network,
                    url: link.url,
                }))
            : [],
    };
};

export function useAdminContactInformation() {
    const fetchContactInformation = async () => {
        state.loading = true;
        state.error = null;

        try {
            const response = await axios.get('/api/admin/contact-information');
            const payload = response.data.data ?? {};
            state.contact = {
                ...payload,
                social_links: payload.social_links ?? [],
            };
            state.initialized = true;
        } catch (error) {
            state.error = error.response?.data ?? { message: 'Error al cargar la información de contacto.' };
            throw error;
        } finally {
            state.loading = false;
        }
    };

    const updateContactInformation = async (payload) => {
        state.saving = true;
        state.error = null;

        try {
            const response = await axios.put('/api/admin/contact-information', ensurePayload(payload));
            state.contact = response.data.data ?? {};
            return state.contact;
        } catch (error) {
            state.error = error.response?.data ?? { message: 'Error al guardar la información de contacto.' };
            throw error;
        } finally {
            state.saving = false;
        }
    };

    const contact = computed(() => state.contact ?? {});
    const isLoading = computed(() => state.loading && !state.initialized);
    const isRefreshing = computed(() => state.loading && state.initialized);
    const isSaving = computed(() => state.saving);

    const clearError = () => {
        state.error = null;
    };

    return {
        state,
        contact,
        isLoading,
        isRefreshing,
        isSaving,
        fetchContactInformation,
        updateContactInformation,
        clearError,
    };
}

