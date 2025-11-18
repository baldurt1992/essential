<template>
    <section class="admin-page">
        <ConfirmPopup />
        <AdminPageHeader title="Suscripciones"
            subtitle="Consulta las suscripciones activas, revisa su estado de renovación y cancela aquellas que necesites finalizar." />

        <div class="admin-page__content">
            <AdminLoader v-if="isLoading" message="Cargando suscripciones..." />

            <template v-else>
                <AdminTableToolbar :count="pagination.total" entity-name="suscripciones registradas"
                    :is-refreshing="isRefreshing" />

                <AdminPlaceholder v-if="!subscriptions.length" icon="pi-chart-bar"
                    text="Aún no hay suscripciones activas. Cuando tus clientes completen el checkout de un plan, verás el detalle aquí." />

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
                                        <span v-if="subscription.cancel_at_period_end" class="admin-table__renewal-note">
                                            Cancelará al finalizar el periodo
                                        </span>
                                    </div>
                                </td>
                                <td class="admin-table__actions">
                                    <button v-if="!subscription.cancel_at_period_end" type="button"
                                        class="admin-icon-button admin-icon-button--danger"
                                        v-tooltip.bottom="cancelTooltip(subscription)" :disabled="!canCancel(subscription)"
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
                        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords}" @page="handlePageChange" />
                </div>
            </template>
        </div>
    </section>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import ConfirmPopup from 'primevue/confirmpopup';
import Paginator from 'primevue/paginator';
import AdminPageHeader from '../ui/AdminPageHeader.vue';
import AdminLoader from '../ui/AdminLoader.vue';
import AdminPlaceholder from '../ui/AdminPlaceholder.vue';
import AdminTableToolbar from '../ui/AdminTableToolbar.vue';
import { useAdminSubscriptions } from '@/composables/admin/useSubscriptions';
import { useAdminPagination } from '@/composables/admin/useAdminPagination';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

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

const { first, handlePageChange: handlePaginationChange } = useAdminPagination(fetchSubscriptions);
const confirm = useConfirm();
const toast = useToast();

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
                toast.add({
                    severity: 'success',
                    summary: 'Suscripción actualizada',
                    detail: 'Se cancelará al fin del periodo.',
                    life: 4000,
                });
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
                toast.add({
                    severity: 'success',
                    summary: 'Suscripción reactivada',
                    detail: 'La suscripción continuará renovándose automáticamente.',
                    life: 4000,
                });
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

const handlePageChange = async (event) => {
    await handlePaginationChange(event);
};

onMounted(async () => {
    await fetchSubscriptions();
});
</script>
