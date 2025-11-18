import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';

/**
 * Composable genérico para operaciones CRUD en el panel admin
 */
export function useAdminCRUD(options = {}) {
    const {
        fetchFn,
        createFn,
        updateFn,
        deleteFn,
        entityName = 'elemento',
        entityNamePlural = 'elementos',
        onSuccess,
        onError,
    } = options;

    const toast = useToast();
    const confirm = useConfirm();

    const isLoading = ref(false);
    const isRefreshing = ref(false);
    const items = ref([]);
    const pagination = ref({});
    const error = ref(null);

    const fetchItems = async (params = {}) => {
        if (!fetchFn) return;

        try {
            isLoading.value = true;
            error.value = null;
            const response = await fetchFn(params);
            items.value = response.data?.data ?? response.data ?? [];
            pagination.value = response.data?.meta ?? response.data?.pagination ?? {};
            return response;
        } catch (err) {
            console.error(`[admin][${entityName}][fetch][error]`, err);
            error.value = err;
            toast.add({
                severity: 'error',
                summary: 'Error al cargar',
                detail: err.response?.data?.message ?? `No se pudieron cargar los ${entityNamePlural}.`,
                life: 5000,
            });
            if (onError) onError(err);
            throw err;
        } finally {
            isLoading.value = false;
            isRefreshing.value = false;
        }
    };

    const refreshItems = async (params = {}) => {
        isRefreshing.value = true;
        return fetchItems(params);
    };

    const createItem = async (data) => {
        if (!createFn) return;

        try {
            const response = await createFn(data);
            toast.add({
                severity: 'success',
                summary: `${entityName} creado`,
                detail: `El ${entityName} se ha creado correctamente.`,
                life: 3000,
            });
            if (onSuccess) onSuccess('create', response);
            return response;
        } catch (err) {
            console.error(`[admin][${entityName}][create][error]`, err);
            toast.add({
                severity: 'error',
                summary: 'Error al crear',
                detail: err.response?.data?.message ?? `No se pudo crear el ${entityName}.`,
                life: 5000,
            });
            if (onError) onError(err);
            throw err;
        }
    };

    const updateItem = async (id, data) => {
        if (!updateFn) return;

        try {
            const response = await updateFn(id, data);
            toast.add({
                severity: 'success',
                summary: `${entityName} actualizado`,
                detail: `El ${entityName} se ha actualizado correctamente.`,
                life: 3000,
            });
            if (onSuccess) onSuccess('update', response);
            return response;
        } catch (err) {
            console.error(`[admin][${entityName}][update][error]`, err);
            toast.add({
                severity: 'error',
                summary: 'Error al actualizar',
                detail: err.response?.data?.message ?? `No se pudo actualizar el ${entityName}.`,
                life: 5000,
            });
            if (onError) onError(err);
            throw err;
        }
    };

    const deleteItem = async (id, itemName = null) => {
        if (!deleteFn) return;

        return new Promise((resolve, reject) => {
            confirm.require({
                message: `¿Eliminar definitivamente ${itemName ? `"${itemName}"` : `este ${entityName}`}?`,
                icon: 'pi pi-trash',
                rejectClass: 'essential-button essential-button--ghost',
                acceptClass: 'essential-button essential-button--danger',
                acceptLabel: 'Eliminar',
                rejectLabel: 'Cancelar',
                accept: async () => {
                    try {
                        const response = await deleteFn(id);
                        toast.add({
                            severity: 'success',
                            summary: `${entityName} eliminado`,
                            life: 3000,
                        });
                        if (onSuccess) onSuccess('delete', response);
                        resolve(response);
                    } catch (err) {
                        console.error(`[admin][${entityName}][delete][error]`, err);
                        toast.add({
                            severity: 'error',
                            summary: 'No se pudo eliminar',
                            detail: err.response?.data?.message ?? `Intenta nuevamente más tarde.`,
                            life: 5000,
                        });
                        if (onError) onError(err);
                        reject(err);
                    }
                },
                reject: () => {
                    reject(new Error('Cancelled'));
                },
            });
        });
    };

    return {
        isLoading,
        isRefreshing,
        items,
        pagination,
        error,
        fetchItems,
        refreshItems,
        createItem,
        updateItem,
        deleteItem,
    };
}

