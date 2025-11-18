<template>
    <div class="admin-placeholder">
        <span class="admin-placeholder__icon">
            <i :class="['pi', icon]"></i>
        </span>
        <p class="admin-placeholder__text">{{ text }}</p>
        <slot />
    </div>
</template>

<script setup>
    defineProps({
        icon: {
            type: String,
            default: 'pi-info-circle',
        },
        text: {
            type: String,
            required: true,
        },
    });
</script>
