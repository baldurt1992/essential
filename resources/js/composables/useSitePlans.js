import { computed, reactive } from 'vue';
import axios from 'axios';

const state = reactive({
    collection: [],
    loading: false,
    error: null,
    initialized: false,
});

const mapPlan = (plan) => ({
    id: plan.id,
    uuid: plan.uuid,
    name: plan.name,
    slug: plan.slug,
    description: plan.description,
    currency: plan.currency ?? 'eur',
    price: plan.price,
    priceCents: plan.price_cents,
    billingInterval: plan.billing_interval,
    billingIntervalCount: plan.billing_interval_count,
    highlight: {
        isPopular: plan.highlight?.is_popular ?? false,
        isRecommended: plan.highlight?.is_recommended ?? false,
        label: plan.highlight?.label ?? null,
    },
    features: Array.isArray(plan.features) ? plan.features : [],
    cta: plan.cta ?? {},
    limits: Array.isArray(plan.limits) ? plan.limits : [],
    metadata: plan.metadata ?? {},
});

export const useSitePlans = () => {
    const plans = computed(() => state.collection);
    const isLoading = computed(() => state.loading);
    const error = computed(() => state.error);

    const fetchPlans = async (options = {}) => {
        if (state.initialized && !options.force) {
            return;
        }

        state.loading = true;
        state.error = null;

        try {
            const response = await axios.get('/api/plans');
            const payload = Array.isArray(response.data?.data) ? response.data.data : [];
            state.collection = payload.map(mapPlan);
            state.initialized = true;
        } catch (err) {
            console.error('[useSitePlans] Error fetching plans', err);
            state.error = err;
        } finally {
            state.loading = false;
        }
    };

    const clearError = () => {
        state.error = null;
    };

    return {
        plans,
        isLoading,
        error,
        fetchPlans,
        clearError,
    };
};


