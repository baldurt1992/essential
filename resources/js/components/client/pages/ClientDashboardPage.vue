<template>
    <div class="client-dashboard">
        <div class="client-dashboard__header">
            <h2 class="client-dashboard__title">Resumen de tu cuenta</h2>
            <p class="client-dashboard__subtitle">Aquí puedes ver un resumen de tus suscripciones y compras</p>
        </div>

        <div v-if="subscriptionsStore.isLoading.value || purchasesStore.isLoading.value" class="client-dashboard__loading">
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
                    <div v-if="subscriptionsStore.activeSubscriptions.value.length === 0" class="client-dashboard__empty">
                        <p>No tienes suscripciones activas</p>
                        <RouterLink :to="{ name: 'plans' }" class="qodef-button qodef-button--primary">
                            Ver planes disponibles
                        </RouterLink>
                    </div>
                    <div v-else class="client-dashboard__subscriptions">
                        <div v-for="subscription in subscriptionsStore.activeSubscriptions.value" :key="subscription.uuid"
                            class="client-dashboard__subscription-item">
                            <div class="client-dashboard__subscription-info">
                                <h4>{{ subscription.plan?.name }}</h4>
                                <p v-if="subscription.current_period_end">
                                    Renovación: {{ formatDate(subscription.current_period_end) }}
                                </p>
                                <p v-else>Sin fecha de renovación</p>
                            </div>
                            <RouterLink :to="{ name: 'client.subscriptions' }"
                                class="qodef-button qodef-button--ghost">
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
                        <RouterLink :to="{ name: 'templates' }" class="qodef-button qodef-button--primary">
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
                                class="qodef-button qodef-button--ghost">
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

    const subscriptionsStore = useClientSubscriptions();
    const purchasesStore = useClientPurchases();

    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    onMounted(async () => {
        await Promise.all([
            subscriptionsStore.fetchSubscriptions(),
            purchasesStore.fetchPurchases(),
        ]);
    });
</script>

<style scoped>
    .client-dashboard {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .client-dashboard__header {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .client-dashboard__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: 32px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .client-dashboard__subtitle {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        opacity: 0.7;
    }

    .client-dashboard__loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        padding: 60px 20px;
        color: rgba(23, 23, 23, 0.6);
    }

    body.dark-mode .client-dashboard__loading {
        color: rgba(243, 243, 243, 0.6);
    }

    .client-dashboard__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 24px;
    }

    @media (max-width: 880px) {
        .client-dashboard__grid {
            grid-template-columns: 1fr;
        }
    }

    .client-dashboard__card {
        background: var(--qode-background-color);
        border: 1px solid rgba(23, 23, 23, 0.1);
        border-radius: 16px;
        overflow: hidden;
    }

    body.dark-mode .client-dashboard__card {
        border-color: rgba(255, 255, 255, 0.08);
    }

    .client-dashboard__card-header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(23, 23, 23, 0.1);
    }

    body.dark-mode .client-dashboard__card-header {
        border-bottom-color: rgba(255, 255, 255, 0.08);
    }

    .client-dashboard__card-title {
        margin: 0;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 14px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .client-dashboard__card-content {
        padding: 24px;
    }

    .client-dashboard__empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        padding: 40px 20px;
        text-align: center;
    }

    .client-dashboard__empty p {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        opacity: 0.7;
    }

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
    }

    .client-dashboard__subscription-info p,
    .client-dashboard__purchase-info p {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        opacity: 0.7;
    }
</style>

