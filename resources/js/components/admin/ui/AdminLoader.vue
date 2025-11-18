<template>
    <div class="admin-loader">
        <span class="admin-loader__spinner"></span>
        <p class="admin-loader__text">{{ message || 'Cargando...' }}</p>
    </div>
</template>

<script setup>
defineProps({
    message: {
        type: String,
        default: null,
    },
});
</script>

