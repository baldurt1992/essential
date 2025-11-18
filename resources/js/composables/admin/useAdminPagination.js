import { ref, computed } from 'vue';

/**
 * Composable para manejar paginación en el panel admin
 */
export function useAdminPagination(fetchFn) {
    const first = ref(0);

    const handlePageChange = async (event) => {
        first.value = event.first;
        if (fetchFn) {
            await fetchFn({ page: event.page + 1 });
        }
    };

    const resetPagination = () => {
        first.value = 0;
    };

    return {
        first,
        handlePageChange,
        resetPagination,
    };
}

