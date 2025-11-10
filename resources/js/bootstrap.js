import axios from 'axios';
window.axios = axios;

window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
window.axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';

window.axios.interceptors.request.use((config) => {
    if (import.meta.env.DEV) {
        // eslint-disable-next-line no-console
        console.debug('[axios][request]', {
            method: config.method,
            url: config.url,
            headers: config.headers,
            data: config.data,
        });
    }

    return config;
}, (error) => {
    if (import.meta.env.DEV) {
        // eslint-disable-next-line no-console
        console.error('[axios][request][error]', error);
    }

    return Promise.reject(error);
});

window.axios.interceptors.response.use((response) => {
    if (import.meta.env.DEV) {
        // eslint-disable-next-line no-console
        console.debug('[axios][response]', {
            status: response.status,
            url: response.config?.url,
            headers: response.headers,
            data: response.data,
        });
    }

    return response;
}, (error) => {
    const status = error.response?.status;
    const { url, __silence401 } = error.config ?? {};

    const shouldLog = !(
        status === 401 &&
        __silence401 === true
    );

    if (shouldLog && import.meta.env.DEV) {
        // eslint-disable-next-line no-console
        console.error('[axios][response][error]', {
            status,
            url,
            headers: error.response?.headers,
            data: error.response?.data,
        });
    }

    return Promise.reject(error);
});
