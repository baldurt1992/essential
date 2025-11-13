<template>
    <div class="plans-page">
        <section class="plans-hero">
            <div class="plans-hero__badge">
                <span class="plans-hero__dot"></span>
                <span>Suscripciones esenciales</span>
            </div>

            <h1 class="plans-hero__title">
                Planes que escalan<br>
                contigo
            </h1>

            <p class="plans-hero__description">
                Diseñamos tiers pensados para artistas, marcas y agencias que necesitan producción creativa constante.
                Escoge el plan que refleja tu ritmo actual o cuéntanos qué necesitas y lo personalizamos.
            </p>

            <div class="plans-hero__actions">
                <button type="button" class="plans-button plans-button--primary" @click="scrollToPlans">
                    Comparar planes
                </button>
                <RouterLink :to="{ name: 'contact', query: { from: 'plans' } }"
                    class="plans-button plans-button--ghost">
                    Habla con nosotros
                </RouterLink>
            </div>
        </section>

        <section class="plans-section" ref="plansSection">
            <div v-if="isLoading" class="plans-state plans-state--loading">
                <span class="plans-spinner"></span>
                <p>Cargando planes…</p>
            </div>

            <div v-else-if="hasError" class="plans-state plans-state--error">
                <p>No pudimos cargar los planes en este momento.</p>
                <button type="button" class="plans-button plans-button--primary" @click="retryFetch">
                    Reintentar
                </button>
            </div>

            <div v-else-if="!plans.length" class="plans-state plans-state--empty">
                <p>Aún no hay planes activos publicados. Estamos ajustando la oferta para ti.</p>
                <RouterLink :to="{ name: 'contact', query: { from: 'plans-empty' } }"
                    class="plans-button plans-button--ghost">
                    Notificar mi interés
                </RouterLink>
            </div>

            <div v-else-if="hasMaximumPlan && currentPlanUuid" class="plan-current-only">
                <article
                    :class="['plan-card', 'plan-card--current', { 'plan-card--recommended': currentPlan?.highlight?.isRecommended }]">
                    <header class="plan-card__header">
                        <div class="plan-card__badge plan-card__badge--current">Plan Actual</div>
                        <h2 class="plan-card__title">{{ currentPlan?.name }}</h2>
                        <p v-if="currentPlan?.description" class="plan-card__description">{{ currentPlan.description }}
                        </p>
                    </header>

                    <div class="plan-card__pricing">
                        <span class="plan-card__amount">{{ formatPrice(currentPlan) }}</span>
                        <span class="plan-card__interval">{{ formatInterval(currentPlan) }}</span>
                    </div>

                    <div v-if="activeSubscription"
                        :class="['plan-card__cancel-notice', { 'plan-card__cancel-notice--warning': activeSubscription?.will_cancel }]">
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

                    <ul v-if="currentPlan?.features?.length" class="plan-card__features">
                        <li v-for="feature in currentPlan.features" :key="feature">
                            <span class="plan-card__feature-icon" aria-hidden="true">
                                <svg viewBox="0 0 16 16">
                                    <path d="M6.2 11.4 3.1 8.3l1.4-1.4 1.7 1.7 4.3-4.3 1.4 1.4-5.7 5.7Z"></path>
                                </svg>
                            </span>
                            <span>{{ feature }}</span>
                        </li>
                    </ul>

                    <div class="plan-card__cta">
                        <RouterLink :to="{ name: 'client.subscriptions' }"
                            class="plans-button plans-button--ghost plan-card__cta-button">
                            Gestionar Suscripción
                        </RouterLink>
                    </div>

                    <footer v-if="currentPlan?.limits?.length" class="plan-card__limits">
                        <h3>Incluye</h3>
                        <ul>
                            <li v-for="limit in currentPlan.limits" :key="limitKey(limit)">
                                <strong v-if="limit.label">{{ limit.label }}:</strong>
                                <span>{{ limitValue(limit) }}</span>
                            </li>
                        </ul>
                    </footer>
                </article>
            </div>

            <div v-else class="plans-grid">
                <article v-for="plan in filteredPlans" :key="plan.id" :class="['plan-card', {
                    'plan-card--recommended': plan.highlight.isRecommended,
                    'plan-card--current': isCurrentPlan(plan)
                }]">
                    <header class="plan-card__header">
                        <div v-if="isCurrentPlan(plan)" class="plan-card__badge plan-card__badge--current">
                            ACTUAL
                        </div>
                        <div v-else-if="planHighlight(plan)" class="plan-card__badge">
                            {{ planHighlight(plan) }}
                        </div>
                        <h2 class="plan-card__title">{{ plan.name }}</h2>
                        <p v-if="plan.description" class="plan-card__description">{{ plan.description }}</p>
                    </header>

                    <div v-if="isCurrentPlan(plan) && activeSubscription"
                        :class="['plan-card__cancel-notice', { 'plan-card__cancel-notice--warning': activeSubscription?.will_cancel }]">
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

                    <div class="plan-card__pricing">
                        <span class="plan-card__amount">{{ formatPrice(plan) }}</span>
                        <span class="plan-card__interval">{{ formatInterval(plan) }}</span>
                    </div>

                    <ul v-if="plan.features.length" class="plan-card__features">
                        <li v-for="feature in plan.features" :key="feature">
                            <span class="plan-card__feature-icon" aria-hidden="true">
                                <svg viewBox="0 0 16 16">
                                    <path d="M6.2 11.4 3.1 8.3l1.4-1.4 1.7 1.7 4.3-4.3 1.4 1.4-5.7 5.7Z"></path>
                                </svg>
                            </span>
                            <span>{{ feature }}</span>
                        </li>
                    </ul>

                    <div class="plan-card__cta">
                        <button v-if="isCurrentPlan(plan)" type="button" disabled
                            class="plans-button plans-button--ghost plan-card__cta-button">
                            Plan Actual
                        </button>
                        <button v-else-if="currentPlanUuid && canUpgrade(plan)" type="button"
                            :disabled="isCreatingCheckout === plan.id" @click="handleCheckout(plan, $event)"
                            class="plans-button plans-button--primary plan-card__cta-button">
                            <span v-if="isCreatingCheckout === plan.id">Creando checkout…</span>
                            <span v-else>Actualizar Plan</span>
                        </button>
                        <button v-else type="button" :disabled="isCreatingCheckout === plan.id"
                            @click="handleCheckout(plan, $event)"
                            class="plans-button plans-button--primary plan-card__cta-button">
                            <span v-if="isCreatingCheckout === plan.id">Creando checkout…</span>
                            <span v-else>{{ plan.cta.label ?? 'Comprar plan' }}</span>
                        </button>
                    </div>

                    <footer v-if="plan.limits.length" class="plan-card__limits">
                        <h3>Incluye</h3>
                        <ul>
                            <li v-for="limit in plan.limits" :key="limitKey(limit)">
                                <strong v-if="limit.label">{{ limit.label }}:</strong>
                                <span>{{ limitValue(limit) }}</span>
                            </li>
                        </ul>
                    </footer>
                </article>
            </div>
        </section>

        <section class="plans-extra">
            <div class="plans-extra__card">
                <h2>¿Necesitas un plan híbrido?</h2>
                <p>
                    Mezclamos producción de plantillas, branding y campañas en un solo paquete. Cuéntanos tu frecuencia,
                    equipo y objetivos y armamos un plan personalizado con métricas claras.
                </p>
                <RouterLink :to="{ name: 'contact', query: { from: 'plans-custom' } }"
                    class="plans-button plans-button--primary">
                    Diseñar plan a medida
                </RouterLink>
            </div>
        </section>

        <!-- Modal de autenticación requerida -->
        <Dialog v-model:visible="showAuthModal" modal :style="{ width: '90%', maxWidth: '500px' }" :closable="true"
            :draggable="false">
            <template #header>
                <h2 class="auth-modal__title">Cuenta requerida</h2>
            </template>

            <div class="auth-modal__content">
                <p class="auth-modal__message">
                    Necesitas tener una cuenta para poder comprar un plan de suscripción.
                </p>
            </div>

            <template #footer>
                <div class="auth-modal__actions">
                    <button type="button" class="auth-nav-button auth-nav-button--ghost" @click="openLoginModal">
                        Iniciar sesión
                    </button>
                    <button type="button" class="auth-nav-button auth-nav-button--primary" @click="openRegisterModal">
                        Registrarse
                    </button>
                </div>
            </template>
        </Dialog>
    </div>
