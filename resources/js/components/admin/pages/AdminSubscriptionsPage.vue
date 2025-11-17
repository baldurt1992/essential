<template>
    <section class="admin-page">
        <ConfirmPopup />
        <header class="admin-page__header">
            <div>
                <h2 class="admin-page__title">Suscripciones</h2>
                <p class="admin-page__subtitle">
                    Consulta las suscripciones activas, revisa su estado de renovación y cancela aquellas que necesites
                    finalizar.
                </p>
            </div>
        </header>

        <div class="admin-page__content">
            <div v-if="isLoading" class="admin-loader">
                <span class="admin-loader__spinner"></span>
                <p class="admin-loader__text">Cargando suscripciones...</p>
            </div>

            <template v-else>
                <div class="admin-table__toolbar">
                    <div class="admin-table__meta">
                        <span class="admin-table__count">{{ pagination.total }} suscripciones registradas</span>
                        <span v-if="isRefreshing" class="admin-table__refresh">Actualizando…</span>
                    </div>
                </div>

                <div v-if="!subscriptions.length" class="admin-placeholder">
                    <span class="admin-placeholder__icon">
                        <i class="pi pi-chart-bar"></i>
                    </span>
                    <p class="admin-placeholder__text">
                        Aún no hay suscripciones activas. Cuando tus clientes completen el checkout de un plan, verás el
                        detalle aquí.
                    </p>
                </div>

                <div v-else class="admin-table__wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Plan</th>
                                <th>Periodo</th>
                                <th>Estado</th>
                                <th>Renovación</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="subscription in subscriptions" :key="subscription.id">
                                <td>
                                    <div class="admin-table__title">
                                        <strong>{{ subscription.user?.name ?? 'Usuario sin nombre' }}</strong>
                                        <span class="admin-table__slug">{{ subscription.user?.email }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="admin-table__title">
                                        <strong>{{ subscription.plan?.name ?? 'Plan eliminado' }}</strong>
                                        <span class="admin-table__slug">/{{ subscription.plan?.slug }}</span>
                                    </div>
                                    <p v-if="subscription.plan?.description" class="admin-table__description">
                                        {{ subscription.plan.description }}
                                    </p>
                                </td>
                                <td>
                                    <div class="admin-table__period">
                                        <span>Inicio: {{ formatDate(subscription.starts_at) }}</span>
                                        <span>Actual: {{ formatRange(subscription.current_period_start,
                                            subscription.current_period_end) }}</span>
                                        <span v-if="subscription.trial_ends_at" class="admin-table__trial">
                                            Trial hasta {{ formatDate(subscription.trial_ends_at) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span :class="['admin-status', statusVariant(subscription.status)]">
                                        {{ formatStatus(subscription.status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="admin-table__renewal">
                                        <span>Renueva: {{ formatDate(subscription.current_period_end) }}</span>
                                        <span v-if="subscription.cancel_at_period_end"
                                            class="admin-table__renewal-note">
                                            Cancelará al finalizar el periodo
                                        </span>
                                    </div>
                                </td>
                                <td class="admin-table__actions">
                                    <button v-if="!subscription.cancel_at_period_end" type="button"
                                        class="admin-icon-button admin-icon-button--danger"
                                        v-tooltip.bottom="cancelTooltip(subscription)"
                                        :disabled="!canCancel(subscription)"
                                        @click="confirmCancel($event, subscription)">
                                        <i class="pi pi-times"></i>
                                    </button>
                                    <button v-else type="button" class="admin-icon-button admin-icon-button--success"
                                        v-tooltip.bottom="reactivateTooltip(subscription)"
                                        :disabled="!canReactivate(subscription)"
                                        @click="confirmReactivate($event, subscription)">
                                        <i class="pi pi-refresh"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="subscriptions.length" class="admin-table__paginator">
                    <Paginator :first="first" :rows="pagination.per_page" :totalRecords="pagination.total"
                        template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords}"
                        @page="handlePageChange" />
                </div>
            </template>
        </div>
    </section>
</template>

<script setup>
    import { onMounted, ref, watch } from 'vue';
    import ConfirmPopup from 'primevue/confirmpopup';
    import { useConfirm } from 'primevue/useconfirm';
    import { useToast } from 'primevue/usetoast';
    import { useAdminSubscriptions } from '../../../composables/admin/useSubscriptions';
    import Paginator from 'primevue/paginator';

    const {
        subscriptions,
        pagination,
        isLoading,
        isRefreshing,
        isSaving,
        fetchSubscriptions,
        cancelSubscription,
        reactivateSubscription,
    } = useAdminSubscriptions();

    const confirm = useConfirm();
    const toast = useToast();
    const first = ref(0);

    watch(
        pagination,
        (meta) => {
            const currentPage = meta?.current_page ?? 1;
            const perPage = meta?.per_page ?? 20;
            first.value = (currentPage - 1) * perPage;
        },
        { immediate: true, deep: true }
    );

    const confirmCancel = (event, subscription) => {
        if (!canCancel(subscription)) {
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
                try {
                    await cancelSubscription(subscription.uuid);
                    const currentPage = pagination.value?.current_page ?? 1;
                    await fetchSubscriptions({ page: currentPage });
                    toast.add({ severity: 'success', summary: 'Suscripción actualizada', detail: 'Se cancelará al fin del periodo.', life: 4000 });
                } catch (error) {
                    toast.add({
                        severity: 'error',
                        summary: 'No se pudo cancelar',
                        detail: error.response?.data?.message ?? 'Intenta nuevamente más tarde.',
                        life: 5000,
                    });
                }
            },
        });
    };

    const confirmReactivate = (event, subscription) => {
        if (!canReactivate(subscription)) {
            return;
        }

        confirm.require({
            target: event.currentTarget,
            message: `¿Reactivar la renovación del plan "${subscription.plan?.name}"?`,
            icon: 'pi pi-refresh',
            rejectClass: 'essential-button essential-button--ghost',
            acceptClass: 'essential-button essential-button--primary',
            acceptLabel: 'Reactivar renovación',
            rejectLabel: 'Volver',
            accept: async () => {
                try {
                    await reactivateSubscription(subscription.uuid);
                    const currentPage = pagination.value?.current_page ?? 1;
                    await fetchSubscriptions({ page: currentPage });
                    toast.add({ severity: 'success', summary: 'Suscripción reactivada', detail: 'La suscripción continuará renovándose automáticamente.', life: 4000 });
                } catch (error) {
                    toast.add({
                        severity: 'error',
                        summary: 'No se pudo reactivar',
                        detail: error.response?.data?.message ?? 'Intenta nuevamente más tarde.',
                        life: 5000,
                    });
                }
            },
        });
    };

    const canCancel = (subscription) => {
        if (isSaving.value) {
            return false;
        }

        return !subscription.cancel_at_period_end && subscription.status === 'active';
    };

    const canReactivate = (subscription) => {
        if (isSaving.value) {
            return false;
        }

        return subscription.cancel_at_period_end && subscription.status === 'active';
    };

    const cancelTooltip = (subscription) => {
        if (subscription.cancel_at_period_end) {
            return 'Cancelación ya programada';
        }

        if (subscription.status !== 'active') {
            return 'Sólo las suscripciones activas pueden cancelarse';
        }

        return 'Cancelar al finalizar periodo';
    };

    const reactivateTooltip = (subscription) => {
        if (!subscription.cancel_at_period_end) {
            return 'La suscripción no está programada para cancelarse';
        }

        if (subscription.status !== 'active') {
            return 'Sólo las suscripciones activas pueden reactivarse';
        }

        return 'Reactivar renovación automática';
    };

    const formatDate = (value) => {
        if (!value) {
            return '—';
        }

        return new Intl.DateTimeFormat('es-ES', {
            dateStyle: 'medium',
        }).format(new Date(value));
    };

    const formatRange = (start, end) => {
        if (!start && !end) {
            return 'Sin datos';
        }

        const startString = formatDate(start);
        const endString = formatDate(end);
        return `${startString} → ${endString}`;
    };

    const formatStatus = (status) => {
        const map = {
            trialing: 'En prueba',
            active: 'Activa',
            past_due: 'Pago vencido',
            canceled: 'Cancelada',
            incomplete: 'Incompleta',
            incomplete_expired: 'Expirada',
            unpaid: 'Impaga',
            paused: 'Pausada',
        };
        return map[status] ?? status;
    };

    const statusVariant = (status) => {
        if (status === 'active' || status === 'trialing') {
            return 'admin-status--active';
        }

        if (status === 'canceled' || status === 'incomplete_expired') {
            return 'admin-status--inactive';
        }

        return 'admin-status--warning';
    };

    onMounted(async () => {
        await fetchSubscriptions();
    });

    const handlePageChange = async (event) => {
        first.value = event.first;
        await fetchSubscriptions({ page: event.page + 1 });
    };
</script>

<style scoped>
    .admin-page {
        display: flex;
        flex-direction: column;
        gap: 26px;
    }

    .admin-page__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }

    .admin-page__title {
        font-family: 'Space Mono', monospace;
        font-size: 22px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin: 0 0 6px;
    }

    .admin-page__subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        opacity: 0.8;
        margin: 0;
        max-width: 540px;
    }

    .admin-page__content {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 20px;
        border: 1px solid rgba(0, 0, 0, 0.06);
        padding: 40px;
        min-height: 320px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .admin-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        margin: 60px 0;
    }

    .admin-loader__spinner {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 3px solid rgba(221, 51, 51, 0.2);
        border-top-color: #dd3333;
        animation: admin-spin 1s linear infinite;
    }

    .admin-loader__text {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        color: rgba(23, 23, 23, 0.7);
    }

    @keyframes admin-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .admin-table__toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 18px;
    }

    .admin-table__meta {
        display: flex;
        align-items: center;
        gap: 16px;
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(23, 23, 23, 0.6);
    }

    .admin-table__refresh {
        color: #dd3333;
    }

    .admin-table__wrapper {
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 18px;
        overflow: auto;
    }

    .admin-table__paginator {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 12px 0 4px;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
    }

    .admin-table thead {
        background: rgba(23, 23, 23, 0.85);
        color: #ffffff;
    }

    .admin-table th,
    .admin-table td {
        padding: 18px 22px;
        text-align: left;
        vertical-align: top;
    }

    .admin-table tbody tr:nth-child(odd) {
        background: rgba(23, 23, 23, 0.02);
    }

    .admin-table tbody tr:hover {
        background: rgba(221, 51, 51, 0.05);
    }

    .admin-table__title {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .admin-table__title strong {
        font-family: 'Space Mono', monospace;
        font-size: 16px;
        letter-spacing: 0.05em;
    }

    .admin-table__slug {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(23, 23, 23, 0.6);
    }

    .admin-table__description {
        margin: 10px 0 0;
        color: rgba(23, 23, 23, 0.75);
        line-height: 1.5;
    }

    .admin-table__period {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 13px;
        color: rgba(23, 23, 23, 0.7);
    }

    .admin-table__trial {
        color: #dd3333;
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        font-size: 11px;
    }

    .admin-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        font-family: 'IBM Plex Mono', monospace;
    }

    .admin-status--active {
        background: rgba(34, 197, 94, 0.16);
        color: #15803d;
    }

    .admin-status--inactive {
        background: rgba(23, 23, 23, 0.12);
        color: rgba(23, 23, 23, 0.7);
    }

    .admin-status--warning {
        background: rgba(250, 204, 21, 0.18);
        color: #a16207;
    }

    .admin-table__renewal {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 13px;
        color: rgba(23, 23, 23, 0.7);
    }

    .admin-table__renewal-note {
        color: #dd3333;
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.08em;
        font-size: 11px;
        text-transform: uppercase;
    }

    .admin-table__actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .admin-icon-button {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 1px solid rgba(23, 23, 23, 0.12);
        background: transparent;
        color: #171717;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .admin-icon-button:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .admin-icon-button--danger {
        border-color: rgba(221, 51, 51, 0.3);
        color: #dd3333;
    }

    .admin-icon-button--danger:hover:not(:disabled) {
        background: rgba(221, 51, 51, 0.12);
        color: #c42b2b;
    }

    .admin-icon-button--success {
        border-color: rgba(34, 197, 94, 0.3);
        color: #22c55e;
    }

    .admin-icon-button--success:hover:not(:disabled) {
        background: rgba(34, 197, 94, 0.12);
        color: #16a34a;
    }

    .admin-placeholder {
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 420px;
        margin: 60px auto;
    }

    .admin-placeholder__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        line-height: 1;
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: rgba(221, 51, 51, 0.12);
        color: #dd3333;
    }

    .admin-placeholder__icon .pi {
        font-size: 24px;
    }

    .admin-placeholder__text {
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        line-height: 1.6;
        color: rgba(23, 23, 23, 0.75);
    }

    @media (max-width: 1024px) {
        .admin-page__content {
            padding: 28px;
        }

        .admin-table thead {
            font-size: 13px;
        }

        .admin-table th,
        .admin-table td {
            padding: 14px 18px;
        }
    }

    @media (max-width: 760px) {
        .admin-page__header {
            flex-direction: column;
            align-items: stretch;
        }

        .admin-table__wrapper {
            overflow-x: auto;
        }
    }
</style>
