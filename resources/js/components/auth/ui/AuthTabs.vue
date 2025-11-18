<template>
    <div class="auth-modal__header">
        <button type="button" class="auth-dialog__tab"
            :class="{ 'auth-dialog__tab--active': currentForm === 'login' }" @click="handleSwitch('login')">
            Iniciar sesión
        </button>
        <button type="button" class="auth-dialog__tab"
            :class="{ 'auth-dialog__tab--active': currentForm === 'register' }" @click="handleSwitch('register')">
            Crear cuenta
        </button>
    </div>
</template>

<script setup>
const props = defineProps({
    currentForm: {
        type: String,
        required: true,
        validator: (value) => ['login', 'register'].includes(value),
    },
});

const emit = defineEmits(['switch']);

const handleSwitch = (form) => {
    emit('switch', form);
};
</script>

<style scoped>
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

@media (max-width: 640px) {
    .auth-dialog__tab {
        font-size: 13px;
        padding: 10px 0;
    }
}
</style>

