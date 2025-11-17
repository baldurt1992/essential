<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)" modal dismissable-mask
        class="contact-dialog" :breakpoints="breakpoints" :style="{ width: 'min(560px, 92vw)' }"
        @hide="$emit('hide')">
        <template #header>
            <div class="contact-dialog__header">
                <h2>Envíanos tu idea</h2>
                <p>Resolvemos tu solicitud en menos de 24 horas hábiles.</p>
            </div>
        </template>

        <form class="contact-form" @submit.prevent="$emit('submit')">
            <div class="contact-form__row">
                <label class="essential-contact-field">
                    <span class="essential-contact-field__label">Nombre y apellido *</span>
                    <InputText v-model="formData.name" type="text" autocomplete="name"
                        :class="{ 'has-error': !!errors.name }" placeholder="¿Cómo te llamas?"
                        class="essential-contact-field__input" />
                    <small v-if="errors.name" class="essential-contact-field__error">{{ errors.name }}</small>
                </label>
            </div>

            <div class="contact-form__row contact-form__row--split">
                <label class="essential-contact-field">
                    <span class="essential-contact-field__label">Correo electrónico *</span>
                    <InputText v-model="formData.email" type="email" autocomplete="email"
                        :class="{ 'has-error': !!errors.email }" placeholder="nombre@empresa.com"
                        class="essential-contact-field__input" />
                    <small v-if="errors.email" class="essential-contact-field__error">{{ errors.email }}</small>
                </label>

                <label class="essential-contact-field">
                    <span class="essential-contact-field__label">WhatsApp o teléfono</span>
                    <InputText v-model="formData.phone" type="tel" autocomplete="tel"
                        :class="{ 'has-error': !!errors.phone }" placeholder="+57 300 000 0000"
                        class="essential-contact-field__input" />
                    <small v-if="errors.phone" class="essential-contact-field__error">{{ errors.phone }}</small>
                </label>
            </div>

            <div class="contact-form__row contact-form__row--split">
                <label class="essential-contact-field">
                    <span class="essential-contact-field__label">Marca o empresa</span>
                    <InputText v-model="formData.company" type="text" autocomplete="organization"
                        :class="{ 'has-error': !!errors.company }" placeholder="Nombre de tu marca"
                        class="essential-contact-field__input" />
                    <small v-if="errors.company" class="essential-contact-field__error">{{ errors.company }}</small>
                </label>

                <label class="essential-contact-field">
                    <span class="essential-contact-field__label">Interés principal</span>
                    <select v-model="formData.subject" class="essential-contact-field__select"
                        :class="{ 'has-error': !!errors.subject }">
                        <option value="" disabled>Selecciona una opción</option>
                        <option v-for="option in interestOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                    <small v-if="errors.subject" class="essential-contact-field__error">{{ errors.subject }}</small>
                </label>
            </div>

            <div class="contact-form__row">
                <label class="essential-contact-field">
                    <span class="essential-contact-field__label">Cuéntanos más *</span>
                    <Textarea v-model="formData.message" rows="5" auto-resize
                        :class="{ 'has-error': !!errors.message }"
                        placeholder="Contexto, objetivos y cualquier dato clave que debamos conocer."
                        class="essential-contact-field__textarea" />
                    <small v-if="errors.message" class="essential-contact-field__error">{{ errors.message }}</small>
                </label>
            </div>

            <div class="contact-form__actions">
                <button type="button" class="essential-contact-cta essential-contact-cta--ghost"
                    @click="$emit('update:visible', false)" :disabled="submitting">
                    Cancelar
                </button>
                <button type="submit" class="essential-contact-cta essential-contact-cta--primary"
                    :disabled="submitting">
                    <span v-if="!submitting">Enviar mensaje</span>
                    <span v-else class="contact-form__spinner"></span>
                </button>
            </div>

            <p class="contact-form__privacy">
                Al continuar aceptas que usemos tus datos para responder a tu solicitud. Revisamos cada mensaje de
                forma
                manual.
            </p>
        </form>
    </Dialog>
</template>

<script setup>
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';

defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
    formData: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    interestOptions: {
        type: Array,
        required: true,
    },
    submitting: {
        type: Boolean,
        default: false,
    },
    breakpoints: {
        type: Object,
        default: () => ({
            '960px': '92vw',
            '640px': '98vw',
        }),
    },
});

defineEmits(['update:visible', 'hide', 'submit']);
</script>

<style scoped>
.contact-dialog :deep(.p-dialog-header) {
    padding-bottom: 0;
    border-bottom: none;
}

.contact-dialog__header {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.contact-dialog__header h2 {
    margin: 0;
    font-family: 'Lexend', sans-serif;
    font-size: clamp(24px, 4vw, 30px);
    text-transform: uppercase;
    color: var(--essential-heading-color);
}

.contact-dialog__header p {
    margin: 0;
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    color: rgba(23, 23, 23, 0.7);
}

body.dark-mode .contact-dialog__header p {
    color: rgba(243, 243, 243, 0.72);
}

.contact-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
    padding-top: 12px;
}

.contact-form__row {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.contact-form__row--split {
    gap: clamp(16px, 3vw, 24px);
}

@media (min-width: 720px) {
    .contact-form__row--split {
        flex-direction: row;
    }

    .contact-form__row--split .essential-contact-field {
        flex: 1;
    }
}

.contact-form__actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 8px;
}

@media (min-width: 640px) {
    .contact-form__actions {
        flex-direction: row;
        justify-content: flex-end;
    }
}

.contact-form__privacy {
    margin: 0;
    font-size: 12px;
    line-height: 1.6;
    color: rgba(23, 23, 23, 0.64);
    text-align: center;
}

body.dark-mode .contact-form__privacy {
    color: rgba(243, 243, 243, 0.64);
}

.contact-form__spinner {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(255, 255, 255, 0.35);
    border-top-color: #fff;
    border-radius: 50%;
    animation: contact-spin 0.8s linear infinite;
}

@keyframes contact-spin {
    to {
        transform: rotate(360deg);
    }
}
</style>

