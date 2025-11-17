<template>
    <article class="services-card" :class="{ 'services-card--popular': isPopular }">
        <div class="services-card__header">
            <span class="services-card__index">{{ index }}</span>
            <div class="services-card__meta">
                <span v-if="isPopular" class="services-card__badge">Popular</span>
                <span class="services-card__title">{{ title }}</span>
            </div>
        </div>

        <div class="services-card__body">
            <p class="services-card__description">{{ description }}</p>
        </div>

        <div class="services-card__footer">
            <RouterLink :to="target" class="essential-underline-link">
                <span>Ver proyecto</span>
                <i class="pi pi-arrow-right"></i>
            </RouterLink>
        </div>
    </article>
</template>

<script setup>
    import { RouterLink } from 'vue-router';

    defineProps({
        index: {
            type: String,
            required: true,
        },
        title: {
            type: String,
            required: true,
        },
        description: {
            type: String,
            required: true,
        },
        target: {
            type: String,
            required: true,
        },
        isPopular: {
            type: Boolean,
            default: false,
        },
    });
</script>

<style scoped>
    .services-card {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 16px;
        padding: 0;
        border-bottom: none;
    }

    .services-card__header {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .services-card__index {
        font-family: var(--essential-font-primary);
        font-size: 18px;
        letter-spacing: 0.1em;
        color: rgba(23, 23, 23, 0.44);
        min-width: 42px;
    }

    body.dark-mode .services-card__index {
        color: rgba(243, 243, 243, 0.38);
    }

    .services-card__meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .services-card__badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: var(--essential-font-menu);
        font-size: 10px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #dd3333;
    }

    .services-card__badge::before {
        content: '';
        width: 20px;
        height: 1px;
        background: currentColor;
    }

    body.dark-mode .services-card__badge {
        color: #ff6666;
    }

    .services-card__title {
        font-family: var(--essential-font-primary);
        font-size: 20px;
        text-transform: uppercase;
    }

    .services-card__body {
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex: 1;
    }

    .services-card__description {
        margin: 0;
        font-family: var(--essential-font-body);
        font-size: 14px;
        line-height: 1.6;
        color: rgba(23, 23, 23, 0.74);
        max-width: 60ch;
    }

    body.dark-mode .services-card__description {
        color: rgba(243, 243, 243, 0.78);
    }

    .services-card__footer {
        display: flex;
    }

    @media (min-width: 768px) {
        .services-card__body {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }
</style>

