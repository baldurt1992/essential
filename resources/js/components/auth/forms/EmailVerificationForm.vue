<template>
    <div class="email-verification-dialog__content">
        <Message v-if="generalError" severity="error" class="email-verification-dialog__general-error">
            {{ generalError }}
        </Message>

        <div class="email-verification-modal__message">
            <p>Hemos enviado un código OTP de 6 dígitos a tu correo:</p>
            <p class="email-verification-modal__email">{{ email }}</p>
            <p>Por favor ingrésalo para terminar tu registro.</p>
        </div>

        <form class="email-verification-form" @submit.prevent="handleSubmit">
            <div class="email-verification-form__field">
                <label class="email-verification-form__label" for="otp-input">Código de verificación</label>
                <div class="email-verification-form__input-wrapper">
                    <InputOtp :model-value="otpInput" @update:model-value="$emit('update:otp-input', $event)" :length="6"
                        :disabled="verifying" class="email-verification-form__input"
                        :class="{ 'p-invalid': otpError }" />
                </div>
                <small v-if="otpError" class="email-verification-form__error">{{ otpError }}</small>
            </div>

            <Button type="submit" class="email-verification-form__submit" :loading="verifying" label="Verificar" />
        </form>

        <div class="email-verification-modal__resend">
            <p v-if="!fromEmail && countdown > 0" class="email-verification-modal__countdown">
                ¿No recibiste el código? Solicítalo de nuevo en <strong>{{ countdown }}</strong> segundos
            </p>
            <Button v-else-if="!fromEmail || countdown === 0" type="button"
                class="email-verification-form__resend-button" :loading="resending" label="Reenviar código"
                @click="handleResend" />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Message from 'primevue/message';
import InputOtp from 'primevue/inputotp';
import Button from 'primevue/button';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    fromEmail: {
        type: Boolean,
        default: false,
    },
    otpInput: {
        type: String,
        required: true,
    },
    otpError: {
        type: String,
        default: null,
    },
    verifying: {
        type: Boolean,
        default: false,
    },
    resending: {
        type: Boolean,
        default: false,
    },
    countdown: {
        type: Number,
        default: 0,
    },
    generalError: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['verify', 'resend', 'update:otp-input']);

const handleSubmit = () => {
    emit('verify');
};

const handleResend = () => {
    emit('resend');
};
</script>

<style scoped>
.email-verification-dialog__content {
    display: flex;
    flex-direction: column;
    gap: 24px;
    width: 100%;
}

.email-verification-dialog__general-error :deep(.p-message) {
    width: 100%;
    border-radius: 12px;
    padding: 14px 16px;
    background: rgba(221, 51, 51, 0.12);
    border: 1px solid rgba(221, 51, 51, 0.24);
}

body.dark-mode .email-verification-dialog__general-error :deep(.p-message) {
    background: rgba(221, 51, 51, 0.22);
    border-color: rgba(221, 51, 51, 0.36);
}

.email-verification-modal__message {
    text-align: center;
    font-size: 15px;
    line-height: 1.6;
}

.email-verification-modal__email {
    font-weight: 600;
    color: #DD3333;
    margin: 8px 0;
}

.email-verification-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
    width: 100%;
}

.email-verification-form__field {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
}

.email-verification-form__label {
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: inherit;
}

.email-verification-form__input-wrapper {
    position: relative;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.email-verification-form__input :deep(.p-inputotp) {
    display: flex;
    justify-content: center;
    gap: 12px;
    width: 100%;
}

.email-verification-form__input :deep(.p-inputotp-input) {
    width: 48px;
    height: 56px;
    border-radius: 14px;
    border: 1px solid rgba(18, 18, 18, 0.15);
    background: #f4f6fb;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 24px;
    font-weight: 600;
    text-align: center;
    transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
}

body.dark-mode .email-verification-form__input :deep(.p-inputotp-input) {
    border-color: rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.08);
    color: #ffffff;
}

.email-verification-form__input :deep(.p-inputotp-input:focus) {
    border-color: #DD3333;
    background: rgba(221, 51, 51, 0.08);
    box-shadow: 0 0 0 3px rgba(221, 51, 51, 0.15);
    outline: none;
}

.email-verification-form__input.p-invalid :deep(.p-inputotp-input) {
    border-color: #DD3333;
}

.email-verification-form__error {
    font-size: 13px;
    color: #DD3333;
}

.email-verification-form__submit :deep(.p-button),
:deep(.email-verification-form__submit.p-button) {
    width: 100%;
    height: 52px;
    border-radius: 16px;
    background-color: #DD3333 !important;
    border: 1px solid #DD3333 !important;
    font-family: 'IBM Plex Mono', sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-weight: 600;
    color: #ffffff !important;
    transition: background 0.2s ease, transform 0.2s ease;
}

.email-verification-form__submit :deep(.p-button:hover),
:deep(.email-verification-form__submit.p-button:hover) {
    background-color: #c42b2b !important;
    border-color: #c42b2b !important;
    transform: translateY(-1px);
}

.email-verification-modal__resend {
    text-align: center;
}

.email-verification-modal__countdown {
    font-size: 14px;
    color: inherit;
    opacity: 0.7;
    min-height: 24px;
    display: inline-block;
}

.email-verification-modal__countdown strong {
    display: inline-block;
    min-width: 24px;
    text-align: center;
}

.email-verification-form__resend-button :deep(.p-button),
:deep(.email-verification-form__resend-button.p-button) {
    background: transparent !important;
    border: 1px solid rgba(18, 18, 18, 0.15) !important;
    color: inherit !important;
    font-family: 'IBM Plex Mono', sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 13px;
    padding: 10px 18px;
}

body.dark-mode .email-verification-form__resend-button :deep(.p-button) {
    border-color: rgba(255, 255, 255, 0.2) !important;
}

.email-verification-form__resend-button :deep(.p-button:hover) {
    background-color: rgba(221, 51, 51, 0.1) !important;
    border-color: #DD3333 !important;
    color: #DD3333 !important;
}
</style>

