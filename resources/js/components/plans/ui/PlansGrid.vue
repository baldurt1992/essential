<template>
    <section class="plans-section" ref="plansSection">
        <div v-if="isLoading" class="essential-state">
            <span class="essential-spinner"></span>
            <p>Cargando planes…</p>
        </div>

        <div v-else-if="hasError" class="essential-state essential-state--error">
            <p>No pudimos cargar los planes en este momento.</p>
            <button type="button" class="essential-retry-button" @click="$emit('retry')">
                Reintentar
            </button>
        </div>

        <div v-else-if="!plans.length" class="essential-state">
            <p>Aún no hay planes activos publicados. Estamos ajustando la oferta para ti.</p>
            <RouterLink :to="{ name: 'contact', query: { from: 'plans-empty' } }" class="essential-retry-button">
                Notificar mi interés
            </RouterLink>
        </div>

        <div v-else-if="hasMaximumPlan && currentPlanUuid" class="plan-current-only">
            <CurrentPlanCard v-if="currentPlan" :plan="currentPlan" :active-subscription="activeSubscription"
                :limit-key="limitKey" :limit-value="limitValue" />
        </div>

        <div v-else class="plans-grid">
            <PlanCard v-for="plan in filteredPlans" :key="plan.id" :plan="plan" :is-current="isCurrentPlan(plan)"
                :is-recommended="plan.highlight?.isRecommended" :highlight="planHighlight(plan)"
                :active-subscription="isCurrentPlan(plan) ? activeSubscription : null"
                :current-plan-uuid="currentPlanUuid" :can-upgrade="canUpgrade(plan)"
                :is-creating-checkout="isCreatingCheckout" :limit-key="limitKey" :limit-value="limitValue"
                @checkout="$emit('checkout', $event)" />
        </div>
    </section>
</template>

<script setup>
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import PlanCard from './PlanCard.vue';
import CurrentPlanCard from './CurrentPlanCard.vue';

const props = defineProps({
    plans: {
        type: Array,
        required: true,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    hasError: {
        type: Boolean,
        default: false,
    },
    filteredPlans: {
        type: Array,
        required: true,
    },
    hasMaximumPlan: {
        type: Boolean,
        default: false,
    },
    currentPlanUuid: {
        type: String,
        default: null,
    },
    currentPlan: {
        type: Object,
        default: null,
    },
    activeSubscription: {
        type: Object,
        default: null,
    },
    isCreatingCheckout: {
        type: [Number, String, null],
        default: null,
    },
    isCurrentPlan: {
        type: Function,
        required: true,
    },
    canUpgrade: {
        type: Function,
        required: true,
    },
    planHighlight: {
        type: Function,
        required: true,
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

defineEmits(['retry', 'checkout']);

const plansSection = ref(null);

defineExpose({
    plansSection,
});
</script>

<style scoped>
.plans-section {
    display: flex;
    flex-direction: column;
    gap: clamp(28px, 6vw, 40px);
}

.plans-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: clamp(20px, 5vw, 32px);
}

@media (min-width: 720px) {
    .plans-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }
}

.plan-current-only {
    display: flex;
    justify-content: center;
    max-width: 600px;
    margin: 0 auto;
}
</style>
