import { reactive, ref, computed, watch } from 'vue';
import { useSlugGeneration } from './useSlugGeneration';

/**
 * Composable para manejar la lógica del formulario de servicios
 * Incluye: validación, manejo de imagen, construcción de payload, resolución de rutas
 */
export function useServiceForm(initialService = null) {
    const { generateSlug } = useSlugGeneration();

    const form = reactive({
        title: '',
        slug: '',
        description: '',
        is_active: true,
        is_popular: false,
        sort_order: 0,
    });

    const errors = reactive({});
    const slugTouched = ref(false);
    const imageFile = ref(null);
    const imageUrl = ref(null);

    /**
     * Resuelve la ruta de una imagen desde diferentes formatos
     */
    const resolveImagePath = (path) => {
        if (!path) {
            return null;
        }

        if (path.startsWith('http://') || path.startsWith('https://')) {
            return path;
        }

        if (path.startsWith('/')) {
            return path;
        }

        if (path.startsWith('storage/')) {
            return `/${path}`;
        }

        return `/storage/${path}`;
    };

    /**
     * Resuelve la URL del link basado en el slug
     */
    const resolveLinkUrl = (slug) => {
        if (!slug) {
            return '/servicios';
        }

        return `/servicios/${slug}`;
    };

    /**
     * Obtiene la imagen actual (preview o del servicio)
     */
    const currentImage = computed(() => {
        if (imageUrl.value) {
            return imageUrl.value;
        }
        // Usar image_url del backend si está disponible (URL completa)
        if (initialService?.image_url) {
            return initialService.image_url;
        }
        // Fallback a image_path si image_url no está disponible
        if (initialService?.image_path) {
            return resolveImagePath(initialService.image_path);
        }
        return null;
    });

    /**
     * Limpia el ObjectURL para evitar memory leaks
     */
    const clearObjectUrl = () => {
        if (imageUrl.value) {
            URL.revokeObjectURL(imageUrl.value);
        }
    };

    /**
     * Maneja el cambio de imagen
     */
    const handleImageChange = (file) => {
        if (!file) {
            return;
        }

        if (!file.type.startsWith('image/')) {
            errors.image = 'Selecciona un archivo de imagen válido.';
            return;
        }

        clearObjectUrl();
        imageFile.value = file;
        imageUrl.value = URL.createObjectURL(file);
        clearFieldError('image');
    };

    /**
     * Limpia la imagen seleccionada
     */
    const clearImage = () => {
        clearObjectUrl();
        imageFile.value = null;
        imageUrl.value = null;
        clearFieldError('image');
    };

    /**
     * Maneja el input del título y genera slug automáticamente
     */
    const handleTitleInput = () => {
        clearFieldError('title');
        if (!slugTouched.value) {
            form.slug = generateSlug(form.title);
        }
    };

    /**
     * Maneja el input del slug manual
     */
    const handleSlugInput = () => {
        slugTouched.value = true;
        clearFieldError('slug');
    };

    /**
     * Regenera el slug desde el título
     */
    const regenerateSlug = () => {
        form.slug = generateSlug(form.title);
        slugTouched.value = false;
    };

    /**
     * Obtiene el error de un campo
     */
    const fieldError = (field) => {
        return errors[field] ?? null;
    };

    /**
     * Limpia el error de un campo
     */
    const clearFieldError = (field) => {
        if (errors[field]) {
            delete errors[field];
        }
    };

    /**
     * Limpia todos los errores
     */
    const clearErrors = () => {
        Object.keys(errors).forEach((key) => delete errors[key]);
    };

    /**
     * Valida el formulario
     */
    const validate = () => {
        clearErrors();
        const localErrors = {};

        if (!form.title?.trim()) {
            localErrors.title = 'El nombre es obligatorio.';
        }

        if (form.title && form.title.length > 255) {
            localErrors.title = 'Máximo 255 caracteres.';
        }

        if (form.slug && form.slug.length > 255) {
            localErrors.slug = 'Máximo 255 caracteres.';
        }

        Object.assign(errors, localErrors);
        return Object.keys(localErrors).length === 0;
    };

    /**
     * Construye el payload para enviar al backend
     */
    const buildPayload = () => {
        const payload = {
            title: form.title?.trim() ?? '',
            slug: form.slug?.trim() || null,
            description: form.description?.trim() || null,
            link_url: resolveLinkUrl(form.slug?.trim() || ''),
            is_active: form.is_active ? 1 : 0,
            is_popular: form.is_popular ? 1 : 0,
            sort_order: form.sort_order ?? 0,
        };

        if (imageFile.value) {
            payload.image = imageFile.value;
        }

        return payload;
    };

    /**
     * Resetea el formulario con los datos del servicio
     */
    const resetForm = (service = null) => {
        const serviceData = service ?? initialService;
        form.title = serviceData?.title ?? '';
        form.slug = serviceData?.slug ?? '';
        form.description = serviceData?.description ?? '';
        form.is_active = serviceData?.is_active ?? true;
        form.is_popular = serviceData?.is_popular ?? false;
        form.sort_order = serviceData?.sort_order ?? 0;
        slugTouched.value = Boolean(form.slug);
        clearErrors();
        clearObjectUrl();
        imageFile.value = null;
        imageUrl.value = null;
    };

    /**
     * Actualiza un campo booleano
     */
    const updateBoolean = (field, value) => {
        form[field] = Boolean(value);
        clearFieldError(field);
    };

    // Resetear cuando cambia el servicio inicial
    watch(
        () => initialService,
        () => {
            resetForm();
        },
        { immediate: true },
    );

    return {
        // State
        form,
        errors,
        slugTouched,
        imageFile,
        imageUrl,
        currentImage,

        // Actions
        handleTitleInput,
        handleSlugInput,
        regenerateSlug,
        handleImageChange,
        clearImage,
        updateBoolean,
        validate,
        buildPayload,
        resetForm,
        clearErrors,
        clearFieldError,
        fieldError,
    };
}