</template>

<script setup>
    import { computed, onMounted, ref } from 'vue';
    import { RouterLink, useRouter, useRoute } from 'vue-router';
    import { useSitePlans } from '../../composables/useSitePlans';
    import { useAuth } from '../../composables/useAuth';
    import { useClientSubscriptions } from '../../composables/useClientSubscriptions';
    import { useAuthModal } from '../../composables/useAuthModal';
    import axios from 'axios';
    import { useToast } from 'primevue/usetoast';
    import Dialog from 'primevue/dialog';

    const router = useRouter();
    const route = useRoute();
    const toast = useToast();
    const auth = useAuth();
    const { openAuthModal } = useAuthModal();
    const plansSection = ref(null);
    const plansStore = useSitePlans();
    const subscriptionsStore = useClientSubscriptions();
    const isCreatingCheckout = ref(null);
    const showAuthModal = ref(false);

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

    // Calculate annual equivalent price for comparison
    const getAnnualPrice = (plan) => {
        const price = plan.price || 0;
        const interval = plan.billing_interval || 'month';
        const count = plan.billing_interval_count || 1;

        if (interval === 'year') {
            return price / count; // Price per year
        } else if (interval === 'month') {
            return (price / count) * 12; // Convert to annual
        } else if (interval === 'week') {
            return (price / count) * 52; // Convert to annual
        } else if (interval === 'day') {
            return (price / count) * 365; // Convert to annual
        }
        return price;
    };

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

    const scrollToPlans = () => {
        if (plansSection.value) {
            plansSection.value.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

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

    const intervalDictionary = {
        day: ['día', 'días'],
        week: ['semana', 'semanas'],
        month: ['mes', 'meses'],
        year: ['año', 'años'],
    };

    const formatInterval = (plan) => {
        const interval = plan.billingInterval ?? 'month';
        const count = plan.billingIntervalCount ?? 1;
        const [singular, plural] = intervalDictionary[interval] ?? intervalDictionary.month;

        if (count === 1) {
            if (interval === 'month') return 'al mes';
            if (interval === 'year') return 'al año';
            return `por ${singular}`;
        }

        return `cada ${count} ${count === 1 ? singular : plural}`;
    };

    const formatPrice = (plan) => {
        const currency = (plan.currency ?? 'EUR').toUpperCase();
        const amount = Number(plan.price ?? 0);
        const formatter = new Intl.NumberFormat('es', {
            style: 'currency',
            currency,
            minimumFractionDigits: amount % 1 === 0 ? 0 : 2,
            maximumFractionDigits: 2,
        });
        return formatter.format(amount);
    };

    const formatDate = (dateString) => {
        console.log('[PlansPage] formatDate called with:', dateString, 'type:', typeof dateString);
        if (!dateString) {
            console.log('[PlansPage] formatDate: dateString is falsy');
            return '';
        }
        const date = new Date(dateString);
        const formatted = date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
        console.log('[PlansPage] formatDate result:', formatted);
        return formatted;
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

    const retryFetch = () => {
        plansStore.fetchPlans({ force: true });
    };

    const handleCheckout = async (plan, event) => {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        if (isCreatingCheckout.value) {
            return;
        }

        // Verificar si el usuario está autenticado
        if (!auth.isAuthenticated.value) {
            showAuthModal.value = true;
            return;
        }

        isCreatingCheckout.value = plan.id;

        try {
            const payload = {
                success_url: `${window.location.origin}/planes?success=true`,
                cancel_url: `${window.location.origin}/planes?canceled=true`,
            };

            // Si el usuario está autenticado, usar su email automáticamente
            if (auth.isAuthenticated.value && auth.user.value?.email) {
                payload.email = auth.user.value.email;
            }

            const response = await axios.post(`/api/plans/${plan.uuid}/checkout`, payload);

            if (response.data?.checkout_url) {
                window.location.href = response.data.checkout_url;
            } else {
                throw new Error('No se recibió la URL de checkout');
            }
        } catch (error) {
            console.error('[PlansPage] Error creating checkout session', error);
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: error.response?.data?.message ?? 'No pudimos crear la sesión de checkout. Por favor, intenta de nuevo.',
                life: 5000,
            });
        } finally {
            isCreatingCheckout.value = null;
        }
    };

    const currentPlan = computed(() => {
        if (!currentPlanUuid.value) {
            return null;
        }
        return plans.value.find(p => p.uuid === currentPlanUuid.value);
    });

    const openLoginModal = () => {
        showAuthModal.value = false;
        // Abrir el modal de autenticación del header
        openAuthModal('login');
    };

    const openRegisterModal = () => {
        showAuthModal.value = false;
        // Abrir el modal de autenticación del header
        openAuthModal('register');
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
        background: var(--qode-background-color);
        color: var(--qode-text-color);
    }

    .plans-hero {
        display: flex;
        flex-direction: column;
        gap: clamp(16px, 5vw, 32px);
        max-width: 720px;
    }

    .plans-hero__badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .plans-hero__dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .plans-hero__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(42px, 8vw, 68px);
        letter-spacing: -0.015em;
        line-height: 1.05;
        text-transform: uppercase;
    }

    .plans-hero__description {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: clamp(16px, 3.6vw, 18px);
        line-height: 1.75;
        max-width: 58ch;
        color: rgba(23, 23, 23, 0.78);
    }

    body.dark-mode .plans-hero__description {
        color: rgba(243, 243, 243, 0.78);
    }

    .plans-hero__actions {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .plans-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 12px 22px;
        border-radius: 999px;
        border: 1px solid var(--qode-text-color);
        background: transparent;
        color: var(--qode-text-color);
        cursor: pointer;
        text-decoration: none;
        transition: transform 0.25s ease, background 0.25s ease, color 0.25s ease, border-color 0.25s ease;
    }

    .plans-button--primary {
        background: #dd3333;
        border-color: #dd3333;
        color: #ffffff;
    }

    .plans-button--ghost:hover {
        background: var(--qode-heading-color);
        color: var(--qode-background-color);
        transform: translateY(-2px);
    }

    .plans-button--primary:hover {
        background: #c42b2b;
        border-color: #c42b2b;
        transform: translateY(-2px);
    }

    body.dark-mode .plans-button {
        border-color: var(--qode-border-color);
        color: var(--qode-text-color);
    }

    body.dark-mode .plans-button--ghost:hover {
        background: #ffffff;
        color: #171717;
    }

    .plans-section {
        display: flex;
        flex-direction: column;
        gap: clamp(28px, 6vw, 40px);
    }

    .plans-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        padding: 60px 20px;
        border: 1px dashed rgba(23, 23, 23, 0.14);
        border-radius: 20px;
        text-align: center;
        font-family: 'Inter', sans-serif;
        color: rgba(23, 23, 23, 0.78);
    }

    body.dark-mode .plans-state {
        border-color: rgba(243, 243, 243, 0.2);
        color: rgba(243, 243, 243, 0.78);
    }

    .plans-spinner {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 3px solid rgba(23, 23, 23, 0.15);
        border-top-color: #dd3333;
        animation: plans-spin 0.9s linear infinite;
    }

    body.dark-mode .plans-spinner {
        border-color: rgba(243, 243, 243, 0.15);
        border-top-color: #ff6666;
    }

    @keyframes plans-spin {
        to {
            transform: rotate(360deg);
        }
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

    .plan-card {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: clamp(22px, 5vw, 28px);
        border: 1px solid rgba(23, 23, 23, 0.08);
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 18px 32px rgba(0, 0, 0, 0.06);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .plan-card:hover {
        transform: translateY(-6px);
        border-color: rgba(221, 51, 51, 0.22);
        box-shadow: 0 28px 48px rgba(0, 0, 0, 0.12);
    }

    .plan-card--recommended {
        border-color: rgba(221, 51, 51, 0.4);
        box-shadow: 0 34px 52px rgba(221, 51, 51, 0.18);
    }

    body.dark-mode .plan-card {
        background: rgba(17, 17, 17, 0.9);
        border-color: rgba(243, 243, 243, 0.06);
        box-shadow: 0 20px 36px rgba(0, 0, 0, 0.45);
    }

    .plan-card__badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(221, 51, 51, 0.1);
        color: #dd3333;
    }

    .plan-card__badge--current {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
    }

    body.dark-mode .plan-card__badge {
        background: rgba(255, 102, 102, 0.14);
        color: #ff6666;
    }

    body.dark-mode .plan-card__badge--current {
        background: rgba(34, 197, 94, 0.2);
        color: #4ade80;
    }

    .plan-card--current {
        border-color: rgba(34, 197, 94, 0.3);
    }

    body.dark-mode .plan-card--current {
        border-color: rgba(34, 197, 94, 0.4);
    }

    .plan-current-only {
        display: flex;
        justify-content: center;
        max-width: 600px;
        margin: 0 auto;
    }

    .plan-card__title {
        margin: 0;
        font-family: 'Space Mono', monospace;
        font-size: clamp(22px, 4vw, 26px);
        text-transform: uppercase;
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

    .plan-card__cancel-notice {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        margin: 16px 0;
        border-radius: 12px;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.2);
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        color: #22c55e;
    }

    .plan-card__cancel-notice--warning {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }

    .plan-card__cancel-notice i {
        font-size: 16px;
        flex-shrink: 0;
    }

    .plan-card__cancel-notice strong {
        font-weight: 600;
        color: #16a34a;
    }

    .plan-card__cancel-notice--warning strong {
        color: #dc2626;
    }

    body.dark-mode .plan-card__cancel-notice {
        background: rgba(34, 197, 94, 0.15);
        border-color: rgba(34, 197, 94, 0.3);
        color: #4ade80;
    }

    body.dark-mode .plan-card__cancel-notice--warning {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.3);
        color: #ff6b6b;
    }

    body.dark-mode .plan-card__cancel-notice strong {
        color: #4ade80;
    }

    body.dark-mode .plan-card__cancel-notice--warning strong {
        color: #ff6b6b;
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
    }

    .plan-card__feature-icon {
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: rgba(23, 23, 23, 0.08);
        color: #171717;
        flex-shrink: 0;
    }

    .plan-card__feature-icon svg {
        width: 12px;
        height: 12px;
        fill: currentColor;
    }

    .plan-card__feature-icon svg path {
        fill: currentColor;
    }

    body.dark-mode .plan-card__feature-icon {
        background: rgba(243, 243, 243, 0.08);
        color: #ffffff;
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

    body.dark-mode .plan-card__limits h3 {
        color: rgba(243, 243, 243, 0.6);
    }

    body.dark-mode .plan-card__limits ul {
        color: rgba(243, 243, 243, 0.78);
    }

    .plans-extra {
        display: flex;
        justify-content: center;
    }

    .plans-extra__card {
        width: min(960px, 100%);
        padding: clamp(28px, 6vw, 40px);
        border-radius: 28px;
        border: 1px solid rgba(221, 51, 51, 0.16);
        background: rgba(221, 51, 51, 0.06);
        display: flex;
        flex-direction: column;
        gap: 18px;
        text-align: left;
    }

    .plans-extra__card h2 {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(24px, 5vw, 32px);
        text-transform: uppercase;
    }

    .plans-extra__card p {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        line-height: 1.7;
        color: rgba(23, 23, 23, 0.78);
    }

    body.dark-mode .plans-extra__card {
        border-color: rgba(255, 102, 102, 0.28);
        background: rgba(255, 102, 102, 0.12);
    }

    body.dark-mode .plans-extra__card p {
        color: rgba(243, 243, 243, 0.78);
    }

    @media (min-width: 960px) {
        .plans-hero__actions {
            flex-direction: row;
            align-items: center;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .plans-button,
        .plan-card,
        .plan-card__cta-button,
        .contact-card__action {
            transition: none;
        }
    }

    /* Modal de autenticación */
    .auth-modal__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(24px, 5vw, 32px);
        font-weight: 400;
        text-transform: uppercase;
        color: var(--qode-heading-color);
    }

    .auth-modal__content {
        padding: 20px 0;
    }

    .auth-modal__message {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 16px;
        line-height: 1.6;
        color: var(--qode-text-color);
    }

    .auth-modal__actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    /* Estilos de botones del modal (iguales a los del header) */
    .auth-nav-button {
        font-family: "IBM Plex Mono", sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 13px;
        padding: 10px 18px;
        border-radius: 999px;
        border: 1px solid var(--qode-text-color);
        background: transparent;
        color: var(--qode-text-color);
        cursor: pointer;
        transition: background 0.25s ease, color 0.25s ease, border-color 0.25s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .auth-nav-button--primary {
        background-color: #DD3333;
        border-color: #DD3333;
        color: #ffffff;
    }

    .auth-nav-button--primary:hover {
        background-color: #c42b2b;
        border-color: #c42b2b;
    }

    .auth-nav-button--ghost:hover {
        background-color: var(--qode-heading-color);
        color: var(--qode-background-color);
    }

    body.dark-mode .auth-nav-button {
        border-color: var(--qode-border-color);
        color: var(--qode-text-color);
    }

    body.dark-mode .auth-nav-button--ghost:hover {
        background-color: #ffffff;
        color: #171717;
    }

    body.dark-mode .auth-nav-button--primary {
        border-color: #ffffff;
    }

    @media (max-width: 480px) {
        .auth-modal__actions {
            flex-direction: column;
        }

        .auth-nav-button {
            width: 100%;
        }
    }
</style>
