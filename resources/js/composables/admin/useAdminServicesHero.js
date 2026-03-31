import { reactive, computed } from 'vue';
import axios from 'axios';

const state = reactive({
    loading: false,
    saving: false,
    hero: null,
    error: null,
});

/**
 * Admin: configuración del video de cabecera de /servicios.
 */
export function useAdminServicesHero() {
    const fetchHero = async () => {
        state.loading = true;
        state.error = null;
        try {
            const response = await axios.get('/api/admin/services-hero');
            state.hero = response.data?.data ?? response.data;
        } catch (error) {
            state.error = error.response?.data ?? { message: 'No se pudo cargar la configuración del video.' };
            throw error;
        } finally {
            state.loading = false;
        }
    };

    const uploadVideo = async (file) => {
        state.saving = true;
        state.error = null;
        try {
            await axios.get('/sanctum/csrf-cookie');
            const formData = new FormData();
            formData.append('video', file);
            const response = await axios.post('/api/admin/services-hero/video', formData);
            state.hero = response.data?.data ?? response.data;
            return state.hero;
        } catch (error) {
            state.error = error.response?.data ?? { message: 'No se pudo subir el video.' };
            throw error;
        } finally {
            state.saving = false;
        }
    };

    return {
        hero: computed(() => state.hero),
        isLoading: computed(() => state.loading),
        isSaving: computed(() => state.saving),
        error: computed(() => state.error),
        fetchHero,
        uploadVideo,
        clearError: () => {
            state.error = null;
        },
    };
}
