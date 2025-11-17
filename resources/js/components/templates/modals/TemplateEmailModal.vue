<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)" modal class="template-email-dialog"
        :style="{ width: '90%', maxWidth: '500px' }" :closable="true" :draggable="false">
        <template #header>
            <h2 class="template-email-modal__title">Completa tu compra</h2>
        </template>

        <div class="template-email-modal__content">
            <p class="template-email-modal__message">
                Para continuar con la compra, necesitamos tu correo electrónico. Te enviaremos el enlace de descarga
                una vez
                completado el pago.
            </p>
            <form @submit.prevent="$emit('submit', email)" class="template-email-modal__form">
                <div class="template-email-modal__field">
                    <InputText v-model="email" type="email" name="email" placeholder="tu@email.com" required
                        :disabled="isSubmitting" class="w-full" />
                </div>
                <Button type="submit" class="essential-button essential-button--primary"
                    :disabled="isSubmitting || !email">
                    <span v-if="!isSubmitting">Continuar al pago</span>
                    <span v-else>Procesando...</span>
                </Button>
            </form>
        </div>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

const props = defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
    isSubmitting: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['submit', 'update:visible']);

const email = ref('');

watch(() => props.visible, (newVal) => {
    if (!newVal) {
        email.value = '';
    }
});
</script>

<style scoped>
.template-email-modal__title {
    margin: 0;
    font-family: 'Lexend', sans-serif;
    font-size: clamp(24px, 5vw, 32px);
    font-weight: 400;
    text-transform: uppercase;
    color: var(--essential-heading-color);
}

.template-email-modal__content {
    padding: 20px 0;
}

.template-email-modal__message {
    margin: 0 0 24px;
    font-family: 'Inter', sans-serif;
    font-size: 16px;
    line-height: 1.6;
    color: var(--essential-text-color);
}

.template-email-modal__form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.template-email-modal__field {
    width: 100%;
}

.template-email-modal__field :deep(.p-inputtext) {
    width: 100%;
    border: none;
    border-bottom: 1px solid rgba(23, 23, 23, 0.6);
    background: transparent;
    font-family: 'Inter', sans-serif;
    font-size: 16px;
    color: var(--essential-text-color);
    padding: 12px 16px;
    border-radius: 0;
    box-shadow: none;
}

body.dark-mode .template-email-modal__field :deep(.p-inputtext) {
    border-bottom-color: rgba(255, 255, 255, 0.6);
}

.template-email-modal__field :deep(.p-inputtext:focus) {
    border-bottom-color: var(--essential-text-color);
    box-shadow: none;
}

.template-email-modal__field :deep(.p-inputtext::placeholder) {
    color: rgba(23, 23, 23, 0.8);
    font-family: 'Inter', sans-serif;
}

body.dark-mode .template-email-modal__field :deep(.p-inputtext::placeholder) {
    color: rgba(255, 255, 255, 0.8);
}
</style>

