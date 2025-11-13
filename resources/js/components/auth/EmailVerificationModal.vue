<template>
    <Dialog v-model:visible="localVisible" :modal="true" :draggable="false" :closable="false" dismissable-mask
        class="email-verification-dialog" :style="dialogStyle" :breakpoints="{ '960px': '95vw', '640px': '98vw' }">
        <template #header>
            <div class="email-verification-modal__header">
                <h2 class="email-verification-modal__title">Verifica tu correo</h2>
            </div>
        </template>

        <div class="email-verification-modal">
            <div class="email-verification-dialog__content">
                <Message v-if="generalError" severity="error" class="email-verification-dialog__general-error">
                    {{ generalError }}
                </Message>

                <div class="email-verification-modal__message">
                    <p>Hemos enviado un código OTP de 6 dígitos a tu correo:</p>
                    <p class="email-verification-modal__email">{{ email }}</p>
                    <p>Por favor ingrésalo para terminar tu registro.</p>
                </div>

                <form class="email-verification-form" @submit.prevent="handleVerify">
                    <div class="email-verification-form__field">
                        <label class="email-verification-form__label" for="otp-input">Código de verificación</label>
                        <span class="email-verification-form__input-wrapper">
                            <InputText id="otp-input" v-model.trim="otpInput" type="text" maxlength="6"
                                placeholder="000000" autocomplete="one-time-code"
                                class="email-verification-form__input w-full" :class="{ 'p-invalid': otpError }"
                                @input="handleOtpInput" required />
                        </span>
                        <small v-if="otpError" class="email-verification-form__error">{{ otpError }}</small>
                    </div>

                    <Button type="submit" class="email-verification-form__submit" :loading="verifying"
                        label="Verificar" />
                </form>

                <div class="email-verification-modal__resend">
                    <p v-if="countdown > 0" class="email-verification-modal__countdown">
                        ¿No recibiste el código? Solicítalo de nuevo en <strong>{{ countdown }}</strong> segundos
                    </p>
                    <Button v-else type="button" class="email-verification-form__resend-button" :loading="resending"
                        label="Reenviar código" @click="handleResend" />
                </div>
            </div>
        </div>

        <template #footer>
            <button type="button" class="email-verification-dialog__close" @click="closeModal" :disabled="verifying">
                Cerrar
            </button>
        </template>
    </Dialog>

    <!-- Modal de éxito -->
    <Dialog v-model:visible="successModalVisible" :modal="true" :draggable="false" :closable="false" dismissable-mask
        class="success-dialog" :style="dialogStyle" :breakpoints="{ '960px': '95vw', '640px': '98vw' }">
        <template #header>
            <div class="success-modal__header">
                <h2 class="success-modal__title">¡Cuenta verificada!</h2>
            </div>
        </template>

        <div class="success-modal">
            <div class="success-modal__content">
                <p class="success-modal__message">Tu cuenta ha sido verificada exitosamente. ¡Comienza a explorar!</p>
            </div>
        </div>

        <template #footer>
            <Button type="button" class="success-modal__button" label="Continuar" @click="handleSuccess" />
        </template>
    </Dialog>
</template>

<script setup>
    import { ref, computed, watch, onBeforeUnmount } from 'vue';
    import axios from 'axios';
    import { useAuth } from '../../composables/useAuth';

    const props = defineProps({
        modelValue: {
            type: Boolean,
            default: false,
        },
        email: {
            type: String,
            required: true,
        },
    });

    const emit = defineEmits(['update:modelValue', 'verified']);

    const auth = useAuth();

    const localVisible = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });

    const otpInput = ref('');
    const otpError = ref(null);
    const verifying = ref(false);
    const resending = ref(false);
    const countdown = ref(60);
    const countdownInterval = ref(null);
    const successModalVisible = ref(false);
    const generalError = computed(() => auth.error.value);

    const dialogStyle = {
        width: '440px',
        background: 'var(--qode-background-color)',
    };

    watch(() => props.modelValue, (visible) => {
        if (visible) {
            startCountdown();
            resetForm();
            auth.clearErrors();
        } else {
            stopCountdown();
        }
    });

    function resetForm() {
        otpInput.value = '';
        otpError.value = null;
    }

    function handleOtpInput(event) {
        // Solo permitir números
        const value = event.target.value.replace(/\D/g, '');
        otpInput.value = value;
        otpError.value = null;
    }

    function startCountdown() {
        countdown.value = 60;
        stopCountdown();
        countdownInterval.value = setInterval(() => {
            if (countdown.value > 0) {
                countdown.value--;
            } else {
                stopCountdown();
            }
        }, 1000);
    }

    function stopCountdown() {
        if (countdownInterval.value) {
            clearInterval(countdownInterval.value);
            countdownInterval.value = null;
        }
    }

    async function handleVerify() {
        if (otpInput.value.length !== 6) {
            otpError.value = 'El código debe tener 6 dígitos';
            return;
        }

        verifying.value = true;
        otpError.value = null;
        auth.clearErrors();

        try {
            await axios.get('/sanctum/csrf-cookie');
            const response = await axios.post('/verify-otp', {
                email: props.email,
                otp: otpInput.value,
            });

            if (response.data.verified) {
                // Actualizar usuario en el store
                await auth.refreshUser();
                // Cerrar modal de verificación
                localVisible.value = false;
                // Mostrar modal de éxito
                successModalVisible.value = true;
            }
        } catch (error) {
            if (error.response?.status === 422) {
                const errors = error.response.data.errors ?? {};
                otpError.value = errors.otp?.[0] ?? 'El código de verificación es inválido o ha expirado.';
            } else {
                otpError.value = error.response?.data?.message ?? 'Ocurrió un error al verificar el código.';
            }
        } finally {
            verifying.value = false;
        }
    }

    async function handleResend() {
        if (countdown.value > 0) {
            return;
        }

        resending.value = true;
        auth.clearErrors();

        try {
            await axios.get('/sanctum/csrf-cookie');
            await axios.post('/resend-otp', {
                email: props.email,
            });

            startCountdown();
        } catch (error) {
            // El error se mostrará en generalError
        } finally {
            resending.value = false;
        }
    }

    function handleSuccess() {
        successModalVisible.value = false;
        emit('verified');
    }

    function closeModal() {
        localVisible.value = false;
    }

    onBeforeUnmount(() => {
        stopCountdown();
    });
