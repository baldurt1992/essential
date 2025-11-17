<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)" modal
        :style="{ width: '90%', maxWidth: '500px' }" :closable="true" :draggable="false">
        <template #header>
            <h2 class="auth-modal__title">Cuenta requerida</h2>
        </template>

        <div class="auth-modal__content">
            <p class="auth-modal__message">
                Necesitas tener una cuenta para poder comprar un plan de suscripción.
            </p>
        </div>

        <template #footer>
            <div class="auth-modal__actions">
                <button type="button" class="auth-nav-button auth-nav-button--ghost" @click="$emit('open-login')">
                    Iniciar sesión
                </button>
                <button type="button" class="auth-nav-button auth-nav-button--primary" @click="$emit('open-register')">
                    Registrarse
                </button>
            </div>
        </template>
    </Dialog>
</template>

<script setup>
import Dialog from 'primevue/dialog';

defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
});

defineEmits(['update:visible', 'open-login', 'open-register']);
</script>

<style scoped>
.auth-modal__title {
    margin: 0;
    font-family: 'Lexend', sans-serif;
    font-size: clamp(24px, 5vw, 32px);
    font-weight: 400;
    text-transform: uppercase;
    color: var(--essential-heading-color);
}

.auth-modal__content {
    padding: 20px 0;
}

.auth-modal__message {
    margin: 0;
    font-family: 'Inter', sans-serif;
    font-size: 16px;
    line-height: 1.6;
    color: var(--essential-text-color);
}

.auth-modal__actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.auth-nav-button {
    font-family: 'IBM Plex Mono', sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 13px;
    padding: 10px 18px;
    border-radius: 999px;
    border: 1px solid var(--essential-text-color);
    background: transparent;
    color: var(--essential-text-color);
    cursor: pointer;
    transition: background 0.25s ease, color 0.25s ease, border-color 0.25s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.auth-nav-button--primary {
    background-color: #DD3333;
    border-color: #DD3333;
    color: #ffffff;
}

.auth-nav-button--primary:hover {
    background-color: #c42b2b;
    border-color: #c42b2b;
}

.auth-nav-button--ghost:hover {
    background-color: var(--essential-heading-color);
    color: var(--essential-background-color);
}

body.dark-mode .auth-nav-button {
    border-color: var(--essential-border-color);
    color: var(--essential-text-color);
}

body.dark-mode .auth-nav-button--ghost:hover {
    background-color: #ffffff;
    color: #171717;
}

body.dark-mode .auth-nav-button--primary {
    border-color: #ffffff;
}

@media (max-width: 480px) {
    .auth-modal__actions {
        flex-direction: column;
    }

    .auth-nav-button {
        width: 100%;
    }
}
</style>

