<template>
    <article class="essential-contact-card">
        <span class="essential-contact-card__icon" aria-hidden="true" v-html="channel.icon"></span>
        <div class="contact-card__content">
            <p class="contact-card__label">{{ channel.label }}</p>
            <p class="contact-card__value">{{ channel.value }}</p>
            <p v-if="channel.helper" class="contact-card__helper">{{ channel.helper }}</p>
        </div>
        <a v-if="channel.href" :href="channel.href" class="essential-contact-card__action"
            :target="channel.newTab ? '_blank' : null" rel="noopener" @click="channel.onClick?.()">
            {{ channel.actionLabel }}
        </a>
        <button v-else type="button" class="essential-contact-card__action" @click="channel.onClick?.()">
            {{ channel.actionLabel }}
        </button>
    </article>
</template>

<script setup>
    defineProps({
        channel: {
            type: Object,
            required: true,
        },
    });
</script>

<style scoped>
    .contact-card__content {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .contact-card__label {
        margin: 0;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(23, 23, 23, 0.6);
    }

    body.dark-mode .contact-card__label {
        color: rgba(243, 243, 243, 0.6);
    }

    .contact-card__value {
        margin: 0;
        font-family: 'Space Mono', monospace;
        font-size: clamp(18px, 4vw, 22px);
        text-transform: uppercase;
        color: var(--essential-text-color);
    }

    .contact-card__helper {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        line-height: 1.6;
        color: rgba(23, 23, 23, 0.64);
    }

    body.dark-mode .contact-card__helper {
        color: rgba(243, 243, 243, 0.64);
    }
</style>
