<template>
    <form class="template-form" @submit.prevent="handleSubmit">
        <section class="template-form__grid">
            <div class="template-form__main">
                <div class="template-form__section">
                    <h3 class="template-form__section-title">Información básica</h3>
                    <div class="template-form__field" :class="{ 'template-form__field--error': fieldError('title') }">
                        <label for="template-title">Nombre de la plantilla *</label>
                        <InputText id="template-title" v-model.trim="form.title" @input="handleTitleInput"
                            placeholder="Ej. Flyer Summer Party" />
                        <div class="template-form__error-slot">
                            <small v-if="fieldError('title')" class="template-form__error">{{ fieldError('title')
                                }}</small>
                        </div>
                    </div>

                    <div class="template-form__field" :class="{ 'template-form__field--error': fieldError('slug') }">
                        <div class="template-form__label-row">
                            <label for="template-slug">Slug (opcional)</label>
                            <button type="button" class="template-form__link" @click="regenerateSlug">
                                Regenerar automáticamente
                            </button>
                        </div>
                        <InputText id="template-slug" v-model.trim="form.slug" @input="() => handleSlugInput()"
                            placeholder="flyer-summer-party" />
                        <div class="template-form__error-slot">
                            <small v-if="fieldError('slug')" class="template-form__error">{{ fieldError('slug')
                                }}</small>
                        </div>
                    </div>

                    <div class="template-form__field"
                        :class="{ 'template-form__field--error': fieldError('description') }">
                        <label for="template-description">Descripción</label>
                        <Textarea id="template-description" v-model.trim="form.description" rows="5"
                            placeholder="Describe los elementos principales, formatos incluidos y recomendaciones."
                            @input="clearFieldError('description')" />
                        <div class="template-form__error-slot">
                            <small v-if="fieldError('description')" class="template-form__error">{{
                                fieldError('description') }}</small>
                        </div>
                    </div>
                </div>

                <div class="template-form__section template-form__section--inline">
                    <div class="template-form__field" :class="{ 'template-form__field--error': fieldError('price') }">
                        <label for="template-price">Precio *</label>
                        <InputNumber input-id="template-price" v-model="form.price" mode="currency"
                            :currency="displayCurrency" :min="0.5" :step="0.5" locale="es-ES" placeholder="0,00"
                            @input="clearFieldError('price')" />
                        <div class="template-form__error-slot">
                            <small v-if="fieldError('price')" class="template-form__error">{{ fieldError('price')
                                }}</small>
                        </div>
                    </div>

                    <div class="template-form__field"
                        :class="{ 'template-form__field--error': fieldError('sort_order') }">
                        <label for="template-order">Orden</label>
                        <InputNumber input-id="template-order" v-model="form.sort_order" :min="0" :step="1"
                            @input="clearFieldError('sort_order')" />
                        <div class="template-form__error-slot">
                            <small v-if="fieldError('sort_order')" class="template-form__error">{{
                                fieldError('sort_order')
                                }}</small>
                        </div>
                    </div>

                    <div class="template-form__switch">
                        <label for="template-active">Visible en catálogo</label>
                        <div class="template-form__switch-control">
                            <ToggleSwitch v-model="form.is_active" />
                            <span>{{ form.is_active ? 'Activa' : 'Oculta' }}</span>
                        </div>
                    </div>

                    <div class="template-form__switch">
                        <label for="template-popular">Destacar como popular</label>
                        <div class="template-form__switch-control">
                            <ToggleSwitch v-model="form.is_popular" />
                            <span>{{ form.is_popular ? 'Popular' : 'Estándar' }}</span>
                        </div>
                    </div>

                    <div class="template-form__switch">
                        <label for="template-new">Marcar como nuevo</label>
                        <div class="template-form__switch-control">
                            <ToggleSwitch v-model="form.is_new" />
                            <span>{{ form.is_new ? 'Nuevo' : 'Catálogo' }}</span>
                        </div>
                    </div>
                </div>

                <div class="template-form__section">
                    <label class="template-form__tags-label">Etiquetas</label>
                    <div class="template-form__tags-input">
                        <InputText v-model="tagInput" placeholder="Agregar etiqueta y presionar Enter"
                            @keydown.enter.prevent="addTag" @keydown="onTagKeydown" @blur="addTag" />
                    </div>

                    <div v-if="form.tags.length" class="template-form__tags-list">
                        <Chip v-for="tag in form.tags" :key="tag" :label="tag" removable @remove="removeTag(tag)" />
                    </div>

                    <div class="template-form__error-slot">
                        <small v-if="fieldError('tags')" class="template-form__error">{{ fieldError('tags') }}</small>
                    </div>
                </div>
            </div>

            <aside class="template-form__aside">
                <h3 class="template-form__section-title">Archivos</h3>

                <div class="template-form__upload"
                    :class="{ 'template-form__field--error': fieldError('preview_image') }">
                    <label>Imagen de vista previa *</label>
                    <div class="template-form__preview">
                        <Image v-if="currentPreview" :src="currentPreview" alt="Vista previa" width="220" preview />
                        <div v-else class="template-form__preview-placeholder">
                            <span class="pi pi-image"></span>
                            <p>Selecciona una imagen (JPG, PNG, WEBP)</p>
                        </div>
                    </div>
                    <input ref="previewInputRef" type="file" accept="image/png,image/jpeg,image/webp"
                        class="template-form__file-input" @change="onPreviewChange" />
                    <div class="template-form__upload-actions">
                        <button type="button" class="qodef-button qodef-button--ghost" @click="triggerPreviewPick">
                            Seleccionar imagen
                        </button>
                        <button v-if="previewFile || previewUrl" type="button" class="template-form__link"
                            @click="clearPreview">Quitar imagen</button>
                    </div>
                    <div class="template-form__error-slot">
                        <small v-if="fieldError('preview_image')" class="template-form__error">{{
                            fieldError('preview_image') }}</small>
                    </div>
                </div>

                <div class="template-form__upload"
                    :class="{ 'template-form__field--error': fieldError('package_file') }">
                    <label>Paquete descargable *</label>
                    <div class="template-form__package-info">
                        <span class="pi pi-file-zip"></span>
                        <div>
                            <p class="template-form__package-name">
                                {{ packageFileName }}
                            </p>
                            <small>Máx. 50 MB</small>
                        </div>
                    </div>
                    <input ref="packageInputRef" type="file" accept=".zip,.rar" class="template-form__file-input"
                        @change="onPackageChange" />
                    <div class="template-form__upload-actions">
                        <button type="button" class="qodef-button qodef-button--ghost" @click="triggerPackagePick">
                            Seleccionar archivo
                        </button>
                        <button v-if="packageFile" type="button" class="template-form__link" @click="clearPackage">
                            Quitar archivo
                        </button>
                    </div>
                    <div class="template-form__error-slot">
                        <small v-if="fieldError('package_file')" class="template-form__error">{{
                            fieldError('package_file')
                            }}</small>
                    </div>
                </div>
            </aside>
        </section>

        <div v-if="generalError" class="template-form__alert">
            <Message severity="error" :closable="false">{{ generalError }}</Message>
        </div>

        <footer class="template-form__actions">
            <button type="button" class="qodef-button qodef-button--ghost" @click="emit('cancel')" :disabled="saving">
                Cancelar
            </button>
            <button type="submit" class="qodef-button qodef-button--primary" :disabled="saving">
                <span v-if="saving" class="template-form__spinner"></span>
                <span>{{ submitLabel }}</span>
            </button>
        </footer>
    </form>
