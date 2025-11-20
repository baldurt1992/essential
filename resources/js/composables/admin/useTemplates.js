import { reactive, computed } from 'vue';
import axios from 'axios';

const state = reactive({
    initialized: false,
    loading: false,
    saving: false,
    templates: [],
    meta: {
        current_page: 1,
        per_page: 15,
        total: 0,
        last_page: 1,
    },
    error: null,
});

const ensureFormData = (data) => {
    if (data instanceof FormData) {
        return data;
    }

    const formData = new FormData();

    Object.entries(data).forEach(([key, value]) => {
        if (value === undefined || value === null) {
            return;
        }

        if (Array.isArray(value)) {
            value.forEach((item) => formData.append(`${key}[]`, item));
            return;
        }

        if (value instanceof File) {
            formData.append(key, value, value.name);
            return;
        }

        formData.append(key, value);
    });

    return formData;
};

const handleError = (error) => {
    if (error.response) {
        console.error('[templates][error][response]', error.response);
        return error.response.data;
    }

    console.error('[templates][error]', error);
    return { message: 'Unexpected error' };
};

export function useAdminTemplates() {
    const fetchTemplates = async (params = {}) => {
        state.loading = true;
        state.error = null;

        try {
            const response = await axios.get('/api/admin/templates', {
                params: {
                    page: params.page ?? state.meta.current_page ?? 1,
                },
            });

            state.templates = response.data.data ?? [];
            if (response.data.meta) {
                state.meta = {
                    current_page: response.data.meta.current_page,
                    per_page: response.data.meta.per_page,
                    total: response.data.meta.total,
                    last_page: response.data.meta.last_page,
                };
            }

            state.initialized = true;
        } catch (error) {
            state.error = handleError(error);
        } finally {
            state.loading = false;
        }
    };

    const createTemplate = async (payload, onProgressCallback = null) => {
        state.saving = true;
        state.error = null;

        try {
            const formData = ensureFormData(payload);

            // Configurar timeout largo para archivos grandes (hasta 150MB)
            const response = await axios.post('/api/admin/templates', formData, {
                timeout: 600000, // 10 minutos para archivos grandes
                maxContentLength: Infinity,
                maxBodyLength: Infinity,
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                onUploadProgress: (progressEvent) => {
                    if (progressEvent.total) {
                        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);

                        if (onProgressCallback) {
                            onProgressCallback(percentCompleted);
                        }

                        if (import.meta.env.DEV) {
                            console.debug('[useAdminTemplates] Upload progress', {
                                percent: percentCompleted,
                                loaded: progressEvent.loaded,
                                total: progressEvent.total,
                                loadedMB: (progressEvent.loaded / (1024 * 1024)).toFixed(2),
                                totalMB: (progressEvent.total / (1024 * 1024)).toFixed(2),
                            });
                        }
                    }
                },
            });

            const template = response.data.data;
            state.templates = [template, ...state.templates];
            state.meta.total += 1;
            return template;
        } catch (error) {
            state.error = handleError(error);
            throw error;
        } finally {
            state.saving = false;
        }
    };

    const updateTemplate = async (templateId, payload, onProgressCallback = null) => {
        state.saving = true;
        state.error = null;

        try {
            // Si payload ya es FormData, usarlo directamente; si no, convertirlo
            const formData = payload instanceof FormData ? payload : ensureFormData(payload);

            // Agregar _method solo si no existe
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }

            // Logs para debugging
            if (import.meta.env.DEV) {
                const packageFile = formData.get('package_file');
                console.debug('[useAdminTemplates] updateTemplate', {
                    templateId,
                    isFormData: payload instanceof FormData,
                    hasPackageFile: formData.has('package_file'),
                    hasPreviewImage: formData.has('preview_image'),
                    formDataKeys: Array.from(formData.keys()),
                    packageFileInfo: packageFile ? {
                        name: packageFile.name,
                        size: packageFile.size,
                        sizeMB: (packageFile.size / (1024 * 1024)).toFixed(2),
                        type: packageFile.type,
                    } : null,
                });
            }

            // Configurar timeout largo para archivos grandes (hasta 150MB)
            // Para archivos de 121MB, necesitamos más tiempo (aprox 10 minutos)
            const response = await axios.post(`/api/admin/templates/${templateId}`, formData, {
                timeout: 600000, // 10 minutos para archivos grandes
                maxContentLength: Infinity,
                maxBodyLength: Infinity,
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                onUploadProgress: (progressEvent) => {
                    if (progressEvent.total) {
                        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);

                        if (onProgressCallback) {
                            onProgressCallback(percentCompleted);
                        }

                        if (import.meta.env.DEV) {
                            console.debug('[useAdminTemplates] Upload progress', {
                                percent: percentCompleted,
                                loaded: progressEvent.loaded,
                                total: progressEvent.total,
                                loadedMB: (progressEvent.loaded / (1024 * 1024)).toFixed(2),
                                totalMB: (progressEvent.total / (1024 * 1024)).toFixed(2),
                            });
                        }
                    }
                },
            });

            const template = response.data.data;
            state.templates = state.templates.map((item) => (item.id === template.id ? template : item));
            return template;
        } catch (error) {
            state.error = handleError(error);
            throw error;
        } finally {
            state.saving = false;
        }
    };

    const deleteTemplate = async (templateId) => {
        state.saving = true;
        state.error = null;

        try {
            await axios.delete(`/api/admin/templates/${templateId}`);
            state.templates = state.templates.filter((item) => item.id !== templateId);
            state.meta.total = Math.max(0, state.meta.total - 1);
        } catch (error) {
            state.error = handleError(error);
            throw error;
        } finally {
            state.saving = false;
        }
    };

    const deleteTemplatePackageFile = async (templateId) => {
        state.saving = true;
        state.error = null;

        try {
            const response = await axios.delete(`/api/admin/templates/${templateId}/package-file`);
            const template = response.data.data;
            state.templates = state.templates.map((item) => (item.id === template.id ? template : item));
            return template;
        } catch (error) {
            state.error = handleError(error);
            throw error;
        } finally {
            state.saving = false;
        }
    };

    const pagination = computed(() => state.meta);
    const templates = computed(() => state.templates);
    const isLoading = computed(() => state.loading && !state.initialized);
    const isRefreshing = computed(() => state.loading && state.initialized);
    const isSaving = computed(() => state.saving);

    return {
        state,
        templates,
        pagination,
        isLoading,
        isRefreshing,
        isSaving,
        fetchTemplates,
        createTemplate,
        updateTemplate,
        deleteTemplate,
        deleteTemplatePackageFile,
    };
}
