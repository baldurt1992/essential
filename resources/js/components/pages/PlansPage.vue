<template>
    <div class="plans-page">
        <PlansHero @scroll-to-plans="scrollToPlans" />

        <PlansGrid ref="plansGridRef" :plans="plans" :is-loading="isLoading" :has-error="hasError"
            :filtered-plans="filteredPlans" :has-maximum-plan="hasMaximumPlan" :current-plan-uuid="currentPlanUuid"
            :current-plan="currentPlan" :active-subscription="activeSubscription"
            :is-creating-checkout="isCreatingCheckout" :is-current-plan="isCurrentPlan" :can-upgrade="canUpgrade"
            :plan-highlight="planHighlight" :limit-key="limitKey" :limit-value="limitValue" @retry="retryFetch"
            @checkout="handleCheckout" />

        <PlansExtra />

        <AuthRequiredDialog :visible="showAuthModal" @update:visible="(val) => showAuthModal = val"
            @open-login="openLoginModal" @open-register="openRegisterModal" />
    </div>
</template>

<script setup>
    import { computed, onMounted, ref } from 'vue';
    import { useRouter, useRoute } from 'vue-router';
    import { useSitePlans } from '@/composables/useSitePlans';
    import { useAuth } from '@/composables/useAuth';
    import { useClientSubscriptions } from '@/composables/useClientSubscriptions';
    import { useToast } from 'primevue/usetoast';
    import { usePlansPage } from '@/composables/usePlansPage';
    import { usePlanCheckout } from '@/composables/usePlanCheckout';
    import PlansHero from '../plans/ui/PlansHero.vue';
    import PlansGrid from '../plans/ui/PlansGrid.vue';
    import PlansExtra from '../plans/ui/PlansExtra.vue';
    import AuthRequiredDialog from '../plans/modals/AuthRequiredDialog.vue';

    const router = useRouter();
    const route = useRoute();
    const toast = useToast();
    const auth = useAuth();
    const plansStore = useSitePlans();
    const subscriptionsStore = useClientSubscriptions();

    const {
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
    } = usePlansPage();

    const {
        isCreatingCheckout,
        showAuthModal,
        handleCheckout,
        openLoginModal,
        openRegisterModal,
    } = usePlanCheckout();

    const plansGridRef = ref(null);

    const scrollToPlans = () => {
        if (plansGridRef.value?.plansSection) {
            plansGridRef.value.plansSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    const retryFetch = () => {
        plansStore.fetchPlans({ force: true });
    };

    onMounted(async () => {
        await plansStore.fetchPlans();

        // Fetch subscriptions if user is authenticated
        if (auth.isAuthenticated.value) {
            await subscriptionsStore.fetchSubscriptions();
        }

        // Handle checkout success/cancel messages
        if (route.query.success === 'true') {
            toast.add({
                severity: 'success',
                summary: '¡Pago exitoso!',
                detail: 'Tu suscripción ha sido activada correctamente. Revisa tu panel de cliente para más detalles.',
                life: 8000,
            });
            // Refresh subscriptions
            if (auth.isAuthenticated.value) {
                await subscriptionsStore.fetchSubscriptions();
            }
            // Clean URL
            router.replace({ name: 'plans', query: {} });
        } else if (route.query.canceled === 'true') {
            toast.add({
                severity: 'info',
                summary: 'Pago cancelado',
                detail: 'El proceso de pago fue cancelado. Puedes intentar nuevamente cuando estés listo.',
                life: 5000,
            });
            // Clean URL
            router.replace({ name: 'plans', query: {} });
        }
    });
</script>

<style scoped>
    .plans-page {
        display: flex;
        flex-direction: column;
        gap: clamp(48px, 10vw, 96px);
        padding: clamp(24px, 8vw, 80px) clamp(20px, 6vw, 120px) clamp(80px, 12vw, 140px);
        background: var(--essential-background-color);
        color: var(--essential-text-color);
    }
</style>
