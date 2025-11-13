import { reactive, computed, readonly } from 'vue';
import axios from 'axios';

const state = reactive({
    user: null,
    initialized: false,
    initializing: false,
    loading: false,
    error: null,
    validationErrors: {},
});

const isAuthenticated = computed(() => !!state.user);
const isAdmin = computed(() => state.user?.roles?.some((role) => role.name === 'admin') ?? false);
const isClient = computed(() => state.user?.roles?.some((role) => role.name === 'client') ?? false);

const user = computed(() => state.user);

let csrfInitialised = false;

async function ensureCsrf({ force } = {}) {
    if (!csrfInitialised || force) {
        await axios.get('/sanctum/csrf-cookie');
        csrfInitialised = true;
        debugCookies('after-csrf');
    }
}

function debugCookies(stage) {
    if (import.meta.env.DEV) {
        // eslint-disable-next-line no-console
        console.debug(`[auth][${stage}] cookies`, document.cookie);
    }
}

async function fetchUser(options = {}) {
    const { silent = false } = options;

    try {
        const response = await axios.get('/api/user', {
            validateStatus: (status) => (status >= 200 && status < 300) || status === 401,
            __silence401: silent,
        });

        if (response.status === 401) {
            state.user = null;

            return null;
        }

        state.user = response.data;

        return response.data;
    } catch (error) {
        state.user = null;

        if (!silent) {
            throw error;
        }

        return null;
    }
}

async function init() {
    if (state.initialized || state.initializing) {
        return;
    }

    state.initializing = true;
    try {
        await fetchUser({ silent: true });
    } finally {
        state.initialized = true;
        state.initializing = false;
    }
}

async function refreshUser(options = {}) {
    return fetchUser(options);
}

function resetErrors() {
    state.error = null;
    state.validationErrors = {};
}

function handleError(error) {
    if (error.response?.status === 422) {
        state.validationErrors = error.response.data.errors ?? {};
        state.error = error.response.data.message ?? 'Revisa los campos.';
    } else if (error.response?.data?.message) {
        state.error = error.response.data.message;
    } else if (error.message) {
        state.error = error.message;
    } else {
        state.error = 'Ocurrió un error inesperado. Inténtalo de nuevo.';
    }
}

async function withCsrfRequest(requestFn) {
    try {
        await ensureCsrf();
        debugCookies('before-request');
        const result = await requestFn();
        debugCookies('after-request');
        return result;
    } catch (error) {
        if (error.response?.status === 419) {
            csrfInitialised = false;
            await ensureCsrf({ force: true });
            debugCookies('retry-after-ensure');
            return requestFn();
        }

        throw error;
    }
}

async function login(payload) {
    state.loading = true;
    resetErrors();

    try {
        await withCsrfRequest(() => axios.post('/login', payload));
        await fetchUser();
        return true;
    } catch (error) {
        handleError(error);
        return false;
    } finally {
        state.loading = false;
    }
}

async function register(payload) {
    state.loading = true;
    resetErrors();

    try {
        const response = await withCsrfRequest(() => axios.post('/register', payload));
        // No iniciamos sesión automáticamente - el usuario debe verificar el OTP primero
        return {
            success: true,
            email: response.data?.email ?? payload.email,
        };
    } catch (error) {
        handleError(error);
        return {
            success: false,
            email: null,
        };
    } finally {
        state.loading = false;
    }
}

async function logout() {
    state.loading = true;
    resetErrors();

    try {
        await withCsrfRequest(() => axios.post('/logout'));
        state.user = null;
        csrfInitialised = false;
        return true;
    } catch (error) {
        handleError(error);
        return false;
    } finally {
        state.loading = false;
    }
}

function clearErrors() {
    resetErrors();
}

export function useAuth() {
    return {
        state: readonly(state),
        user,
        isAuthenticated,
        isAdmin,
        isClient,
        init,
        refreshUser,
        login,
        register,
        logout,
        clearErrors,
        loading: computed(() => state.loading),
        error: computed(() => state.error),
        validationErrors: computed(() => state.validationErrors),
    };
}
