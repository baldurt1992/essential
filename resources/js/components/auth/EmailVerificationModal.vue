<template>
    <Dialog v-model:visible="localVisible" :modal="true" :draggable="false" :closable="false" dismissable-mask
        class="email-verification-dialog" :style="dialogStyle" :breakpoints="{ '960px': '95vw', '640px': '98vw' }">
        <template #header>
            <div class="email-verification-modal__header">
                <h2 class="email-verification-modal__title">Verifica tu correo</h2>
            </div>
        </template>

        <div class="email-verification-modal">
            <EmailVerificationForm :email="email" :from-email="fromEmail" v-model:otp-input="otpInput"
                :otp-error="otpError" :verifying="verifying" :resending="resending" :countdown="countdown"
                :general-error="generalError" @verify="handleVerify" @resend="handleResend" />
        </div>

        <template #footer>
            <button type="button" class="email-verification-dialog__close" @click="closeModal" :disabled="verifying">
                Cerrar
            </button>
        </template>
    </Dialog>

    <EmailVerificationSuccessModal v-model="successModalVisible" @continue="handleSuccess" />
</template>

<script setup>
    import { computed, ref, watch } from 'vue';
    import Dialog from 'primevue/dialog';
    import { useEmailVerification } from '../../composables/useEmailVerification';
    import EmailVerificationForm from './forms/EmailVerificationForm.vue';
    import EmailVerificationSuccessModal from './modals/EmailVerificationSuccessModal.vue';

    const props = defineProps({
        modelValue: {
            type: Boolean,
            default: false,
        },
        email: {
            type: String,
            required: true,
        },
        fromEmail: {
            type: Boolean,
            default: false,
        },
    });

    const emit = defineEmits(['update:modelValue', 'verified']);

    const localVisible = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });

    const successModalVisible = ref(false);

    // Usar el composable para toda la lógica de verificación
    const emailRef = computed(() => props.email);
    const fromEmailRef = computed(() => props.fromEmail);

    const {
        otpInput,
        otpError,
        verifying,
        resending,
        countdown,
        generalError,
        verifyOtp,
        resendOtp,
        initializeCountdown,
    } = useEmailVerification(emailRef, fromEmailRef);

    const dialogStyle = {
        width: '520px',
        background: 'var(--qode-background-color)',
    };

    // Inicializar countdown cuando el modal se abre
    watch(() => props.modelValue, (visible) => {
        initializeCountdown(visible);
    });

    const handleVerify = async () => {
        const result = await verifyOtp();
        if (result.success) {
            // Cerrar modal de verificación
            localVisible.value = false;
            // Mostrar modal de éxito
            successModalVisible.value = true;
        }
    };

    const handleResend = async () => {
        await resendOtp();
    };

    const handleSuccess = () => {
        successModalVisible.value = false;
        emit('verified');
    };

    const closeModal = () => {
        localVisible.value = false;
    };
</script>

<style scoped>

    .email-verification-modal,
    .email-verification-modal * {
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

    .email-verification-dialog :deep(.p-dialog) {
        background: #ffffff;
        color: #171717;
        border-radius: 28px;
        box-shadow: 0 28px 80px rgba(0, 0, 0, 0.28);
        border: 1px solid rgba(0, 0, 0, 0.06);
    }

    body.dark-mode .email-verification-dialog :deep(.p-dialog) {
        background: #101010;
        color: #f3f3f3;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .email-verification-dialog :deep(.p-dialog-header) {
        border: none;
        padding: 30px 36px 0;
        background: transparent;
    }

    .email-verification-dialog :deep(.p-dialog-content) {
        border: none;
        padding: 26px 36px 34px;
        background: transparent;
    }

    .email-verification-dialog :deep(.p-dialog-footer) {
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

    @media (max-width: 640px) {

        .email-verification-dialog :deep(.p-dialog-header),
        .email-verification-dialog :deep(.p-dialog-content),
        .email-verification-dialog :deep(.p-dialog-footer) {
            padding-left: 22px;
            padding-right: 22px;
        }
    }
</style>
