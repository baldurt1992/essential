<template>
    <header class="admin-page__header">
        <div>
            <h2 class="admin-page__title">{{ title }}</h2>
            <p v-if="subtitle" class="admin-page__subtitle">{{ subtitle }}</p>
        </div>
        <div class="admin-page__header-actions">
            <slot name="action">
                <button v-if="actionLabel" type="button"
                    class="admin-page__action essential-button essential-button--primary" @click="$emit('action')">
                    <i v-if="actionIcon" :class="['pi', actionIcon]"></i>
                    <span>{{ actionLabel }}</span>
                </button>
            </slot>
        </div>
    </header>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        default: null,
    },
    actionLabel: {
        type: String,
        default: null,
    },
    actionIcon: {
        type: String,
        default: 'pi-plus',
    },
});

defineEmits(['action']);
</script>

