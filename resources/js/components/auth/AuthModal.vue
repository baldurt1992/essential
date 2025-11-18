<template>
    <Dialog v-model:visible="localVisible" :modal="true" :draggable="false" :closable="false" dismissable-mask
        class="auth-dialog" :style="dialogStyle" :breakpoints="{ '960px': '95vw', '640px': '98vw' }">
        <template #header>
            <AuthTabs :current-form="currentForm" @switch="switchForm" />
        </template>

        <div class="auth-modal">
            <div class="auth-dialog__content">
                <Message v-if="generalError" severity="error" class="auth-dialog__general-error">
                    {{ generalError }}
                </Message>

                <LoginForm v-if="currentForm === 'login'" :form="loginForm" :errors="loginErrors" :loading="loading"
                    @submit="handleLogin" @recover-password="openPasswordRecovery" />

                <RegisterForm v-else :form="registerForm" :errors="registerErrors" :loading="loading"
                    @submit="handleRegister" />
            </div>
        </div>

        <template #footer>
            <button type="button" class="auth-dialog__close" @click="closeModal" :disabled="loading">
                Cerrar
            </button>
        </template>
    </Dialog>

    <EmailVerificationModal :model-value="emailVerificationModalVisible"
        @update:model-value="(val) => { if (!val) closeEmailVerificationModal(); }"
        :email="emailVerificationEmail || registeredEmail" :from-email="emailVerificationFromEmail"
        @verified="handleEmailVerified" />

    <PasswordRecoveryModal v-model="passwordRecoveryModalVisible"
        @update:model-value="(val) => { if (!val) passwordRecoveryModalVisible = false; }" />
</template>

<script setup>
    import { computed, ref, watch } from 'vue';
    import Dialog from 'primevue/dialog';
    import Message from 'primevue/message';
    import { useAuth } from '../../composables/useAuth';
    import { useAuthModal } from '../../composables/useAuthModal';
    import { useAuthForms } from '../../composables/useAuthForms';
    import AuthTabs from './ui/AuthTabs.vue';
    import LoginForm from './forms/LoginForm.vue';
    import RegisterForm from './forms/RegisterForm.vue';
    import EmailVerificationModal from './EmailVerificationModal.vue';
    import PasswordRecoveryModal from './PasswordRecoveryModal.vue';

    const props = defineProps({
        modelValue: {
            type: Boolean,
            default: false,
        },
        form: {
            type: String,
            default: 'login',
            validator: (value) => ['login', 'register'].includes(value),
        },
    });

    const emit = defineEmits(['update:modelValue', 'update:form', 'success']);

    const auth = useAuth();
    const { emailVerificationModalVisible, emailVerificationEmail, emailVerificationFromEmail, closeEmailVerificationModal, openEmailVerificationModal } = useAuthModal();
    const registeredEmail = ref('');
    const passwordRecoveryModalVisible = ref(false);

    // Usar el composable para manejar los formularios
    const {
        loginForm,
        registerForm,
        loading,
        generalError,
        loginErrors,
        registerErrors,
        resetForms,
        clearErrors,
    } = useAuthForms();

    const localVisible = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });

    const currentForm = computed({
        get: () => props.form,
        set: (value) => emit('update:form', value),
    });

    const dialogStyle = {
        width: '440px',
        background: 'var(--qode-background-color)',
    };

    watch(() => props.modelValue, (visible) => {
        if (!visible) {
            resetForms();
            clearErrors();
        }
    });

    const switchForm = (targetForm) => {
        currentForm.value = targetForm;
        clearErrors();
    };

    const handleLogin = async () => {
        const success = await auth.login({
            email: loginForm.email,
            password: loginForm.password,
        });

        if (success) {
            emit('success', 'login');
            closeModal();
        }
    };

    const handleRegister = async () => {
        const result = await auth.register({
            name: registerForm.name,
            email: registerForm.email,
            password: registerForm.password,
            password_confirmation: registerForm.password_confirmation,
        });

        if (result.success) {
            // Mostrar modal de verificación de email
            registeredEmail.value = result.email;
            // Usar el estado global para abrir el modal de verificación
            openEmailVerificationModal(result.email);
            closeModal();
        }
    };

    const handleEmailVerified = () => {
        closeEmailVerificationModal();
        registeredEmail.value = '';
        emit('success', 'register');
    };

    const openPasswordRecovery = () => {
        passwordRecoveryModalVisible.value = true;
    };

    const closeModal = () => {
        emit('update:modelValue', false);
    };
</script>

<style scoped>

    .auth-modal,
    .auth-modal * {
        font-family: 'Inter', sans-serif;
        text-transform: none;
        letter-spacing: normal;
        color: inherit;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .auth-modal {
        width: 100%;
    }

    .auth-modal strong,
    .auth-modal b {
        font-weight: 600;
    }

    :deep(.p-dialog-mask) {
        backdrop-filter: blur(6px);
    }

    .auth-dialog :deep(.p-dialog) {
        background: #ffffff;
        color: #171717;
        border-radius: 28px;
        box-shadow: 0 28px 80px rgba(0, 0, 0, 0.28);
        border: 1px solid rgba(0, 0, 0, 0.06);
    }

    body.dark-mode .auth-dialog :deep(.p-dialog) {
        background: #101010;
        color: #f3f3f3;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .auth-dialog :deep(.p-dialog-header) {
        border: none;
        padding: 30px 36px 0;
        background: transparent;
    }

    .auth-dialog :deep(.p-dialog-content) {
        border: none;
        padding: 26px 36px 34px;
        background: transparent;
    }

    .auth-dialog :deep(.p-dialog-footer) {
        border: none;
        padding: 0 36px 26px;
        background: transparent;
    }

    .auth-dialog__content {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
    }

    .auth-dialog__general-error :deep(.p-message) {
        width: 100%;
        border-radius: 12px;
        padding: 14px 16px;
        background: rgba(221, 51, 51, 0.12);
        border: 1px solid rgba(221, 51, 51, 0.24);
    }

    body.dark-mode .auth-dialog__general-error :deep(.p-message) {
        background: rgba(221, 51, 51, 0.22);
        border-color: rgba(221, 51, 51, 0.36);
    }

    .auth-dialog__close {
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

    .auth-dialog__close:hover {
        color: #DD3333;
    }

    @media (max-width: 640px) {

        .auth-dialog :deep(.p-dialog-header),
        .auth-dialog :deep(.p-dialog-content),
        .auth-dialog :deep(.p-dialog-footer) {
            padding-left: 22px;
            padding-right: 22px;
        }
    }
</style>
