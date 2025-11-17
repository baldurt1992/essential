<template>
    <component :is="tag" :class="['essential-section-title', { 'essential-section-title--padded': padded }]">
        <slot />
    </component>
</template>

<script setup>
defineProps({
    tag: {
        type: String,
        default: 'h2',
        validator: (value) => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'].includes(value),
    },
    padded: {
        type: Boolean,
        default: false,
    },
});
</script>

<style scoped>
.essential-section-title {
    font-family: 'Lexend', sans-serif;
    font-size: clamp(48px, 6vw, 56px);
    font-weight: 400;
    color: var(--essential-text-color);
    margin: 0;
    line-height: 1.07143em;
    text-transform: uppercase;
}

.essential-section-title--padded {
    padding-top: 0%;
    padding-bottom: 0%;
    padding-left: 6%;
    padding-right: 6%;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    word-wrap: break-word;
}

@media (max-width: 1180px) {
    .essential-section-title--padded {
        padding-left: 5%;
        padding-right: 5%;
    }
}

@media (max-width: 880px) {
    .essential-section-title {
        font-size: clamp(36px, 8vw, 48px);
    }

    .essential-section-title--padded {
        padding-left: 5%;
        padding-right: 5%;
    }
}

@media (max-width: 680px) {
    .essential-section-title {
        font-size: clamp(32px, 10vw, 42px);
    }

    .essential-section-title--padded {
        padding-left: 20px;
        padding-right: 20px;
    }
}
</style>

