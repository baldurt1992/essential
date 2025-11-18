<template>
    <Dialog v-model:visible="localVisible" :modal="true" :draggable="false" :closable="true" dismissable-mask
        class="password-recovery-dialog" :style="dialogStyle" :breakpoints="{ '960px': '95vw', '640px': '98vw' }"
        @hide="handleClose">
        <template #header>
            <div class="password-recovery-modal__header">
                <h2 class="password-recovery-modal__title">Recuperar contraseña</h2>
            </div>
        </template>

        <div class="password-recovery-modal">
            <!-- Step 1: Email -->
            <div v-if="currentStep === 'email'" class="password-recovery-modal__content">
                <Message v-if="generalError" severity="error" class="password-recovery-modal__general-error">
                    {{ generalError }}
                </Message>
                <PasswordRecoveryEmailForm :email="email" :error="emailError" :loading="sendingEmail"
                    @submit="handleSendCode" @update:email="(val) => email = val" />
            </div>

            <!-- Step 2: OTP -->
            <div v-else-if="currentStep === 'otp'" class="password-recovery-modal__content">
                <PasswordRecoveryOtpForm :email="email" :otp-input="otpInput" :otp-error="otpError"
                    :verifying="verifying" :resending="resending" :countdown="countdown" :general-error="generalError"
                    @verify="handleVerify" @resend="handleResend" @update:otp-input="(val) => otpInput = val" />
            </div>

            <!-- Step 3: New Password -->
            <div v-else-if="currentStep === 'new-password'" class="password-recovery-modal__content">
                <PasswordRecoveryNewPasswordForm :new-password="newPassword" :confirm-password="confirmPassword"
                    :password-error="passwordError" :confirm-password-error="confirmPasswordError" :loading="updating"
                    @submit="handleUpdatePassword" @update:new-password="(val) => newPassword = val"
                    @update:confirm-password="(val) => confirmPassword = val" />
            </div>
        </div>

        <template #footer>
            <button type="button" class="password-recovery-dialog__close" @click="handleClose"
                :disabled="sendingEmail || verifying || updating">
                Cerrar
            </button>
        </template>
    </Dialog>

    <PasswordRecoverySuccessModal v-model="successModalVisible" @continue="handleSuccess" />
</template>

<script setup>
    import { computed, ref, watch } from 'vue';
    import Dialog from 'primevue/dialog';
    import Message from 'primevue/message';
    import { usePasswordRecovery } from '../../composables/usePasswordRecovery';
    import PasswordRecoveryEmailForm from './forms/PasswordRecoveryEmailForm.vue';
    import PasswordRecoveryOtpForm from './forms/PasswordRecoveryOtpForm.vue';
    import PasswordRecoveryNewPasswordForm from './forms/PasswordRecoveryNewPasswordForm.vue';
    import PasswordRecoverySuccessModal from './modals/PasswordRecoverySuccessModal.vue';

    const props = defineProps({
        modelValue: {
            type: Boolean,
            default: false,
        },
    });

    const emit = defineEmits(['update:modelValue']);

    const localVisible = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });

    const successModalVisible = ref(false);

    const {
        email,
        emailError,
        sendingEmail,
        otpInput,
        otpError,
        verifying,
        resending,
        countdown,
        newPassword,
        confirmPassword,
        passwordError,
        confirmPasswordError,
        updating,
        currentStep,
        generalError,
        resetForm,
        sendRecoveryCode,
        verifyOtp,
        resendOtp,
        updatePassword,
        initializeCountdown,
    } = usePasswordRecovery();

    const dialogStyle = {
        width: '520px',
        background: 'var(--qode-background-color)',
    };

    watch(() => props.modelValue, (visible) => {
        if (visible) {
            resetForm();
            initializeCountdown(visible);
        }
    });

    watch(() => currentStep.value, (step) => {
        if (step === 'otp') {
            initializeCountdown(true);
        }
    });

    const handleSendCode = async () => {
        await sendRecoveryCode();
    };

    const handleVerify = async () => {
        await verifyOtp();
    };

    const handleResend = async () => {
        await resendOtp();
    };

    const handleUpdatePassword = async () => {
        const result = await updatePassword();
        if (result.success) {
            successModalVisible.value = true;
        }
    };

    const handleSuccess = () => {
        successModalVisible.value = false;
        localVisible.value = false;
        resetForm();
        emit('update:modelValue', false);
        // Cerrar también el modal de auth para que el usuario pueda iniciar sesión
        // El componente padre (AuthModal) puede escuchar este evento si es necesario
    };

    const handleClose = () => {
        if (!sendingEmail.value && !verifying.value && !updating.value) {
            localVisible.value = false;
            resetForm();
        }
    };
</script>

<style scoped>

    .password-recovery-modal,
    .password-recovery-modal * {
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

    .password-recovery-dialog :deep(.p-dialog) {
        background: #ffffff;
        color: #171717;
        border-radius: 28px;
        box-shadow: 0 28px 80px rgba(0, 0, 0, 0.28);
        border: 1px solid rgba(0, 0, 0, 0.06);
    }

    body.dark-mode .password-recovery-dialog :deep(.p-dialog) {
        background: #101010;
        color: #f3f3f3;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .password-recovery-dialog :deep(.p-dialog-header) {
        border: none;
        padding: 30px 36px 0;
        background: transparent;
    }

    .password-recovery-dialog :deep(.p-dialog-content) {
        border: none;
        padding: 26px 36px 34px;
        background: transparent;
    }

    .password-recovery-dialog :deep(.p-dialog-footer) {
        border: none;
        padding: 0 36px 26px;
        background: transparent;
    }

    .password-recovery-modal__header {
        text-align: center;
    }

    .password-recovery-modal__title {
        font-family: 'IBM Plex Mono', sans-serif;
        font-size: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin: 0;
    }

    .password-recovery-modal__content {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
    }

    .password-recovery-modal__general-error :deep(.p-message) {
        width: 100%;
        border-radius: 12px;
        padding: 14px 16px;
        background: rgba(221, 51, 51, 0.12);
        border: 1px solid rgba(221, 51, 51, 0.24);
    }

    body.dark-mode .password-recovery-modal__general-error :deep(.p-message) {
        background: rgba(221, 51, 51, 0.22);
        border-color: rgba(221, 51, 51, 0.36);
    }

    .password-recovery-dialog__close {
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

    .password-recovery-dialog__close:hover:not(:disabled) {
        color: #DD3333;
    }

    .password-recovery-dialog__close:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    @media (max-width: 640px) {

        .password-recovery-dialog :deep(.p-dialog-header),
        .password-recovery-dialog :deep(.p-dialog-content),
        .password-recovery-dialog :deep(.p-dialog-footer) {
            padding-left: 22px;
            padding-right: 22px;
        }
    }
</style>
