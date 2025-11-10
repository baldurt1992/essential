<template>
    <Dialog v-model:visible="localVisible" :modal="true" :draggable="false" :closable="false" dismissable-mask
        class="auth-dialog" :style="dialogStyle" :breakpoints="{ '960px': '95vw', '640px': '98vw' }">
        <template #header>
            <div class="auth-modal__header">
                <button type="button" class="auth-dialog__tab"
                    :class="{ 'auth-dialog__tab--active': currentForm === 'login' }" @click="switchForm('login')">
                    Iniciar sesión
                </button>
                <button type="button" class="auth-dialog__tab"
                    :class="{ 'auth-dialog__tab--active': currentForm === 'register' }" @click="switchForm('register')">
                    Crear cuenta
                </button>
            </div>
        </template>

        <div class="auth-modal">
            <div class="auth-dialog__content">
                <Message v-if="generalError" severity="error" class="auth-dialog__general-error">
                    {{ generalError }}
                </Message>

                <form v-if="currentForm === 'login'" class="auth-form" @submit.prevent="handleLogin">
                    <div class="auth-form__field">
                        <label class="auth-form__label" for="login-email">Correo electrónico</label>
                        <span class="auth-form__input-wrapper">
                            <InputText id="login-email" v-model.trim="loginForm.email" type="email"
                                placeholder="tucorreo@dominio.com" autocomplete="email" class="auth-form__input w-full"
                                required />
                        </span>
                        <small v-if="loginErrors.email" class="auth-form__error">{{ loginErrors.email }}</small>
                    </div>

                    <div class="auth-form__field">
                        <label class="auth-form__label" for="login-password">Contraseña</label>
                        <span class="auth-form__input-wrapper">
                            <Password id="login-password" v-model="loginForm.password" toggle-mask :feedback="false"
                                autocomplete="current-password" class="auth-form__input w-full"
                                input-class="auth-form__password-input" :pt="passwordPT" required />
                        </span>
                        <small v-if="loginErrors.password" class="auth-form__error">{{ loginErrors.password }}</small>
                    </div>

                    <Button type="submit" class="auth-form__submit" :loading="loading" label="Ingresar" />
                </form>

                <form v-else class="auth-form" @submit.prevent="handleRegister">
                    <div class="auth-form__field">
                        <label class="auth-form__label" for="register-name">Nombre completo</label>
                        <span class="auth-form__input-wrapper">
                            <InputText id="register-name" v-model.trim="registerForm.name" type="text"
                                placeholder="Tu nombre" autocomplete="name" class="auth-form__input w-full" required />
                        </span>
                        <small v-if="registerErrors.name" class="auth-form__error">{{ registerErrors.name }}</small>
                    </div>

                    <div class="auth-form__field">
                        <label class="auth-form__label" for="register-email">Correo electrónico</label>
                        <span class="auth-form__input-wrapper">
                            <InputText id="register-email" v-model.trim="registerForm.email" type="email"
                                placeholder="tucorreo@dominio.com" autocomplete="email" class="auth-form__input w-full"
                                required />
                        </span>
                        <small v-if="registerErrors.email" class="auth-form__error">{{ registerErrors.email }}</small>
                    </div>

                    <div class="auth-form__field">
                        <label class="auth-form__label" for="register-password">Contraseña</label>
                        <span class="auth-form__input-wrapper">
                            <Password id="register-password" v-model="registerForm.password" toggle-mask
                                :feedback="false" autocomplete="new-password" class="auth-form__input w-full"
                                input-class="auth-form__password-input" :pt="passwordPT" required />
                        </span>
                        <small v-if="registerErrors.password" class="auth-form__error">{{ registerErrors.password
                        }}</small>
                    </div>

                    <div class="auth-form__field">
                        <label class="auth-form__label" for="register-password-confirmation">Confirmar
                            contraseña</label>
                        <span class="auth-form__input-wrapper">
                            <Password id="register-password-confirmation" v-model="registerForm.password_confirmation"
                                toggle-mask :feedback="false" autocomplete="new-password"
                                class="auth-form__input w-full" input-class="auth-form__password-input" :pt="passwordPT"
                                required />
                        </span>
                        <small v-if="registerErrors.password_confirmation" class="auth-form__error">{{
                            registerErrors.password_confirmation }}</small>
                    </div>

                    <Button type="submit" class="auth-form__submit" :loading="loading" label="Crear cuenta" />
                </form>
            </div>
        </div>

        <template #footer>
            <button type="button" class="auth-dialog__close" @click="closeModal" :disabled="loading">
                Cerrar
            </button>
        </template>
    </Dialog>
</template>

