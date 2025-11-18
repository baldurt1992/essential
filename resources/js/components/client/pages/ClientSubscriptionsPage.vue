<template>
    <div class="client-subscriptions">
        <ConfirmPopup />
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
            <button @click="subscriptionsStore.fetchSubscriptions()" class="essential-button essential-button--primary">
                Reintentar
            </button>
        </div>

        <div v-else-if="subscriptionsStore.subscriptions.value.length === 0" class="client-subscriptions__empty">
            <p>No tienes suscripciones</p>
            <RouterLink :to="{ name: 'plans' }" class="essential-button essential-button--primary">
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
                        <div
                            :class="['client-subscription-card__cancel-warning', { 'client-subscription-card__cancel-warning--info': !subscription.will_cancel }]">
                            <i class="pi pi-info-circle"></i>
                            <span v-if="subscription.will_cancel && subscription.current_period_end">
                                Tu suscripción finalizará el <strong>{{ formatDate(subscription.current_period_end)
                                }}</strong> y perderás el acceso a los beneficios del plan.
                            </span>
                            <span v-else-if="subscription.current_period_end">
                                Tu suscripción se renueva automáticamente el <strong>{{
                                    formatDate(subscription.current_period_end) }}</strong>.
                            </span>
                            <span v-else>
                                Tu suscripción está activa.
                            </span>
                        </div>
                    </div>
                    <div v-if="subscription.is_active && !subscription.will_cancel"
                        class="client-subscription-card__actions">
                        <button @click="confirmCancel($event, subscription)"
                            class="essential-button essential-button--ghost" :disabled="isCanceling">
                            Cancelar suscripción
                        </button>
                    </div>
                    <div v-if="subscription.is_active && subscription.will_cancel"
                        class="client-subscription-card__actions">
                        <button type="button" @click="confirmReactivate($event, subscription)"
                            class="essential-button essential-button--primary" :disabled="isCanceling">
                            Reactivar suscripción
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
    import { useConfirm } from 'primevue/useconfirm';
    import ConfirmPopup from 'primevue/confirmpopup';
    import { useClientSubscriptions } from '../../../composables/useClientSubscriptions.js';
    import { useClientFormatting } from '../../../composables/useClientFormatting.js';

    const toast = useToast();
    const confirm = useConfirm();
    const subscriptionsStore = useClientSubscriptions();
    const isCanceling = ref(false);

    const { formatDate, formatPrice } = useClientFormatting();

    const getStatusLabel = (subscription) => {
        if (subscription.will_cancel) return 'Se cancelará al final del período';
        if (subscription.is_active) return 'Activa';
        return 'Inactiva';
    };

    const confirmCancel = (event, subscription) => {
        if (isCanceling.value) {
            return;
        }

        confirm.require({
            target: event.currentTarget,
            message: `¿Cancelar la renovación del plan "${subscription.plan?.name}"?`,
            icon: 'pi pi-times-circle',
            rejectClass: 'essential-button essential-button--ghost',
            acceptClass: 'essential-button essential-button--danger',
            acceptLabel: 'Cancelar renovación',
            rejectLabel: 'Volver',
            accept: async () => {
                isCanceling.value = true;
                try {
                    const result = await subscriptionsStore.cancelSubscription(subscription.uuid);

                    if (result.success) {
                        toast.add({
                            severity: 'success',
                            summary: 'Suscripción actualizada',
                            detail: 'Se cancelará al fin del periodo. La facturación automática ha sido desactivada.',
                            life: 5000,
                        });
                    } else {
                        toast.add({
                            severity: 'error',
                            summary: 'No se pudo cancelar',
                            detail: result.error || 'Intenta nuevamente más tarde.',
                            life: 5000,
                        });
                    }
                } catch (error) {
                    toast.add({
                        severity: 'error',
                        summary: 'No se pudo cancelar',
                        detail: error.response?.data?.message ?? 'Intenta nuevamente más tarde.',
                        life: 5000,
                    });
                } finally {
                    isCanceling.value = false;
                }
            },
        });
    };

    const confirmReactivate = (event, subscription) => {
        if (isCanceling.value) {
            return;
        }

        confirm.require({
            target: event.currentTarget,
            message: `¿Reactivar la facturación automática del plan "${subscription.plan?.name}"?`,
            icon: 'pi pi-check-circle',
            rejectClass: 'essential-button essential-button--ghost',
            acceptClass: 'essential-button essential-button--primary',
            acceptLabel: 'Reactivar',
            rejectLabel: 'Cancelar',
            accept: async () => {
                isCanceling.value = true;
                try {
                    const result = await subscriptionsStore.reactivateSubscription(subscription.uuid);

                    if (result.success) {
                        toast.add({
                            severity: 'success',
                            summary: 'Suscripción reactivada',
                            detail: 'La facturación automática ha sido reactivada. Tu suscripción se renovará automáticamente.',
                            life: 5000,
                        });
                    } else {
                        toast.add({
                            severity: 'error',
                            summary: 'No se pudo reactivar',
                            detail: result.error || 'Intenta nuevamente más tarde.',
                            life: 5000,
                        });
                    }
                } catch (error) {
                    toast.add({
                        severity: 'error',
                        summary: 'No se pudo reactivar',
                        detail: error.response?.data?.message ?? 'Intenta nuevamente más tarde.',
                        life: 5000,
                    });
                } finally {
                    isCanceling.value = false;
                }
            },
        });
    };

    onMounted(async () => {
        await subscriptionsStore.fetchSubscriptions();
    });
</script>

<style scoped>
    /* Client Subscriptions Specific Styles */

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

    .client-subscription-card__actions .essential-button--primary {
        background: #dd3333 !important;
        color: #ffffff !important;
        border-color: #dd3333 !important;
    }

    .client-subscription-card__actions .essential-button--primary:hover:not(:disabled) {
        background: #c42b2b !important;
        border-color: #c42b2b !important;
    }

    .client-subscription-card__cancel-warning {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 18px;
        margin-top: 16px;
        border-radius: 12px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        line-height: 1.5;
        color: #ef4444;
    }

    .client-subscription-card__cancel-warning--info {
        background: rgba(34, 197, 94, 0.1);
        border-color: rgba(34, 197, 94, 0.2);
        color: #22c55e;
    }

    .client-subscription-card__cancel-warning i {
        font-size: 16px;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .client-subscription-card__cancel-warning strong {
        font-weight: 600;
        color: #dc2626;
    }

    .client-subscription-card__cancel-warning--info strong {
        color: #16a34a;
    }

    body.dark-mode .client-subscription-card__cancel-warning {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.3);
        color: #ff6b6b;
    }

    body.dark-mode .client-subscription-card__cancel-warning--info {
        background: rgba(34, 197, 94, 0.15);
        border-color: rgba(34, 197, 94, 0.3);
        color: #4ade80;
    }

    body.dark-mode .client-subscription-card__cancel-warning strong {
        color: #ff6b6b;
    }

    body.dark-mode .client-subscription-card__cancel-warning--info strong {
        color: #4ade80;
    }
</style>
