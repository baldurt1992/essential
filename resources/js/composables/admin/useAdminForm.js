import { ref, computed } from 'vue';

/**
 * Composable genérico para manejo de formularios admin
 */
export function useAdminForm(initialForm = {}) {
    const form = ref({ ...initialForm });
    const backendErrors = ref({});
    const generalError = ref('');

    const fieldError = (field) => {
        return backendErrors.value?.[field]?.[0] ?? null;
    };

    const clearFieldError = (field) => {
        if (backendErrors.value?.[field]) {
            const next = { ...backendErrors.value };
            delete next[field];
            backendErrors.value = next;
        }
    };

    const clearErrors = () => {
        backendErrors.value = {};
        generalError.value = '';
    };

    const setBackendErrors = (errors = {}) => {
        backendErrors.value = errors;
    };

    const setGeneralError = (message = '') => {
        generalError.value = message;
    };

    const resetForm = (newInitialForm = null) => {
        form.value = { ...(newInitialForm ?? initialForm) };
        clearErrors();
    };

    return {
        form,
        backendErrors,
        generalError,
        fieldError,
        clearFieldError,
        clearErrors,
        setBackendErrors,
        setGeneralError,
        resetForm,
    };
}

