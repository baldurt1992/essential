<template>
    <form class="service-form" @submit.prevent="handleSubmit">
        <section class="service-form__grid">
            <div class="service-form__main">
                <div class="service-form__section">
                    <h3 class="service-form__section-title">Información principal</h3>

                    <div class="service-form__field" :class="{ 'service-form__field--error': fieldError('title') }">
                        <label for="service-title">Nombre *</label>
                        <InputText id="service-title" v-model.trim="form.title" @input="handleTitleInput"
                            placeholder="Ej. Gestión de redes" />
                        <div class="service-form__error-slot">
                            <small v-if="fieldError('title')" class="service-form__error">{{ fieldError('title')
                                }}</small>
                        </div>
                    </div>

                    <div class="service-form__field" :class="{ 'service-form__field--error': fieldError('slug') }">
                        <div class="service-form__label-row">
                            <label for="service-slug">Slug (opcional)</label>
                            <button type="button" class="service-form__link" @click="regenerateSlug">Regenerar
                                automáticamente</button>
                        </div>
                        <InputText id="service-slug" v-model.trim="form.slug" @input="handleSlugInput"
                            placeholder="gestion-redes" />
                        <div class="service-form__error-slot">
                            <small v-if="fieldError('slug')" class="service-form__error">{{ fieldError('slug')
                                }}</small>
                        </div>
                    </div>

                    <div class="service-form__field"
                        :class="{ 'service-form__field--error': fieldError('description') }">
                        <label for="service-description">Descripción</label>
                        <Textarea id="service-description" v-model.trim="form.description" rows="4"
                            placeholder="Describe en qué consiste el servicio."
                            @input="clearFieldError('description')" />
                        <div class="service-form__error-slot">
                            <small v-if="fieldError('description')" class="service-form__error">{{
                                fieldError('description') }}</small>
                        </div>
                    </div>
                </div>

                <div class="service-form__section service-form__section--inline">
                    <div class="service-form__switch">
                        <label for="service-active">Visible</label>
                        <div class="service-form__switch-control">
                            <ToggleSwitch id="service-active" :model-value="form.is_active"
                                @update:model-value="updateBoolean('is_active', $event)" />
                            <span>{{ form.is_active ? 'Activo' : 'Inactivo' }}</span>
                        </div>
                    </div>

                    <div class="service-form__switch">
                        <label for="service-popular">Destacado</label>
                        <div class="service-form__switch-control">
                            <ToggleSwitch id="service-popular" :model-value="form.is_popular"
                                @update:model-value="updateBoolean('is_popular', $event)" />
                            <span>{{ form.is_popular ? 'Popular' : 'Normal' }}</span>
                        </div>
                    </div>

                    <div class="service-form__field"
                        :class="{ 'service-form__field--error': fieldError('sort_order') }">
                        <label for="service-order">Orden</label>
                        <InputNumber input-id="service-order" v-model="form.sort_order" :min="0" :step="1"
                            @input="clearFieldError('sort_order')" />
                        <div class="service-form__error-slot">
                            <small v-if="fieldError('sort_order')" class="service-form__error">{{
                                fieldError('sort_order') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="service-form__aside">
                <h3 class="service-form__section-title">Imagen destacada</h3>
                <div class="service-form__upload" :class="{ 'service-form__field--error': fieldError('image') }">
                    <label>Imagen (JPG/PNG/WEBP)</label>
                    <div class="service-form__preview">
                        <Image v-if="currentImage" :src="currentImage" alt="Vista previa" width="220" preview />
                        <div v-else class="service-form__preview-placeholder">
                            <i class="pi pi-image"></i>
                            <p>Selecciona una imagen representativa</p>
                        </div>
                    </div>
                    <input ref="imageRef" type="file" accept="image/png,image/jpeg,image/webp"
                        class="service-form__file-input" @change="onImageChange" />
                    <div class="service-form__upload-actions">
                        <button type="button" class="qodef-button qodef-button--ghost" @click="pickImage">
                            Seleccionar imagen
                        </button>
                        <button v-if="imageFile || imageUrl" type="button" class="service-form__link"
                            @click="clearImage">
                            Quitar imagen
                        </button>
                    </div>
                    <div class="service-form__error-slot">
                        <small v-if="fieldError('image')" class="service-form__error">{{ fieldError('image') }}</small>
                    </div>
                </div>
            </aside>
        </section>

        <div v-if="generalError" class="service-form__alert">
            <Message severity="error" :closable="false">{{ generalError }}</Message>
        </div>

        <footer class="service-form__actions">
            <button type="button" class="qodef-button qodef-button--ghost" @click="emit('cancel')" :disabled="saving">
                Cancelar
            </button>
            <button type="submit" class="qodef-button qodef-button--primary" :disabled="saving">
                <span v-if="saving" class="service-form__spinner"></span>
                <span>{{ submitLabel }}</span>
            </button>
        </footer>
    </form>
</template>

<script setup>
    import { computed, onBeforeUnmount, reactive, ref, watch } from 'vue';
    import InputText from 'primevue/inputtext';
    import Textarea from 'primevue/textarea';
    import InputNumber from 'primevue/inputnumber';
    import ToggleSwitch from 'primevue/toggleswitch';
    import Image from 'primevue/image';
    import Message from 'primevue/message';

    const props = defineProps({
        mode: {
            type: String,
            default: 'create',
        },
        service: {
            type: Object,
            default: null,
        },
        saving: {
            type: Boolean,
            default: false,
        },
        backendErrors: {
            type: Object,
            default: () => ({}),
        },
        generalError: {
            type: String,
            default: '',
        },
    });

    const emit = defineEmits(['submit', 'cancel']);

    const form = reactive({
        title: '',
        slug: '',
        description: '',
        is_active: true,
        is_popular: false,
        sort_order: 0,
    });

    const errors = reactive({});
    const backendFieldErrors = ref({});
    const slugTouched = ref(false);
    const imageFile = ref(null);
    const imageUrl = ref(null);
    const imageRef = ref(null);

    const updateBoolean = (field, value) => {
        form[field] = Boolean(value);
        clearFieldError(field);
    };

    const submitLabel = computed(() => (props.mode === 'edit' ? 'Guardar cambios' : 'Crear servicio'));

    const currentImage = computed(() => {
        if (imageUrl.value) {
            return imageUrl.value;
        }
        if (props.service?.image_path) {
            return resolveImagePath(props.service.image_path);
        }
        return null;
    });

    const fieldError = (field) => {
        if (errors[field]) {
            return errors[field];
        }

        if (backendFieldErrors.value?.[field]) {
            const backendValue = backendFieldErrors.value[field];
            return Array.isArray(backendValue) ? backendValue[0] : backendValue;
        }

        return null;
    };

    const clearFieldError = (field) => {
        if (errors[field]) {
            delete errors[field];
        }
        if (backendFieldErrors.value?.[field]) {
            delete backendFieldErrors.value[field];
        }
    };

    const handleTitleInput = () => {
        clearFieldError('title');
        if (!slugTouched.value) {
            form.slug = slugify(form.title);
        }
    };

    const handleSlugInput = () => {
        slugTouched.value = true;
        clearFieldError('slug');
    };

    const slugify = (value) => value
        .toString()
        .normalize('NFD')
        .replace(/[^\w\s-]/g, '')
        .trim()
        .replace(/[-\s]+/g, '-')
        .toLowerCase();

    const regenerateSlug = () => {
        form.slug = slugify(form.title);
        slugTouched.value = false;
    };

    const pickImage = () => {
        imageRef.value?.click();
    };

    const onImageChange = (event) => {
        const file = event.target.files?.[0];
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

    const clearImage = () => {
        clearObjectUrl();
        imageFile.value = null;
        imageUrl.value = null;
        if (imageRef.value) {
            imageRef.value.value = '';
        }
        clearFieldError('image');
    };

    const clearObjectUrl = () => {
        if (imageUrl.value) {
            URL.revokeObjectURL(imageUrl.value);
        }
    };

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

    const resolveLinkUrl = (slug) => {
        if (!slug) {
            return '/servicios';
        }

        return `/servicios/${slug}`;
    };

    const clearErrors = () => {
        Object.keys(errors).forEach((key) => delete errors[key]);
    };

    const resetForm = () => {
        form.title = props.service?.title ?? '';
        form.slug = props.service?.slug ?? '';
        form.description = props.service?.description ?? '';
        form.is_active = props.service?.is_active ?? true;
        form.is_popular = props.service?.is_popular ?? false;
        form.sort_order = props.service?.sort_order ?? 0;
        slugTouched.value = Boolean(form.slug);
        backendFieldErrors.value = {};
        clearErrors();
        clearObjectUrl();
        imageFile.value = null;
        imageUrl.value = null;
    };

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

    const handleSubmit = () => {
        backendFieldErrors.value = {};
        if (!validate()) {
            return;
        }

        emit('submit', { payload: buildPayload() });
    };

    watch(
        () => props.service,
        () => {
            resetForm();
        },
        { immediate: true },
    );

    watch(
        () => props.backendErrors,
        (value) => {
            backendFieldErrors.value = value || {};
        },
        { deep: true },
    );

    onBeforeUnmount(() => {
        clearObjectUrl();
    });
</script>

<style scoped>
    .service-form {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .service-form__grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
        gap: 36px;
    }

    .service-form__main {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .service-form__section {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .service-form__section--inline {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 18px;
        align-items: flex-start;
    }

    .service-form__section-title {
        font-family: 'Space Mono', monospace;
        font-size: 16px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin: 0 0 6px;
    }

    .service-form__field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .service-form__label-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .service-form__link {
        background: none;
        border: none;
        padding: 0;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #dd3333;
        cursor: pointer;
    }

    .service-form__link:hover {
        text-decoration: underline;
    }

    .service-form__switch {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .service-form__switch-control {
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.08em;
        font-size: 12px;
        text-transform: uppercase;
    }

    .service-form__aside {
        display: flex;
        flex-direction: column;
        gap: 24px;
        background: rgba(23, 23, 23, 0.02);
        border-radius: 18px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 22px 24px;
    }

    .service-form__upload {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .service-form__preview {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 220px;
        border-radius: 16px;
        border: 1px dashed rgba(23, 23, 23, 0.2);
        background: rgba(255, 255, 255, 0.8);
        overflow: hidden;
    }

    .service-form__preview-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        color: rgba(23, 23, 23, 0.5);
        font-family: 'Inter', sans-serif;
        text-align: center;
        padding: 30px 20px;
    }

    .service-form__preview-placeholder .pi {
        font-size: 32px;
    }

    .service-form__file-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
        width: 0;
        height: 0;
    }

    .service-form__upload-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .service-form__error {
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        color: #dd3333;
    }

    .service-form__error-slot {
        min-height: 18px;
    }

    .service-form__field--error :deep(.p-inputtext),
    .service-form__field--error :deep(.p-inputnumber-input) {
        border-color: #dd3333;
    }

    .service-form__alert {
        margin-top: 4px;
    }

    .service-form__actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .service-form__spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-top-color: #ffffff;
        margin-right: 8px;
        animation: service-spin 1s linear infinite;
    }

    @keyframes service-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 980px) {
        .service-form__grid {
            grid-template-columns: 1fr;
        }
    }
</style>
