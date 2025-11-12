<template>
    <div class="client-subscriptions">
        <div class="client-subscriptions__header">
            <h2 class="client-subscriptions__title">Mis Suscripciones</h2>
            <p class="client-subscriptions__subtitle">Gestiona tus planes y suscripciones activas</p>
        </div>

        <div v-if="subscriptionsStore.isLoading.value" class="client-subscriptions__loading">
            <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
            <p>Cargando suscripciones...</p>
        </div>

        <div v-else-if="subscriptionsStore.hasError.value" class="client-subscriptions__error">
            <p>{{ subscriptionsStore.state.error }}</p>
            <button @click="subscriptionsStore.fetchSubscriptions()" class="qodef-button qodef-button--primary">
                Reintentar
            </button>
        </div>

        <div v-else-if="subscriptionsStore.subscriptions.value.length === 0" class="client-subscriptions__empty">
            <p>No tienes suscripciones</p>
            <RouterLink :to="{ name: 'plans' }" class="qodef-button qodef-button--primary">
                Ver planes disponibles
            </RouterLink>
        </div>

        <div v-else class="client-subscriptions__list">
            <div v-for="subscription in subscriptionsStore.subscriptions.value" :key="subscription.uuid"
                class="client-subscription-card">
                <div class="client-subscription-card__header">
                    <div class="client-subscription-card__info">
                        <h3>{{ subscription.plan?.name }}</h3>
                        <span class="client-subscription-card__status" :class="{
                            'client-subscription-card__status--active': subscription.is_active,
                            'client-subscription-card__status--canceled': subscription.will_cancel,
                        }">
                            {{ getStatusLabel(subscription) }}
                        </span>
                    </div>
                </div>
                <div class="client-subscription-card__content">
                    <div class="client-subscription-card__details">
                        <div class="client-subscription-card__detail">
                            <span class="client-subscription-card__detail-label">Precio</span>
                            <span class="client-subscription-card__detail-value">
                                {{ formatPrice(subscription.plan?.price, subscription.plan?.currency) }}
                                / {{ subscription.plan?.billing_interval }}
                            </span>
                        </div>
                        <div v-if="subscription.current_period_start" class="client-subscription-card__detail">
                            <span class="client-subscription-card__detail-label">Inicio del período</span>
                            <span class="client-subscription-card__detail-value">
                                {{ formatDate(subscription.current_period_start) }}
                            </span>
                        </div>
                        <div v-if="subscription.current_period_end" class="client-subscription-card__detail">
                            <span class="client-subscription-card__detail-label">
                                {{ subscription.will_cancel ? 'Finaliza el' : 'Renovación' }}
                            </span>
                            <span class="client-subscription-card__detail-value">
                                {{ formatDate(subscription.current_period_end) }}
                            </span>
                        </div>
                    </div>
                    <div v-if="subscription.is_active && !subscription.will_cancel" class="client-subscription-card__actions">
                        <button @click="handleCancel(subscription)" class="qodef-button qodef-button--ghost"
                            :disabled="isCanceling">
                            Cancelar suscripción
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted } from 'vue';
    import { RouterLink } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { useClientSubscriptions } from '../../../composables/useClientSubscriptions.js';

    const toast = useToast();
    const subscriptionsStore = useClientSubscriptions();
    const isCanceling = ref(false);

    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    const formatPrice = (price, currency = 'USD') => {
        if (!price) return 'N/A';
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: currency,
        }).format(price);
    };

    const getStatusLabel = (subscription) => {
        if (subscription.will_cancel) return 'Se cancelará al final del período';
        if (subscription.is_active) return 'Activa';
        return 'Inactiva';
    };

    const handleCancel = async (subscription) => {
        if (!confirm('¿Estás seguro de que deseas cancelar esta suscripción? Se mantendrá activa hasta el final del período actual.')) {
            return;
        }

        isCanceling.value = true;
        const result = await subscriptionsStore.cancelSubscription(subscription.uuid);
        isCanceling.value = false;

        if (result.success) {
            toast.add({
                severity: 'success',
                summary: 'Suscripción cancelada',
                detail: 'Tu suscripción se cancelará al final del período actual.',
                life: 5000,
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: result.error || 'No se pudo cancelar la suscripción',
                life: 5000,
            });
        }
    };

    onMounted(async () => {
        await subscriptionsStore.fetchSubscriptions();
    });
</script>

<style scoped>
    .client-subscriptions {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .client-subscriptions__header {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .client-subscriptions__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: 32px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .client-subscriptions__subtitle {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        opacity: 0.7;
    }

    .client-subscriptions__loading,
    .client-subscriptions__error,
    .client-subscriptions__empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        padding: 60px 20px;
        text-align: center;
    }

    .client-subscriptions__error p,
    .client-subscriptions__empty p {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        opacity: 0.7;
    }

    .client-subscriptions__list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .client-subscription-card {
        background: var(--qode-background-color);
        border: 1px solid rgba(23, 23, 23, 0.1);
        border-radius: 16px;
        overflow: hidden;
    }

    body.dark-mode .client-subscription-card {
        border-color: rgba(255, 255, 255, 0.08);
    }

    .client-subscription-card__header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(23, 23, 23, 0.1);
    }

    body.dark-mode .client-subscription-card__header {
        border-bottom-color: rgba(255, 255, 255, 0.08);
    }

    .client-subscription-card__info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .client-subscription-card__info h3 {
        margin: 0;
        font-family: 'Space Mono', monospace;
        font-size: 18px;
    }

    .client-subscription-card__status {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(23, 23, 23, 0.1);
    }

    .client-subscription-card__status--active {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
    }

    .client-subscription-card__status--canceled {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
    }

    .client-subscription-card__content {
        padding: 24px;
    }

    .client-subscription-card__details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .client-subscription-card__detail {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .client-subscription-card__detail-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        opacity: 0.7;
    }

    .client-subscription-card__detail-value {
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        font-weight: 500;
    }

    .client-subscription-card__actions {
        display: flex;
        justify-content: flex-end;
    }
</style>

