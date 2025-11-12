<template>
    <form class="plan-form" @submit.prevent="handleSubmit">
        <section class="plan-form__grid">
            <div class="plan-form__main">
                <div class="plan-form__section">
                    <h3 class="plan-form__section-title">Información del plan</h3>

                    <div class="plan-form__field" :class="{ 'plan-form__field--error': fieldError('name') }">
                        <label for="plan-name">Nombre *</label>
                        <InputText id="plan-name" v-model.trim="form.name" @input="handleNameInput"
                            placeholder="Ej. Suscripción Anual" />
                        <div class="plan-form__error-slot">
                            <small v-if="fieldError('name')" class="plan-form__error">{{ fieldError('name') }}</small>
                        </div>
                    </div>

                    <div class="plan-form__field" :class="{ 'plan-form__field--error': fieldError('slug') }">
                        <div class="plan-form__label-row">
                            <label for="plan-slug">Slug (opcional)</label>
                            <button type="button" class="plan-form__link" @click="regenerateSlug">
                                Regenerar automáticamente
                            </button>
                        </div>
                        <InputText id="plan-slug" v-model.trim="form.slug" @input="handleSlugInput"
                            placeholder="suscripcion-anual" />
                        <div class="plan-form__error-slot">
                            <small v-if="fieldError('slug')" class="plan-form__error">{{ fieldError('slug') }}</small>
                        </div>
                    </div>

                    <div class="plan-form__field" :class="{ 'plan-form__field--error': fieldError('description') }">
                        <label for="plan-description">Descripción</label>
                        <Textarea id="plan-description" v-model.trim="form.description" rows="4"
                            placeholder="Describe el alcance del plan, beneficios y límites." @input="clearFieldError('description')" />
                        <div class="plan-form__error-slot">
                            <small v-if="fieldError('description')" class="plan-form__error">{{ fieldError('description') }}</small>
                        </div>
                    </div>
                </div>

                <div class="plan-form__section plan-form__section--inline">
                    <div class="plan-form__field" :class="{ 'plan-form__field--error': fieldError('price') }">
                        <label for="plan-price">Precio *</label>
                        <InputNumber input-id="plan-price" v-model="form.price" mode="currency"
                            :currency="displayCurrency" locale="es-ES" :min="0.5" :step="0.5" placeholder="0,00"
                            @input="clearFieldError('price')" />
                        <div class="plan-form__error-slot">
                            <small v-if="fieldError('price')" class="plan-form__error">{{ fieldError('price') }}</small>
                        </div>
                    </div>

                    <div class="plan-form__field" :class="{ 'plan-form__field--error': fieldError('billing_interval') }">
                        <label for="plan-interval">Intervalo *</label>
                        <Dropdown id="plan-interval" v-model="form.billing_interval" :options="billingIntervalOptions"
                            optionLabel="label" optionValue="value" placeholder="Selecciona intervalo"
                            @change="clearFieldError('billing_interval')" />
                        <div class="plan-form__error-slot">
                            <small v-if="fieldError('billing_interval')" class="plan-form__error">{{ fieldError('billing_interval') }}</small>
                        </div>
                    </div>

                    <div class="plan-form__field" :class="{ 'plan-form__field--error': fieldError('billing_interval_count') }">
                        <label for="plan-interval-count"># de intervalos *</label>
                        <InputNumber input-id="plan-interval-count" v-model="form.billing_interval_count" :min="1"
                            :max="24" :step="1" @input="clearFieldError('billing_interval_count')" />
                        <div class="plan-form__error-slot">
                            <small v-if="fieldError('billing_interval_count')" class="plan-form__error">{{ fieldError('billing_interval_count') }}</small>
                        </div>
                    </div>

                    <div class="plan-form__switch">
                        <label for="plan-active">Plan disponible</label>
                        <div class="plan-form__switch-control">
                            <ToggleSwitch id="plan-active" v-model="form.is_active" />
                            <span>{{ form.is_active ? 'Activo' : 'Inactivo' }}</span>
                        </div>
                    </div>
                </div>

                <div class="plan-form__section">
                    <h3 class="plan-form__section-title">Características</h3>
                    <div class="plan-form__tags-input">
                        <InputText v-model="tagInput" placeholder="Agregar característica y Enter"
                            @keydown.enter.prevent="addTag" @keydown="onTagKeydown" @blur="addTag" />
                    </div>
                    <div v-if="form.features.length" class="plan-form__tags-list">
                        <Chip v-for="feature in form.features" :key="feature" :label="feature" removable
                            @remove="removeTag(feature)" />
                    </div>
                    <div class="plan-form__error-slot">
                        <small v-if="fieldError('features')" class="plan-form__error">{{ fieldError('features') }}</small>
                    </div>
                </div>

                <div class="plan-form__section plan-form__section--inline">
                    <div class="plan-form__field" :class="{ 'plan-form__field--error': fieldError('sort_order') }">
                        <label for="plan-sort">Orden</label>
                        <InputNumber input-id="plan-sort" v-model="form.sort_order" :min="0" :step="1"
                            @input="clearFieldError('sort_order')" />
                        <div class="plan-form__error-slot">
                            <small v-if="fieldError('sort_order')" class="plan-form__error">{{ fieldError('sort_order') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div v-if="generalError" class="plan-form__alert">
            <Message severity="error" :closable="false">{{ generalError }}</Message>
        </div>

        <footer class="plan-form__actions">
            <button type="button" class="qodef-button qodef-button--ghost" @click="emit('cancel')" :disabled="saving">
                Cancelar
            </button>
            <button type="submit" class="qodef-button qodef-button--primary" :disabled="saving">
                <span v-if="saving" class="plan-form__spinner"></span>
                <span>{{ submitLabel }}</span>
            </button>
        </footer>
    </form>
</template>

<script setup>
    import { computed, reactive, ref, watch } from 'vue';
    import InputNumber from 'primevue/inputnumber';
    import InputText from 'primevue/inputtext';
    import Textarea from 'primevue/textarea';
    import ToggleSwitch from 'primevue/toggleswitch';
    import Dropdown from 'primevue/dropdown';
    import Chip from 'primevue/chip';
    import Message from 'primevue/message';

    const billingIntervalOptions = [
        { label: 'Día', value: 'day' },
        { label: 'Semana', value: 'week' },
        { label: 'Mes', value: 'month' },
        { label: 'Año', value: 'year' },
    ];

    const props = defineProps({
        mode: {
            type: String,
            default: 'create',
        },
        plan: {
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
        name: '',
        slug: '',
        description: '',
        billing_interval: 'month',
        billing_interval_count: 1,
        price: null,
        features: [],
        is_active: true,
        sort_order: 0,
    });

    const errors = reactive({});
    const backendFieldErrors = ref({});
    const slugTouched = ref(false);
    const tagInput = ref('');

    const displayCurrency = computed(() => props.currency?.toUpperCase() ?? 'EUR');
    const submitLabel = computed(() => (props.mode === 'edit' ? 'Guardar cambios' : 'Crear plan'));

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

    const handleNameInput = () => {
        clearFieldError('name');
        if (!slugTouched.value) {
            form.slug = slugify(form.name);
        }
    };

    const handleSlugInput = () => {
        slugTouched.value = true;
        clearFieldError('slug');
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

    const regenerateSlug = () => {
        form.slug = slugify(form.name);
        slugTouched.value = false;
    };

    const addTag = () => {
        const value = (tagInput.value || '').trim();
        if (!value) {
            return;
        }

        const exists = form.features.some((tag) => tag.toLowerCase() === value.toLowerCase());
        if (!exists) {
            form.features = [...form.features, value];
            clearFieldError('features');
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
        form.features = form.features.filter((tag) => tag !== value);
    };

    const clearErrors = () => {
        Object.keys(errors).forEach((key) => {
            delete errors[key];
        });
    };

    const resetForm = () => {
        form.name = props.plan?.name ?? '';
        form.slug = props.plan?.slug ?? '';
        form.description = props.plan?.description ?? '';
        form.billing_interval = props.plan?.billing_interval ?? 'month';
        form.billing_interval_count = props.plan?.billing_interval_count ?? 1;
        form.price = props.plan?.price ?? null;
        form.features = Array.isArray(props.plan?.features) ? [...props.plan.features] : [];
        form.is_active = props.plan?.is_active ?? true;
        form.sort_order = props.plan?.sort_order ?? 0;
        tagInput.value = '';
        slugTouched.value = Boolean(form.slug);
        clearErrors();
        backendFieldErrors.value = {};
    };

    const validate = () => {
        clearErrors();
        const localErrors = {};

        if (!form.name?.trim()) {
            localErrors.name = 'El nombre es obligatorio.';
        }

        if (form.name && form.name.length > 255) {
            localErrors.name = 'Máximo 255 caracteres.';
        }

        if (form.slug && form.slug.length > 255) {
            localErrors.slug = 'Máximo 255 caracteres.';
        }

        if (!form.billing_interval) {
            localErrors.billing_interval = 'Selecciona un intervalo.';
        }

        if (!form.billing_interval_count || form.billing_interval_count < 1) {
            localErrors.billing_interval_count = 'Debe ser al menos 1.';
        }

        if (form.price === null || form.price === undefined || Number(form.price) < 0.5) {
            localErrors.price = 'Define un precio igual o superior a 0,50.';
        }

        if (form.features?.some((tag) => tag.length > 150)) {
            localErrors.features = 'Cada característica debe tener máximo 150 caracteres.';
        }

        Object.assign(errors, localErrors);

        return Object.keys(localErrors).length === 0;
    };

    const buildPayload = () => {
        return {
            name: form.name?.trim() ?? '',
            slug: form.slug?.trim() || null,
            description: form.description?.trim() || null,
            billing_interval: form.billing_interval,
            billing_interval_count: form.billing_interval_count,
            price: Number(form.price ?? 0),
            features: Array.isArray(form.features) ? [...form.features] : [],
            is_active: Boolean(form.is_active),
            sort_order: form.sort_order ?? 0,
        };
    };

    const handleSubmit = () => {
        backendFieldErrors.value = {};
        if (!validate()) {
            return;
        }

        emit('submit', {
            payload: buildPayload(),
        });
    };

    watch(
        () => props.plan,
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
</script>

<style scoped>
    .plan-form {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .plan-form__grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 32px;
    }

    .plan-form__main {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .plan-form__section {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .plan-form__section--inline {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 18px;
        align-items: flex-start;
    }

    .plan-form__section-title {
        font-family: 'Space Mono', monospace;
        font-size: 16px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin: 0 0 6px;
    }

    .plan-form__field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .plan-form__switch {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .plan-form__switch-control {
        display: flex;
        align-items: center;
        gap: 12px;
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.08em;
        font-size: 12px;
        text-transform: uppercase;
    }

    .plan-form__label-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .plan-form__link {
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

    .plan-form__link:hover {
        text-decoration: underline;
    }

    .plan-form__tags-input {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .plan-form__tags-input :deep(.p-inputtext) {
        width: 100%;
    }

    .plan-form__tags-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .plan-form__tags-list :deep(.p-chip) {
        background: rgba(23, 23, 23, 0.08);
        color: #171717;
        border-radius: 999px;
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.06em;
        padding: 6px 12px;
    }

    .plan-form__error {
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        color: #dd3333;
    }

    .plan-form__error-slot {
        min-height: 18px;
    }

    .plan-form__field--error :deep(.p-inputtext),
    .plan-form__field--error :deep(.p-inputnumber-input),
    .plan-form__field--error :deep(.p-dropdown),
    .plan-form__field--error :deep(.p-dropdown-label) {
        border-color: #dd3333;
    }

    .plan-form__alert {
        margin-top: 4px;
    }

    .plan-form__actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .plan-form__spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-top-color: #ffffff;
        margin-right: 8px;
        animation: plan-spin 1s linear infinite;
    }

    @keyframes plan-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 880px) {
        .plan-form__section--inline {
            grid-template-columns: 1fr;
        }
    }
</style>