<script setup>
    import { computed, reactive, watch } from 'vue';
    import { useAuth } from '../../composables/useAuth';

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

    const localVisible = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value),
    });

    const currentForm = computed({
        get: () => props.form,
        set: (value) => emit('update:form', value),
    });

    const loginForm = reactive({
        email: '',
        password: '',
    });

    const registerForm = reactive({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const loading = computed(() => auth.loading.value);
    const generalError = computed(() => auth.error.value);

    const loginErrors = computed(() => ({
        email: auth.validationErrors.value.email?.[0] ?? null,
        password: auth.validationErrors.value.password?.[0] ?? null,
    }));

    const registerErrors = computed(() => ({
        name: auth.validationErrors.value.name?.[0] ?? null,
        email: auth.validationErrors.value.email?.[0] ?? null,
        password: auth.validationErrors.value.password?.[0] ?? null,
        password_confirmation: auth.validationErrors.value.password_confirmation?.[0] ?? null,
    }));

    const dialogStyle = {
        width: '440px',
        background: 'var(--qode-background-color)',
    };

    const passwordPT = {
        root: { class: 'auth-password' },
        input: { class: 'auth-password__input' },
        panel: { class: 'auth-password__panel' },
        toggleMask: { class: 'auth-password__toggle' },
    };

    watch(() => props.modelValue, (visible) => {
        if (!visible) {
            resetForms();
            auth.clearErrors();
        }
    });

    function switchForm(targetForm) {
        currentForm.value = targetForm;
        auth.clearErrors();
    }

    function resetForms() {
        loginForm.email = '';
        loginForm.password = '';
        registerForm.name = '';
        registerForm.email = '';
        registerForm.password = '';
        registerForm.password_confirmation = '';
    }

    async function handleLogin() {
        const success = await auth.login({
            email: loginForm.email,
            password: loginForm.password,
        });

        if (success) {
            emit('success', 'login');
            closeModal();
        }
    }

    async function handleRegister() {
        const success = await auth.register({
            name: registerForm.name,
            email: registerForm.email,
            password: registerForm.password,
            password_confirmation: registerForm.password_confirmation,
        });

        if (success) {
            emit('success', 'register');
            closeModal();
        }
    }

    function closeModal() {
        emit('update:modelValue', false);
    }
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

    .auth-modal__header {
        display: flex;
        gap: 10px;
        background: rgba(221, 51, 51, 0.08);
        border-radius: 14px;
        padding: 6px;
    }

    body.dark-mode .auth-modal__header {
        background: rgba(221, 51, 51, 0.16);
    }

    .auth-dialog__tab {
        flex: 1;
        background: transparent;
        border: none;
        border-radius: 10px;
        font-family: 'IBM Plex Mono', sans-serif;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 12px 0;
        cursor: pointer;
        transition: all 0.25s ease;
        color: inherit;
    }

    .auth-dialog__tab--active {
        background: #DD3333;
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(221, 51, 51, 0.35);
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

    .auth-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 100%;
    }

    .auth-form__field {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%;
    }

    .auth-form__label {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: inherit;
    }

    .auth-form__input-wrapper {
        position: relative;
        width: 100%;
    }

    .auth-form__input :deep(.p-inputtext),
    .auth-password__input :deep(.p-password-input) {
        width: 100%;
        height: 48px;
        padding: 0 16px;
        border-radius: 14px;
        border: 1px solid rgba(18, 18, 18, 0.15);
        background: #f4f6fb;
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
    }

    body.dark-mode .auth-form__input :deep(.p-inputtext),
    body.dark-mode .auth-password__input :deep(.p-password-input) {
        border-color: rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.08);
    }

    .auth-form__input :deep(.p-inputtext:focus),
    .auth-password__input :deep(.p-password-input:focus) {
        border-color: #DD3333;
        background: rgba(221, 51, 51, 0.08);
        box-shadow: 0 0 0 3px rgba(221, 51, 51, 0.15);
    }

    .auth-password__toggle :deep(.p-password-toggle-mask) {
        color: inherit;
    }

    .auth-form__error {
        font-size: 13px;
        color: #DD3333;
    }

    .auth-form__submit :deep(.p-button),
    :deep(.auth-form__submit.p-button) {
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

    .auth-form__submit :deep(.p-button:hover),
    :deep(.auth-form__submit.p-button:hover) {
        background-color: #c42b2b !important;
        border-color: #c42b2b !important;
        transform: translateY(-1px);
    }

    .auth-form__submit :deep(.p-button:focus-visible),
    :deep(.auth-form__submit.p-button:focus-visible) {
        box-shadow: 0 0 0 3px rgba(221, 51, 51, 0.22);
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

        .auth-dialog__tab {
            font-size: 13px;
            padding: 10px 0;
        }
    }
</style>
