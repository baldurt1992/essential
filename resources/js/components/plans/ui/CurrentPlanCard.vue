<template>
    <article
        :class="['essential-plan-card', 'essential-plan-card--current', { 'essential-plan-card--recommended': plan?.highlight?.isRecommended }]">
        <header class="plan-card__header">
            <div class="essential-plan-badge essential-plan-badge--current">Plan Actual</div>
            <h2 class="plan-card__title">{{ plan?.name }}</h2>
            <p v-if="plan?.description" class="plan-card__description">{{ plan.description }}</p>
        </header>

        <div class="plan-card__pricing">
            <span class="plan-card__amount">{{ formatPrice(plan) }}</span>
            <span class="plan-card__interval">{{ formatInterval(plan) }}</span>
        </div>

        <div v-if="activeSubscription"
            :class="['essential-plan-cancel-notice', { 'essential-plan-cancel-notice--warning': activeSubscription?.will_cancel }]">
            <i class="pi pi-info-circle"></i>
            <span v-if="activeSubscription?.will_cancel && activeSubscription?.current_period_end">
                Tu suscripción finalizará el <strong>{{ formatDate(activeSubscription.current_period_end)
                }}</strong> y perderás el acceso a los beneficios del plan.
            </span>
            <span v-else-if="activeSubscription?.current_period_end">
                Tu suscripción se renueva automáticamente el <strong>{{
                    formatDate(activeSubscription.current_period_end) }}</strong>.
            </span>
            <span v-else>
                Tu suscripción está activa
            </span>
        </div>

        <ul v-if="plan?.features?.length" class="plan-card__features">
            <li v-for="feature in plan.features" :key="feature">
                <span class="essential-plan-feature-icon" aria-hidden="true">
                    <svg viewBox="0 0 16 16">
                        <path d="M6.2 11.4 3.1 8.3l1.4-1.4 1.7 1.7 4.3-4.3 1.4 1.4-5.7 5.7Z"></path>
                    </svg>
                </span>
                <span>{{ feature }}</span>
            </li>
        </ul>

        <div class="plan-card__cta">
            <RouterLink :to="{ name: 'client.subscriptions' }"
                class="essential-contact-cta essential-contact-cta--ghost plan-card__cta-button">
                Gestionar Suscripción
            </RouterLink>
        </div>

        <footer v-if="plan?.limits?.length" class="plan-card__limits">
            <h3>Incluye</h3>
            <ul>
                <li v-for="limit in plan.limits" :key="limitKey(limit)">
                    <strong v-if="limit.label">{{ limit.label }}:</strong>
                    <span>{{ limitValue(limit) }}</span>
                </li>
            </ul>
        </footer>
    </article>
</template>

<script setup>
import { RouterLink } from 'vue-router';
import { usePlanPricing } from '@/composables/usePlanPricing';

defineProps({
    plan: {
        type: Object,
        required: true,
    },
    activeSubscription: {
        type: Object,
        default: null,
    },
    limitKey: {
        type: Function,
        required: true,
    },
    limitValue: {
        type: Function,
        required: true,
    },
});

const { formatPrice, formatInterval, formatDate } = usePlanPricing();
</script>

<style scoped>
.plan-card__header {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.plan-card__title {
    margin: 0;
    font-family: 'Space Mono', monospace;
    font-size: clamp(22px, 4vw, 26px);
    text-transform: uppercase;
    color: var(--essential-heading-color);
}

.plan-card__description {
    margin: 0;
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    line-height: 1.65;
    color: rgba(23, 23, 23, 0.72);
}

body.dark-mode .plan-card__description {
    color: rgba(243, 243, 243, 0.72);
}

.plan-card__pricing {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.plan-card__amount {
    font-family: 'Lexend', sans-serif;
    font-size: clamp(32px, 6vw, 40px);
    font-weight: 500;
    line-height: 1.1;
    color: var(--essential-heading-color);
}

.plan-card__interval {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: rgba(23, 23, 23, 0.56);
}

body.dark-mode .plan-card__interval {
    color: rgba(243, 243, 243, 0.56);
}

.plan-card__features {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.plan-card__features li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    line-height: 1.6;
    color: var(--essential-text-color);
}

.plan-card__cta {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: auto;
}

.plan-card__cta-button {
    width: 100%;
    justify-content: center;
}

.plan-card__limits {
    border-top: 1px solid rgba(23, 23, 23, 0.08);
    padding-top: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

body.dark-mode .plan-card__limits {
    border-top-color: rgba(243, 243, 243, 0.12);
}

.plan-card__limits h3 {
    margin: 0;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: rgba(23, 23, 23, 0.6);
}

body.dark-mode .plan-card__limits h3 {
    color: rgba(243, 243, 243, 0.6);
}

.plan-card__limits ul {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    color: rgba(23, 23, 23, 0.78);
}

body.dark-mode .plan-card__limits ul {
    color: rgba(243, 243, 243, 0.78);
}
</style>

