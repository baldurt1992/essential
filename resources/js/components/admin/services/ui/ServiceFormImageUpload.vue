<template>
    <div class="service-form__upload" :class="{ 'service-form__field--error': hasError }">
        <label>Imagen (JPG/PNG/WEBP)</label>
        <div class="service-form__preview">
            <Image v-if="currentImage" :src="currentImage" alt="Vista previa" width="220" preview />
            <div v-else class="service-form__preview-placeholder">
                <i class="pi pi-image"></i>
                <p>Selecciona una imagen representativa</p>
            </div>
        </div>
        <input ref="imageRef" type="file" accept="image/png,image/jpeg,image/webp" class="service-form__file-input"
            @change="onFileChange" />
        <div class="service-form__upload-actions">
            <button type="button" class="essential-button essential-button--ghost" @click="pickImage">
                Seleccionar imagen
            </button>
            <button v-if="hasImage" type="button" class="service-form__link" @click="handleClear">
                Quitar imagen
            </button>
        </div>
        <div class="service-form__error-slot">
            <small v-if="errorMessage" class="service-form__error">{{ errorMessage }}</small>
        </div>
    </div>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import Image from 'primevue/image';

    const props = defineProps({
        currentImage: {
            type: String,
            default: null,
        },
        errorMessage: {
            type: String,
            default: null,
        },
    });

    const emit = defineEmits(['image-change', 'image-clear']);

    const imageRef = ref(null);

    const hasImage = computed(() => !!props.currentImage);
    const hasError = computed(() => !!props.errorMessage);

    const pickImage = () => {
        imageRef.value?.click();
    };

    const onFileChange = (event) => {
        const file = event.target.files?.[0];
        if (!file) {
            return;
        }

        emit('image-change', file);

        // Limpiar el input para permitir seleccionar el mismo archivo de nuevo
        if (imageRef.value) {
            imageRef.value.value = '';
        }
    };

    const handleClear = () => {
        if (imageRef.value) {
            imageRef.value.value = '';
        }
        emit('image-clear');
    };
</script>
