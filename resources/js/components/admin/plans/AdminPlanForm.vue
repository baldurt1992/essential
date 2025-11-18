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
                            placeholder="Describe el alcance del plan, beneficios y límites."
                            @input="clearFieldError('description')" />
                        <div class="plan-form__error-slot">
                            <small v-if="fieldError('description')" class="plan-form__error">{{
                                fieldError('description') }}</small>
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

                    <div class="plan-form__field"
                        :class="{ 'plan-form__field--error': fieldError('billing_interval') }">
                        <label for="plan-interval">Frecuencia de cobro *</label>
                        <Dropdown id="plan-interval" v-model="selectedBillingInterval" :options="billingIntervalOptions"
                            optionLabel="label" optionValue="value" placeholder="Selecciona frecuencia"
                            @change="handleBillingIntervalChange" />
                        <small class="plan-form__help-text">{{ getIntervalDescription() }}</small>
                        <div class="plan-form__error-slot">
                            <small v-if="fieldError('billing_interval')" class="plan-form__error">{{
                                fieldError('billing_interval') }}</small>
                            <small v-if="fieldError('billing_interval_count')" class="plan-form__error">{{
                                fieldError('billing_interval_count') }}</small>
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
                    <h3 class="plan-form__section-title">Límite de descargas</h3>
                    <div class="plan-form__switch">
                        <label for="plan-unlimited-downloads">Descargas ilimitadas</label>
                        <div class="plan-form__switch-control">
                            <ToggleSwitch id="plan-unlimited-downloads" v-model="form.unlimited_downloads"
                                @update:model-value="handleUnlimitedDownloadsChange" />
                            <span>{{ form.unlimited_downloads ? 'Ilimitado' : 'Limitado' }}</span>
                        </div>
                    </div>
                    <div v-if="!form.unlimited_downloads" class="plan-form__field"
                        :class="{ 'plan-form__field--error': fieldError('download_limit') }">
                        <label for="plan-download-limit">Límite de descargas mensual *</label>
                        <InputNumber input-id="plan-download-limit" v-model="form.download_limit" :min="1" :step="1"
                            placeholder="Ej. 10, 30, 100" @input="clearFieldError('download_limit')" />
                        <small class="plan-form__help-text">Número de plantillas que el usuario puede descargar por mes.
                            El contador se resetea automáticamente cada mes.</small>
                        <div class="plan-form__error-slot">
                            <small v-if="fieldError('download_limit')" class="plan-form__error">{{
                                fieldError('download_limit') }}</small>
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
                        <small v-if="fieldError('features')" class="plan-form__error">{{ fieldError('features')
                            }}</small>
                    </div>
                </div>

                <div class="plan-form__section plan-form__section--inline">
                    <div class="plan-form__field" :class="{ 'plan-form__field--error': fieldError('sort_order') }">
                        <label for="plan-sort">Orden</label>
                        <InputNumber input-id="plan-sort" v-model="form.sort_order" :min="0" :step="1"
                            @input="clearFieldError('sort_order')" />
                        <div class="plan-form__error-slot">
                            <small v-if="fieldError('sort_order')" class="plan-form__error">{{ fieldError('sort_order')
                                }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div v-if="generalError" class="plan-form__alert">
            <Message severity="error" :closable="false">{{ generalError }}</Message>
        </div>

        <footer class="plan-form__actions">
            <button type="button" class="essential-button essential-button--ghost" @click="emit('cancel')"
                :disabled="saving">
                Cancelar
            </button>
            <button type="submit" class="essential-button essential-button--primary" :disabled="saving">
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
        { label: 'Diario', value: 'day-1', interval: 'day', count: 1 },
        { label: 'Semanal', value: 'week-1', interval: 'week', count: 1 },
        { label: 'Quincenal', value: 'week-2', interval: 'week', count: 2 },
        { label: 'Mensual', value: 'month-1', interval: 'month', count: 1 },
        { label: 'Trimestral', value: 'month-3', interval: 'month', count: 3 },
        { label: 'Semestral', value: 'month-6', interval: 'month', count: 6 },
        { label: 'Anual', value: 'year-1', interval: 'year', count: 1 },
        { label: 'Bienal', value: 'year-2', interval: 'year', count: 2 },
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
        download_limit: null,
        unlimited_downloads: false,
    });

    const selectedBillingInterval = ref(null);

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

    const handleUnlimitedDownloadsChange = (value) => {
        if (value) {
            form.download_limit = null;
        }
    };

    const handleBillingIntervalChange = () => {
        const selected = billingIntervalOptions.find(opt => opt.value === selectedBillingInterval.value);
        if (selected) {
            form.billing_interval = selected.interval;
            form.billing_interval_count = selected.count;
            clearFieldError('billing_interval');
            clearFieldError('billing_interval_count');
        }
    };

    const getIntervalDescription = () => {
        if (!selectedBillingInterval.value) {
            return 'Selecciona la frecuencia de cobro del plan';
        }

        const selected = billingIntervalOptions.find(opt => opt.value === selectedBillingInterval.value);
        if (!selected) return '';

        const labels = {
            'Diario': 'El usuario será cobrado cada día',
            'Semanal': 'El usuario será cobrado cada semana',
            'Quincenal': 'El usuario será cobrado cada 2 semanas',
            'Mensual': 'El usuario será cobrado cada mes',
            'Trimestral': 'El usuario será cobrado cada 3 meses',
            'Semestral': 'El usuario será cobrado cada 6 meses',
            'Anual': 'El usuario será cobrado cada año',
            'Bienal': 'El usuario será cobrado cada 2 años',
        };

        return labels[selected.label] || '';
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
        form.download_limit = props.plan?.download_limit ?? null;
        form.unlimited_downloads = props.plan?.unlimited_downloads ?? false;

        // Mapear billing_interval y billing_interval_count a la opción seleccionada
        const matchingOption = billingIntervalOptions.find(opt =>
            opt.interval === form.billing_interval && opt.count === form.billing_interval_count
        );
        selectedBillingInterval.value = matchingOption ? matchingOption.value : null;

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

        if (!selectedBillingInterval.value) {
            localErrors.billing_interval = 'Selecciona una frecuencia de cobro.';
        }

        if (form.price === null || form.price === undefined || Number(form.price) < 0.5) {
            localErrors.price = 'Define un precio igual o superior a 0,50.';
        }

        if (!form.unlimited_downloads && (!form.download_limit || form.download_limit < 1)) {
            localErrors.download_limit = 'Define un límite de descargas válido (mínimo 1).';
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
            download_limit: form.unlimited_downloads ? null : (form.download_limit ?? null),
            unlimited_downloads: Boolean(form.unlimited_downloads),
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
    /* Plan Form Specific Styles */
    .plan-form__section--inline {
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    }

    .plan-form__alert {
        margin-top: 4px;
    }

    @media (max-width: 880px) {
        .plan-form__section--inline {
            grid-template-columns: 1fr;
        }
    }
</style>
