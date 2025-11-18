<template>
    <Dialog v-model:visible="localVisible" :modal="true" :draggable="false" :closable="false" dismissable-mask
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
            <Button type="button" class="success-modal__button essential-button--primary" label="Continuar"
                @click="handleContinue" />
        </template>
    </Dialog>
</template>

<script setup>
import { computed } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'continue']);

const localVisible = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const dialogStyle = {
    width: '520px',
    background: 'var(--qode-background-color)',
};

const handleContinue = () => {
    localVisible.value = false;
    emit('continue');
};
</script>

<style scoped>
.success-modal,
.success-modal * {
    font-family: 'Inter', sans-serif;
    text-transform: none;
    letter-spacing: normal;
    color: inherit;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.success-dialog :deep(.p-dialog) {
    background: #ffffff;
    color: #171717;
    border-radius: 28px;
    box-shadow: 0 28px 80px rgba(0, 0, 0, 0.28);
    border: 1px solid rgba(0, 0, 0, 0.06);
}

body.dark-mode .success-dialog :deep(.p-dialog) {
    background: #101010;
    color: #f3f3f3;
    border-color: rgba(255, 255, 255, 0.08);
}

.success-dialog :deep(.p-dialog-header) {
    border: none;
    padding: 30px 36px 0;
    background: transparent;
}

.success-dialog :deep(.p-dialog-content) {
    border: none;
    padding: 26px 36px 34px;
    background: transparent;
}

.success-dialog :deep(.p-dialog-footer) {
    border: none;
    padding: 0 36px 26px;
    background: transparent;
}

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
    .success-dialog :deep(.p-dialog-header),
    .success-dialog :deep(.p-dialog-content),
    .success-dialog :deep(.p-dialog-footer) {
        padding-left: 22px;
        padding-right: 22px;
    }
}
</style>

