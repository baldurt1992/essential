<template>
    <div class="client-dashboard">
        <div class="client-dashboard__header">
            <h2 class="client-dashboard__title">Resumen de tu cuenta</h2>
            <p class="client-dashboard__subtitle">Aquí puedes ver un resumen de tus suscripciones y compras</p>
        </div>

        <div v-if="subscriptionsStore.isLoading.value || purchasesStore.isLoading.value"
            class="client-dashboard__loading">
            <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
            <p>Cargando información...</p>
        </div>

        <div v-else class="client-dashboard__grid">
            <!-- Suscripciones Activas -->
            <div class="client-dashboard__card">
                <div class="client-dashboard__card-header">
                    <h3 class="client-dashboard__card-title">
                        <i class="pi pi-id-card"></i>
                        Suscripciones Activas
                    </h3>
                </div>
                <div class="client-dashboard__card-content">
                    <div v-if="subscriptionsStore.activeSubscriptions.value.length === 0"
                        class="client-dashboard__empty">
                        <p>No tienes suscripciones activas</p>
                        <RouterLink :to="{ name: 'plans' }" class="essential-button essential-button--primary">
                            Ver planes disponibles
                        </RouterLink>
                    </div>
                    <div v-else class="client-dashboard__subscriptions">
                        <div v-for="subscription in subscriptionsStore.activeSubscriptions.value"
                            :key="subscription.uuid" class="client-dashboard__subscription-item">
                            <div class="client-dashboard__subscription-info">
                                <h4>{{ subscription.plan?.name }}</h4>
                                <p v-if="subscription.current_period_end">
                                    Renovación: {{ formatDate(subscription.current_period_end) }}
                                </p>
                                <p v-else>Sin fecha de renovación</p>
                            </div>
                            <RouterLink :to="{ name: 'client.subscriptions' }"
                                class="essential-button essential-button--ghost">
                                Gestionar
                            </RouterLink>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compras Recientes -->
            <div class="client-dashboard__card">
                <div class="client-dashboard__card-header">
                    <h3 class="client-dashboard__card-title">
                        <i class="pi pi-shopping-bag"></i>
                        Compras Recientes
                    </h3>
                </div>
                <div class="client-dashboard__card-content">
                    <div v-if="purchasesStore.purchases.value.length === 0" class="client-dashboard__empty">
                        <p>No has realizado compras aún</p>
                        <RouterLink :to="{ name: 'templates' }" class="essential-button essential-button--primary">
                            Explorar plantillas
                        </RouterLink>
                    </div>
                    <div v-else class="client-dashboard__purchases">
                        <div v-for="purchase in purchasesStore.purchases.value.slice(0, 5)" :key="purchase.uuid"
                            class="client-dashboard__purchase-item">
                            <div class="client-dashboard__purchase-info">
                                <h4>{{ purchase.template?.title }}</h4>
                                <p>{{ formatDate(purchase.purchased_at) }}</p>
                            </div>
                            <RouterLink :to="{ name: 'client.purchases' }"
                                class="essential-button essential-button--ghost">
                                Ver detalles
                            </RouterLink>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { onMounted } from 'vue';
    import { RouterLink } from 'vue-router';
    import { useClientSubscriptions } from '../../../composables/useClientSubscriptions.js';
    import { useClientPurchases } from '../../../composables/useClientPurchases.js';
    import { useClientFormatting } from '../../../composables/useClientFormatting.js';

    const subscriptionsStore = useClientSubscriptions();
    const purchasesStore = useClientPurchases();
    const { formatDate } = useClientFormatting();

    onMounted(async () => {
        await Promise.all([
            subscriptionsStore.fetchSubscriptions(),
            purchasesStore.fetchPurchases(),
        ]);
    });
</script>

<style scoped>

    /* Client Dashboard Specific Styles */
    .client-dashboard__subscriptions,
    .client-dashboard__purchases {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .client-dashboard__subscription-item,
    .client-dashboard__purchase-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px;
        background: rgba(23, 23, 23, 0.02);
        border-radius: 12px;
    }

    body.dark-mode .client-dashboard__subscription-item,
    body.dark-mode .client-dashboard__purchase-item {
        background: rgba(255, 255, 255, 0.02);
    }

    .client-dashboard__subscription-info h4,
    .client-dashboard__purchase-info h4 {
        margin: 0 0 4px;
        font-family: 'Space Mono', monospace;
        font-size: 16px;
        color: var(--essential-heading-color);
    }

    .client-dashboard__subscription-info p,
    .client-dashboard__purchase-info p {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        opacity: 0.7;
        color: var(--essential-text-color);
    }
</style>