</script>

<style scoped>

    .email-verification-modal,
    .email-verification-modal *,
    .success-modal,
    .success-modal * {
        font-family: 'Inter', sans-serif;
        text-transform: none;
        letter-spacing: normal;
        color: inherit;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    :deep(.p-dialog-mask) {
        backdrop-filter: blur(6px);
    }

    .email-verification-dialog :deep(.p-dialog),
    .success-dialog :deep(.p-dialog) {
        background: #ffffff;
        color: #171717;
        border-radius: 28px;
        box-shadow: 0 28px 80px rgba(0, 0, 0, 0.28);
        border: 1px solid rgba(0, 0, 0, 0.06);
    }

    body.dark-mode .email-verification-dialog :deep(.p-dialog),
    body.dark-mode .success-dialog :deep(.p-dialog) {
        background: #101010;
        color: #f3f3f3;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .email-verification-dialog :deep(.p-dialog-header),
    .success-dialog :deep(.p-dialog-header) {
        border: none;
        padding: 30px 36px 0;
        background: transparent;
    }

    .email-verification-dialog :deep(.p-dialog-content),
    .success-dialog :deep(.p-dialog-content) {
        border: none;
        padding: 26px 36px 34px;
        background: transparent;
    }

    .email-verification-dialog :deep(.p-dialog-footer),
    .success-dialog :deep(.p-dialog-footer) {
        border: none;
        padding: 0 36px 26px;
        background: transparent;
    }

    .email-verification-modal__header {
        text-align: center;
    }

    .email-verification-modal__title {
        font-family: 'IBM Plex Mono', sans-serif;
        font-size: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin: 0;
    }

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
    }

    .email-verification-form__input :deep(.p-inputtext) {
        width: 100%;
        height: 48px;
        padding: 0 16px;
        border-radius: 14px;
        border: 1px solid rgba(18, 18, 18, 0.15);
        background: #f4f6fb;
        font-family: 'Courier New', monospace;
        font-size: 24px;
        font-weight: bold;
        letter-spacing: 8px;
        text-align: center;
        transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
    }

    body.dark-mode .email-verification-form__input :deep(.p-inputtext) {
        border-color: rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.08);
    }

    .email-verification-form__input :deep(.p-inputtext:focus) {
        border-color: #DD3333;
        background: rgba(221, 51, 51, 0.08);
        box-shadow: 0 0 0 3px rgba(221, 51, 51, 0.15);
    }

    .email-verification-form__input.p-invalid :deep(.p-inputtext) {
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

    .email-verification-dialog__close {
        background: transparent;
        border: none;
        font-family: 'IBM Plex Mono', sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        font-size: 12px;
        cursor: pointer;
        color: inherit;
        transition: color 0.2s ease;
    }

    .email-verification-dialog__close:hover {
        color: #DD3333;
    }

    /* Success Modal */
    .success-modal__header {
        text-align: center;
    }

    .success-modal__title {
        font-family: 'IBM Plex Mono', sans-serif;
        font-size: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin: 0;
        color: #DD3333;
    }

    .success-modal__content {
        text-align: center;
    }

    .success-modal__message {
        font-size: 16px;
        line-height: 1.6;
        margin: 0;
    }

    .success-modal__button :deep(.p-button),
    :deep(.success-modal__button.p-button) {
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

    .success-modal__button :deep(.p-button:hover) {
        background-color: #c42b2b !important;
        border-color: #c42b2b !important;
        transform: translateY(-1px);
    }

    @media (max-width: 640px) {

        .email-verification-dialog :deep(.p-dialog-header),
        .email-verification-dialog :deep(.p-dialog-content),
        .email-verification-dialog :deep(.p-dialog-footer),
        .success-dialog :deep(.p-dialog-header),
        .success-dialog :deep(.p-dialog-content),
        .success-dialog :deep(.p-dialog-footer) {
            padding-left: 22px;
            padding-right: 22px;
        }
    }
</style>
