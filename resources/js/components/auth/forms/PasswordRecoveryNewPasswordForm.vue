<template>
    <form class="auth-form" @submit.prevent="handleSubmit">
        <div class="auth-form__field">
            <label class="auth-form__label" for="new-password">Nueva contraseña</label>
            <span class="auth-form__input-wrapper">
                <Password id="new-password" :model-value="newPassword" @update:model-value="$emit('update:new-password', $event)" toggle-mask :feedback="false"
                    autocomplete="new-password" class="auth-form__input w-full" input-class="auth-form__password-input"
                    :pt="passwordPT" :disabled="loading" required />
            </span>
            <small v-if="passwordError" class="auth-form__error">{{ passwordError }}</small>
        </div>

        <div class="auth-form__field">
            <label class="auth-form__label" for="confirm-password">Confirmar contraseña</label>
            <span class="auth-form__input-wrapper">
                <Password id="confirm-password" :model-value="confirmPassword" @update:model-value="$emit('update:confirm-password', $event)" toggle-mask :feedback="false"
                    autocomplete="new-password" class="auth-form__input w-full" input-class="auth-form__password-input"
                    :pt="passwordPT" :disabled="loading" required />
            </span>
            <small v-if="confirmPasswordError" class="auth-form__error">{{ confirmPasswordError }}</small>
        </div>

        <Button type="submit" class="auth-form__submit" :loading="loading" label="Actualizar contraseña" />
    </form>
</template>

<script setup>
import Password from 'primevue/password';
import Button from 'primevue/button';

const props = defineProps({
    newPassword: {
        type: String,
        required: true,
    },
    confirmPassword: {
        type: String,
        required: true,
    },
    passwordError: {
        type: String,
        default: null,
    },
    confirmPasswordError: {
        type: String,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['submit', 'update:new-password', 'update:confirm-password']);

const passwordPT = {
    root: { class: 'auth-password' },
    input: { class: 'auth-password__input' },
    panel: { class: 'auth-password__panel' },
    toggleMask: { class: 'auth-password__toggle' },
};

const handleSubmit = () => {
    emit('submit');
};
</script>

<style scoped>
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
</style>

