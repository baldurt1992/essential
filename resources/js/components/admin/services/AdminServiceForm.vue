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
                <ServiceFormImageUpload :current-image="currentImage" :error-message="fieldError('image')"
                    @image-change="handleImageChange" @image-clear="clearImage" />
            </aside>
        </section>

        <div v-if="generalError" class="service-form__alert">
            <Message severity="error" :closable="false">{{ generalError }}</Message>
        </div>

        <footer class="service-form__actions">
            <button type="button" class="essential-button essential-button--ghost" @click="emit('cancel')"
                :disabled="saving">
                Cancelar
            </button>
            <button type="submit" class="essential-button essential-button--primary" :disabled="saving">
                <span v-if="saving" class="service-form__spinner"></span>
                <span>{{ submitLabel }}</span>
            </button>
        </footer>
    </form>
</template>

<script setup>
    import { computed, onBeforeUnmount, ref, watch } from 'vue';
    import InputText from 'primevue/inputtext';
    import Textarea from 'primevue/textarea';
    import InputNumber from 'primevue/inputnumber';
    import ToggleSwitch from 'primevue/toggleswitch';
    import Message from 'primevue/message';
    import { useServiceForm } from '@/composables/admin/useServiceForm';
    import ServiceFormImageUpload from './ui/ServiceFormImageUpload.vue';

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

    // Usar el composable para toda la lógica del formulario
    const {
        form,
        errors: formErrors,
        currentImage,
        imageFile,
        handleTitleInput,
        handleSlugInput,
        regenerateSlug,
        handleImageChange,
        clearImage,
        updateBoolean,
        validate,
        buildPayload,
        resetForm: resetServiceForm,
        clearFieldError,
        fieldError: getFormFieldError,
    } = useServiceForm(props.service);

    const backendFieldErrors = ref({});

    const submitLabel = computed(() => (props.mode === 'edit' ? 'Guardar cambios' : 'Crear servicio'));

    // Combinar errores del formulario con errores del backend
    const fieldError = (field) => {
        const formError = getFormFieldError(field);
        if (formError) {
            return formError;
        }

        if (backendFieldErrors.value?.[field]) {
            const backendValue = backendFieldErrors.value[field];
            return Array.isArray(backendValue) ? backendValue[0] : backendValue;
        }

        return null;
    };

    const handleSubmit = () => {
        backendFieldErrors.value = {};
        if (!validate()) {
            return;
        }

        emit('submit', { payload: buildPayload() });
    };

    // Sincronizar errores del backend
    watch(
        () => props.backendErrors,
        (value) => {
            backendFieldErrors.value = value || {};
        },
        { deep: true },
    );

    // Resetear formulario cuando cambia el servicio
    watch(
        () => props.service,
        () => {
            resetServiceForm(props.service);
        },
        { immediate: true },
    );

    // Limpiar ObjectURL al desmontar
    onBeforeUnmount(() => {
        if (imageFile.value && currentImage.value?.startsWith('blob:')) {
            URL.revokeObjectURL(currentImage.value);
        }
    });
</script>

<style scoped>

    /* Service Form Specific Styles */
    .service-form__grid {
        grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
        gap: 36px;
    }

    .service-form__alert {
        margin-top: 4px;
    }

    @media (max-width: 980px) {
        .service-form__grid {
            grid-template-columns: 1fr;
        }
    }
</style>