</template>

<script setup>
    import { computed, onBeforeUnmount, reactive, ref, watch } from 'vue';
    import InputNumber from 'primevue/inputnumber';
    import ToggleSwitch from 'primevue/toggleswitch';
    import InputText from 'primevue/inputtext';
    import Textarea from 'primevue/textarea';
    import Image from 'primevue/image';
    import Chip from 'primevue/chip';
    import Message from 'primevue/message';
    import Button from 'primevue/button';

    const props = defineProps({
        mode: {
            type: String,
            default: 'create',
        },
        template: {
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
        currency: {
            type: String,
            default: 'EUR',
        },
    });

    const emit = defineEmits(['submit', 'cancel']);

    const form = reactive({
        title: '',
        slug: '',
        description: '',
        price: null,
        tags: [],
        is_active: true,
        is_popular: false,
        is_new: false,
        sort_order: 0,
    });

    const tagInput = ref('');
    const errors = reactive({});
    const backendFieldErrors = ref({});
    const slugTouched = ref(false);

    const previewFile = ref(null);
    const previewUrl = ref(null);
    const packageFile = ref(null);

    const previewInputRef = ref(null);
    const packageInputRef = ref(null);

    const displayCurrency = computed(() => props.currency?.toUpperCase() ?? 'EUR');
    const submitLabel = computed(() => (props.mode === 'edit' ? 'Guardar cambios' : 'Crear plantilla'));

    const packageFileName = computed(() => {
        if (packageFile.value) {
            return packageFile.value.name;
        }

        if (props.template?.download_path) {
            return props.template.download_path;
        }

        return 'Selecciona un archivo .zip o .rar';
    });

    const currentPreview = computed(() => {
        if (previewUrl.value) {
            return previewUrl.value;
        }

        if (props.template?.preview_image_path) {
            return resolvePreviewUrl(props.template.preview_image_path);
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

    const resetForm = () => {
        form.title = props.template?.title ?? '';
        form.slug = props.template?.slug ?? '';
        form.description = props.template?.description ?? '';
        form.price = props.template?.price ?? null;
        form.tags = Array.isArray(props.template?.tags) ? [...props.template.tags] : [];
        form.is_active = props.template?.is_active ?? true;
        form.is_popular = props.template?.is_popular ?? false;
        form.is_new = props.template?.is_new ?? false;
        form.sort_order = props.template?.sort_order ?? 0;
        tagInput.value = '';

        if (previewUrl.value) {
            URL.revokeObjectURL(previewUrl.value);
        }

        previewFile.value = null;
        previewUrl.value = null;
        packageFile.value = null;
        slugTouched.value = Boolean(form.slug);
        clearErrors();
        backendFieldErrors.value = {};
    };

    const clearErrors = () => {
        Object.keys(errors).forEach((key) => {
            delete errors[key];
        });
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

    const regenerateSlug = () => {
        form.slug = slugify(form.title);
        slugTouched.value = false;
    };

    const slugify = (value) => {
        return value
            .toString()
            .normalize('NFD')
            .replace(/[^\w\s-]/g, '')
            .trim()
            .replace(/[-\s]+/g, '-')
            .toLowerCase();
    };

    const triggerPreviewPick = () => {
        previewInputRef.value?.click();
    };

    const triggerPackagePick = () => {
        packageInputRef.value?.click();
    };

    const onPreviewChange = (event) => {
        const file = event.target.files?.[0];
        if (!file) {
            return;
        }

        if (!file.type.startsWith('image/')) {
            errors.preview_image = 'El archivo seleccionado no es una imagen válida.';
            return;
        }

        if (previewUrl.value) {
            URL.revokeObjectURL(previewUrl.value);
        }

        previewFile.value = file;
        previewUrl.value = URL.createObjectURL(file);
        delete errors.preview_image;
    };

    const onPackageChange = (event) => {
        const file = event.target.files?.[0];
        if (!file) {
            return;
        }

        const validExtensions = ['zip', 'rar'];
        const extension = file.name.split('.').pop()?.toLowerCase();
        if (!extension || !validExtensions.includes(extension)) {
            errors.package_file = 'Formato no soportado. Usa archivos .zip o .rar.';
            return;
        }

        // Validar tamaño máximo: 50MB (50 * 1024 * 1024 bytes)
        const maxSize = 50 * 1024 * 1024; // 50MB en bytes
        if (file.size > maxSize) {
            errors.package_file = `El archivo es demasiado grande. El tamaño máximo permitido es 50MB. Tu archivo tiene ${(file.size / (1024 * 1024)).toFixed(2)}MB.`;
            return;
        }

        packageFile.value = file;
        delete errors.package_file;
    };

    const clearPreview = () => {
        if (previewUrl.value) {
            URL.revokeObjectURL(previewUrl.value);
        }

        previewUrl.value = null;
        previewFile.value = null;
        previewInputRef.value && (previewInputRef.value.value = '');
        clearFieldError('preview_image');
    };

    const clearPackage = () => {
        packageFile.value = null;
        packageInputRef.value && (packageInputRef.value.value = '');
        clearFieldError('package_file');
    };

    const resolvePreviewUrl = (path) => {
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

    const validate = () => {
        clearErrors();
        const localErrors = {};

        if (!form.title?.trim()) {
            localErrors.title = 'El nombre es obligatorio.';
        } else if (form.title.length > 255) {
            localErrors.title = 'Máximo 255 caracteres.';
        }

        if (form.slug && form.slug.length > 255) {
            localErrors.slug = 'Máximo 255 caracteres.';
        }

        if (form.description && form.description.length > 2000) {
            localErrors.description = 'La descripción supera el límite de 2000 caracteres.';
        }

        if (form.price === null || form.price === undefined || Number(form.price) < 0.5) {
            localErrors.price = 'Define un precio igual o superior a 0,50.';
        }

        if (form.sort_order !== null && form.sort_order !== undefined && form.sort_order < 0) {
            localErrors.sort_order = 'El orden debe ser 0 o mayor.';
        }

        if (form.tags?.some((tag) => tag.length > 100)) {
            localErrors.tags = 'Cada etiqueta debe tener máximo 100 caracteres.';
        }

        if (props.mode === 'create' && !previewFile.value) {
            localErrors.preview_image = 'La vista previa es obligatoria para nuevas plantillas.';
        }

        if (props.mode === 'create' && !packageFile.value) {
            localErrors.package_file = 'Debes adjuntar el paquete descargable (.zip o .rar).';
        }

        Object.assign(errors, localErrors);
        return Object.keys(localErrors).length === 0;
    };

    const buildPayload = () => {
        const payload = {
            title: form.title?.trim() ?? '',
            slug: form.slug?.trim() || null,
            description: form.description?.trim() || null,
            price: Number(form.price ?? 0),
            tags: Array.isArray(form.tags) ? form.tags.filter((tag) => tag && tag.trim()).map((tag) => tag.trim()) : [],
            is_active: Boolean(form.is_active),
            is_popular: Boolean(form.is_popular),
            is_new: Boolean(form.is_new),
            sort_order: form.sort_order ?? 0,
        };

        return payload;
    };

    const buildFormData = (payload) => {
        const formData = new FormData();

        formData.append('title', payload.title ?? '');
        if (payload.slug) {
            formData.append('slug', payload.slug);
        }
        if (payload.description) {
            formData.append('description', payload.description);
        }

        formData.append('price', payload.price ?? 0);
        formData.append('is_active', payload.is_active ? '1' : '0');
        formData.append('is_popular', payload.is_popular ? '1' : '0');
        formData.append('is_new', payload.is_new ? '1' : '0');
        formData.append('sort_order', payload.sort_order ?? 0);

        if (payload.tags?.length) {
            payload.tags.forEach((tag) => formData.append('tags[]', tag));
        }

        if (previewFile.value) {
            formData.append('preview_image', previewFile.value, previewFile.value.name);
        }

        if (packageFile.value) {
            formData.append('package_file', packageFile.value, packageFile.value.name);
        }

        return formData;
    };

    const handleSubmit = () => {
        backendFieldErrors.value = {};
        if (!validate()) {
            return;
        }

        const payload = buildPayload();
        const formData = buildFormData(payload);

        emit('submit', {
            payload,
            formData,
            setPreviewFile,
            setPackageFile,
            updateBackendErrors,
        });
    };

    const setPreviewFile = (file) => {
        if (previewUrl.value) {
            URL.revokeObjectURL(previewUrl.value);
        }

        previewFile.value = file;
        previewUrl.value = file ? URL.createObjectURL(file) : null;
    };

    const setPackageFile = (file) => {
        packageFile.value = file;
    };

    const updateBackendErrors = (errorsObject = {}) => {
        backendFieldErrors.value = errorsObject;
    };

    const addTag = () => {
        const value = (tagInput.value || '').trim();
        if (!value) {
            return;
        }

        const exists = form.tags.some((tag) => tag.toLowerCase() === value.toLowerCase());
        if (!exists) {
            form.tags = [...form.tags, value];
            clearFieldError('tags');
        }

        tagInput.value = '';
    };

    const onTagKeydown = (event) => {
        if (event.key === ',' || event.key === ';') {
            event.preventDefault();
            addTag();
        }
    };

    const removeTag = (value) => {
        form.tags = form.tags.filter((tag) => tag !== value);
    };

    watch(
        () => props.template,
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
        if (previewUrl.value) {
            URL.revokeObjectURL(previewUrl.value);
        }
    });
</script>

<style scoped>
    .template-form {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .template-form__grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
        gap: 36px;
    }

    .template-form__main {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .template-form__section {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .template-form__section--inline {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 18px;
        align-items: flex-start;
    }

    .template-form__section-title {
        font-family: 'Space Mono', monospace;
        font-size: 16px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin: 0 0 6px;
    }

    .template-form__field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .template-form__switch {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .template-form__switch-control {
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.08em;
        font-size: 12px;
        text-transform: uppercase;
    }

    .template-form__tags-label {
        font-family: 'Space Mono', monospace;
        font-size: 14px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .template-form__tags-input {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .template-form__tags-input :deep(.p-inputtext) {
        width: 100%;
    }

    .template-form__tags-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
    }

    .template-form__tags-list :deep(.p-chip) {
        background: rgba(23, 23, 23, 0.08);
        color: #171717;
        border-radius: 999px;
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.06em;
        padding: 6px 12px;
    }

    .template-form__tags-list :deep(.p-chip .p-chip-remove-icon) {
        color: inherit;
    }

    .template-form__aside {
        display: flex;
        flex-direction: column;
        gap: 24px;
        background: rgba(23, 23, 23, 0.02);
        border-radius: 18px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 22px 24px;
    }

    .template-form__upload {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .template-form__preview {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 220px;
        border-radius: 16px;
        border: 1px dashed rgba(23, 23, 23, 0.2);
        background: rgba(255, 255, 255, 0.8);
        overflow: hidden;
    }

    .template-form__preview-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        color: rgba(23, 23, 23, 0.5);
        font-family: 'Inter', sans-serif;
        text-align: center;
        padding: 30px 20px;
    }

    .template-form__preview-placeholder .pi {
        font-size: 32px;
    }

    .template-form__package-info {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 14px;
        border-radius: 14px;
        background: rgba(23, 23, 23, 0.04);
    }

    .template-form__package-info .pi {
        font-size: 24px;
        color: #171717;
    }

    .template-form__package-name {
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.06em;
        font-size: 13px;
        margin: 0 0 6px;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .template-form__upload-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .template-form__file-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
        width: 0;
        height: 0;
    }

    .template-form__link {
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

    .template-form__link:hover {
        text-decoration: underline;
    }

    .template-form__label-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .template-form__error {
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        color: #dd3333;
    }

    .template-form__error-slot {
        min-height: 18px;
    }

    .template-form__field--error :deep(.p-inputtext),
    .template-form__field--error :deep(.p-inputnumber-input),
    .template-form__field--error :deep(.p-chips-input-token input) {
        border-color: #dd3333;
    }

    .template-form__alert {
        margin-top: 4px;
    }

    .template-form__actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .template-form__spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-top-color: #ffffff;
        margin-right: 8px;
        animation: template-spin 1s linear infinite;
    }

    @keyframes template-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 980px) {
        .template-form__grid {
            grid-template-columns: 1fr;
        }
    }
</style>
