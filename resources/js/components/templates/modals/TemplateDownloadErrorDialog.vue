<template>
    <Dialog :visible="visible" @update:visible="$emit('update:visible', $event)" modal class="download-error-dialog"
        :style="{ width: '90%', maxWidth: '500px' }" :closable="true" :draggable="false">
        <template #header>
            <h2 class="download-error-dialog__title">Límite alcanzado</h2>
        </template>

        <div class="download-error-dialog__content">
            <p class="download-error-dialog__message">{{ message }}</p>
        </div>
        <template #footer>
            <Button @click="$emit('update:visible', false)" class="essential-button essential-button--primary">
                Entendido
            </Button>
        </template>
    </Dialog>
</template>

<script setup>
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';

defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
    message: {
        type: String,
        required: true,
    },
});

defineEmits(['update:visible']);
</script>

<style scoped>
.download-error-dialog__title {
    margin: 0;
    font-family: 'Lexend', sans-serif;
    font-size: clamp(24px, 5vw, 32px);
    font-weight: 400;
    text-transform: uppercase;
    color: var(--essential-heading-color);
}

.download-error-dialog__content {
    padding: 20px 0;
}

.download-error-dialog__message {
    margin: 0;
    font-family: 'Inter', sans-serif;
    font-size: 16px;
    line-height: 1.6;
    color: var(--essential-text-color);
}

.download-error-dialog :deep(.p-dialog-footer) {
    padding-top: 20px;
    padding-bottom: 0;
}

.download-error-dialog :deep(.p-dialog-footer .essential-button) {
    margin: 0;
}
</style>

