<template>
    <div class="essential-tagline">
        <div class="essential-tagline__inner">
            <span class="essential-tagline__icon"></span>
            <span class="essential-tagline__text">{{ text }}</span>
        </div>
    </div>
</template>

<script setup>
defineProps({
    text: {
        type: String,
        required: true,
    },
});
</script>

<style scoped>
.essential-tagline {
    padding-top: 20px;
    padding-bottom: 120px;
    padding-left: 0px;
    padding-right: 0px;
    border-top: 1px solid var(--essential-border-color);
    border-right: 0;
    border-bottom: 0;
    border-left: 0;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

.essential-tagline__inner {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 14px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--essential-text-color);
    padding: 0px 0px 0px 55px;
    max-width: 100%;
    box-sizing: border-box;
}

.essential-tagline__icon {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: var(--essential-text-color);
    display: inline-block;
}

@media (max-width: 1180px) {
    .essential-tagline__inner {
        padding-left: 40px;
    }
}

@media (max-width: 880px) {
    .essential-tagline__inner {
        padding-left: 30px;
    }
}

@media (max-width: 680px) {
    .essential-tagline {
        padding-top: 15px;
        padding-bottom: 60px;
    }

    .essential-tagline__inner {
        padding-left: 20px;
        font-size: 12px;
    }
}
</style>

