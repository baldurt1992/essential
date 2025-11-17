import { computed } from 'vue';
import { useSitePlans } from './useSitePlans';
import { useClientSubscriptions } from './useClientSubscriptions';
import { useAuth } from './useAuth';
import { usePlanPricing } from './usePlanPricing';

/**
 * Composable para manejar la lógica de la página de planes
 */
export function usePlansPage() {
    const auth = useAuth();
    const plansStore = useSitePlans();
    const subscriptionsStore = useClientSubscriptions();
    const { getAnnualPrice } = usePlanPricing();

    const plans = plansStore.plans;
    const isLoading = plansStore.isLoading;
    const error = plansStore.error;
    const hasError = computed(() => !!error.value);

    // Get active subscription
    const activeSubscription = computed(() => {
        if (!auth.isAuthenticated.value || !subscriptionsStore.activeSubscriptions.value.length) {
            return null;
        }
        return subscriptionsStore.activeSubscriptions.value[0]; // Get first active subscription
    });

    const currentPlanUuid = computed(() => activeSubscription.value?.plan?.uuid || null);

    // Check if plan is current user's plan
    const isCurrentPlan = (plan) => {
        return currentPlanUuid.value === plan.uuid;
    };

    // Check if plan can be upgraded to (only if higher than current)
    const canUpgrade = (plan) => {
        if (!currentPlanUuid.value) {
            return true; // No current plan, can purchase any
        }

        const currentPlan = plans.value.find(p => p.uuid === currentPlanUuid.value);
        if (!currentPlan) {
            return true;
        }

        const currentAnnualPrice = getAnnualPrice(currentPlan);
        const planAnnualPrice = getAnnualPrice(plan);

        return planAnnualPrice > currentAnnualPrice;
    };

    // Get filtered plans (only show current plan + upgradeable plans, or all if no current plan)
    const filteredPlans = computed(() => {
        if (!currentPlanUuid.value) {
            return plans.value; // Show all plans if no active subscription
        }

        // Show current plan + plans that can be upgraded to
        return plans.value.filter(plan =>
            isCurrentPlan(plan) || canUpgrade(plan)
        );
    });

    // Check if user has maximum plan
    const hasMaximumPlan = computed(() => {
        if (!currentPlanUuid.value || !plans.value.length) {
            return false;
        }

        const currentPlan = plans.value.find(p => p.uuid === currentPlanUuid.value);
        if (!currentPlan) {
            return false;
        }

        const currentAnnualPrice = getAnnualPrice(currentPlan);
        const maxAnnualPrice = Math.max(...plans.value.map(p => getAnnualPrice(p)));

        return currentAnnualPrice >= maxAnnualPrice;
    });

    const currentPlan = computed(() => {
        if (!currentPlanUuid.value) {
            return null;
        }
        return plans.value.find(p => p.uuid === currentPlanUuid.value);
    });

    const planHighlight = (plan) => {
        if (plan.highlight?.label) {
            return plan.highlight.label;
        }

        if (plan.highlight?.isRecommended) {
            return 'Recomendado';
        }

        if (plan.highlight?.isPopular) {
            return 'Popular';
        }

        return null;
    };

    const limitKey = (limit) => {
        if (!limit) return Math.random().toString(36).slice(2);
        if (typeof limit === 'string') return limit;
        return `${limit.label ?? ''}-${limit.value ?? limit.text ?? ''}`;
    };

    const limitValue = (limit) => {
        if (!limit) return '';
        if (typeof limit === 'string') return limit;
        return limit.value ?? limit.text ?? '';
    };

    return {
        plans,
        isLoading,
        hasError,
        activeSubscription,
        currentPlanUuid,
        currentPlan,
        filteredPlans,
        hasMaximumPlan,
        isCurrentPlan,
        canUpgrade,
        planHighlight,
        limitKey,
        limitValue,
    };
}

