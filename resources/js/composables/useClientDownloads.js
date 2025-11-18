/**
 * Composable para gestionar las descargas del usuario
 */
import { ref, computed } from 'vue';
import axios from 'axios';

const downloads = ref([]);
const loading = ref(false);
const error = ref(null);

export function useClientDownloads() {
    const fetchDownloads = async () => {
        loading.value = true;
        error.value = null;

        try {
            const response = await axios.get('/api/client/downloads');
            downloads.value = response.data.data || [];
        } catch (err) {
            error.value = err.response?.data?.message || 'Error al cargar las descargas';
            downloads.value = [];
        } finally {
            loading.value = false;
        }
    };

    const hasDownloads = computed(() => downloads.value.length > 0);

    return {
        downloads,
        loading,
        error,
        hasDownloads,
        fetchDownloads,
    };
}

