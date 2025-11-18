import { reactive, computed } from 'vue';
import { useAuth } from './useAuth';

/**
 * Composable para manejar el estado de los formularios de autenticación (login y register)
 * Incluye: estado de formularios, errores, reset, validación
 */
export function useAuthForms() {
    const auth = useAuth();

    const loginForm = reactive({
        email: '',
        password: '',
    });

    const registerForm = reactive({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const loading = computed(() => auth.loading.value);
    const generalError = computed(() => auth.error.value);

    const loginErrors = computed(() => ({
        email: auth.validationErrors.value.email?.[0] ?? null,
        password: auth.validationErrors.value.password?.[0] ?? null,
    }));

    const registerErrors = computed(() => ({
        name: auth.validationErrors.value.name?.[0] ?? null,
        email: auth.validationErrors.value.email?.[0] ?? null,
        password: auth.validationErrors.value.password?.[0] ?? null,
        password_confirmation: auth.validationErrors.value.password_confirmation?.[0] ?? null,
    }));

    /**
     * Resetea ambos formularios
     */
    const resetForms = () => {
        loginForm.email = '';
        loginForm.password = '';
        registerForm.name = '';
        registerForm.email = '';
        registerForm.password = '';
        registerForm.password_confirmation = '';
    };

    /**
     * Limpia los errores del auth store
     */
    const clearErrors = () => {
        auth.clearErrors();
    };

    return {
        // State
        loginForm,
        registerForm,
        loading,
        generalError,
        loginErrors,
        registerErrors,

        // Actions
        resetForms,
        clearErrors,
    };
}

