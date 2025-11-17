<template>
    <section class="admin-page">
        <ConfirmPopup />
        <header class="admin-page__header">
            <div>
                <h2 class="admin-page__title">Planes de suscripción</h2>
                <p class="admin-page__subtitle">
                    Gestiona los planes disponibles, sus precios, intervalos de cobro y beneficios incluidos para los
                    clientes.
                </p>
            </div>
            <button type="button" class="admin-page__action essential-button essential-button--primary"
                @click="openCreateModal">
                <i class="pi pi-plus"></i>
                <span>Nuevo plan</span>
            </button>
        </header>

        <div class="admin-page__content">
            <div v-if="isLoading" class="admin-loader">
                <span class="admin-loader__spinner"></span>
                <p class="admin-loader__text">Cargando planes...</p>
            </div>

            <template v-else>
                <div class="admin-table__toolbar">
                    <div class="admin-table__meta">
                        <span class="admin-table__count">{{ pagination.total }} planes registrados</span>
                        <span v-if="isRefreshing" class="admin-table__refresh">Actualizando…</span>
                    </div>
                </div>

                <div v-if="!plans.length" class="admin-placeholder">
                    <span class="admin-placeholder__icon">
                        <i class="pi pi-briefcase"></i>
                    </span>
                    <p class="admin-placeholder__text">
                        Todavía no has configurado planes. Crea el primero para habilitar suscripciones recurrentes.
                    </p>
                </div>

                <div v-else class="admin-table__wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Plan</th>
                                <th>Intervalo</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Actualizado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="plan in plans" :key="plan.id">
                                <td>
                                    <div class="admin-table__title">
                                        <strong>{{ plan.name }}</strong>
                                        <span class="admin-table__slug">/{{ plan.slug }}</span>
                                    </div>
                                    <p v-if="plan.description" class="admin-table__description">{{ plan.description }}
                                    </p>
                                    <div v-if="plan.features?.length" class="admin-table__features">
                                        <span v-for="feature in plan.features.slice(0, 3)" :key="feature"
                                            class="admin-table__feature-chip">
                                            {{ feature }}
                                        </span>
                                        <span v-if="plan.features.length > 3" class="admin-table__feature-more">+{{
                                            plan.features.length - 3 }}</span>
                                    </div>
                                </td>
                                <td>{{ formatInterval(plan.billing_interval, plan.billing_interval_count) }}</td>
                                <td class="admin-table__price">{{ formatPrice(plan.price, plan.currency) }}</td>
                                <td>
                                    <span
                                        :class="['admin-status', plan.is_active ? 'admin-status--active' : 'admin-status--inactive']">
                                        {{ plan.is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="admin-table__timestamp">{{ formatUpdatedAt(plan.updated_at) }}</span>
                                </td>
                                <td class="admin-table__actions">
                                    <button type="button" class="admin-icon-button" v-tooltip.bottom="'Editar plan'"
                                        @click="openEditModal(plan)">
                                        <i class="pi pi-pencil"></i>
                                    </button>
                                    <button type="button" class="admin-icon-button admin-icon-button--danger"
                                        v-tooltip.bottom="'Eliminar plan'" @click="confirmDelete($event, plan)">
                                        <i class="pi pi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="plans.length" class="admin-table__paginator">
                    <Paginator :first="first" :rows="pagination.per_page" :totalRecords="pagination.total"
                        template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords}"
                        @page="handlePageChange" />
                </div>
            </template>
        </div>

        <Dialog v-model:visible="isModalOpen" modal :draggable="false"
            :breakpoints="{ '960px': '75vw', '640px': '92vw' }" style="width: 720px" :header="modalTitle"
            @hide="handleModalHide">
            <AdminPlanForm v-if="isModalOpen" :key="modalKey" :mode="modalMode" :plan="selectedPlan" :saving="isSaving"
                :backend-errors="formFieldErrors" :general-error="formGeneralError" :currency="defaultCurrency"
                @submit="handleFormSubmit" @cancel="closeModal" />
        </Dialog>
    </section>
</template>

<script setup>
    import { computed, onMounted, ref, watch } from 'vue';
    import { useConfirm } from 'primevue/useconfirm';
    import { useToast } from 'primevue/usetoast';
    import ConfirmPopup from 'primevue/confirmpopup';
    import Dialog from 'primevue/dialog';
    import AdminPlanForm from '../plans/AdminPlanForm.vue';
    import { useAdminPlans } from '../../../composables/admin/usePlans';
    import Paginator from 'primevue/paginator';

    const first = ref(0);

    const {
        plans,
        pagination,
        isLoading,
        isRefreshing,
        isSaving,
        fetchPlans,
        createPlan,
        updatePlan,
        deletePlan,
    } = useAdminPlans();

    const confirm = useConfirm();
    const toast = useToast();

    const isModalOpen = ref(false);
    const modalMode = ref('create');
    const selectedPlan = ref(null);
    const formFieldErrors = ref({});
    const formGeneralError = ref('');
    const modalKey = ref(0);

    const defaultCurrency = computed(() => plans.value?.[0]?.currency?.toUpperCase() ?? 'EUR');
    const modalTitle = computed(() => (modalMode.value === 'edit' ? 'Editar plan' : 'Nuevo plan'));

    watch(
        pagination,
        (meta) => {
            const currentPage = meta?.current_page ?? 1;
            const perPage = meta?.per_page ?? 10;
            first.value = (currentPage - 1) * perPage;
        },
        { immediate: true, deep: true }
    );

    const openCreateModal = () => {
        modalMode.value = 'create';
        selectedPlan.value = null;
        formFieldErrors.value = {};
        formGeneralError.value = '';
        modalKey.value++;
        isModalOpen.value = true;
    };

    const openEditModal = (plan) => {
        modalMode.value = 'edit';
        selectedPlan.value = plan;
        formFieldErrors.value = {};
        formGeneralError.value = '';
        modalKey.value++;
        isModalOpen.value = true;
    };

    const handleFormSubmit = async ({ payload }) => {
        formFieldErrors.value = {};
        formGeneralError.value = '';

        try {
            if (modalMode.value === 'edit' && selectedPlan.value) {
                await updatePlan(selectedPlan.value.uuid, payload);
                const currentPage = pagination.value?.current_page ?? 1;
                await fetchPlans({ page: currentPage });
                toast.add({ severity: 'success', summary: 'Plan actualizado', life: 3000 });
            } else {
                await createPlan(payload);
                await fetchPlans({ page: 1 });
                toast.add({ severity: 'success', summary: 'Plan creado', life: 3000 });
            }
            isModalOpen.value = false;
        } catch (error) {
            const backendErrors = error.response?.data?.errors ?? {};
            formFieldErrors.value = backendErrors;
            formGeneralError.value = error.response?.data?.message ?? 'No pudimos guardar el plan, intenta de nuevo.';
            toast.add({ severity: 'error', summary: 'Error', detail: formGeneralError.value, life: 5000 });
        }
    };

    const confirmDelete = (event, plan) => {
        confirm.require({
            target: event.currentTarget,
            message: `¿Eliminar definitivamente el plan "${plan.name}"?`,
            icon: 'pi pi-trash',
            rejectClass: 'essential-button essential-button--ghost',
            acceptClass: 'essential-button essential-button--danger',
            acceptLabel: 'Eliminar',
            rejectLabel: 'Cancelar',
            accept: async () => {
                try {
                    await deletePlan(plan.uuid);
                    const currentPage = pagination.value?.current_page ?? 1;
                    await fetchPlans({ page: currentPage });
                    toast.add({ severity: 'success', summary: 'Plan eliminado', life: 3000 });
                } catch (error) {
                    console.error('[admin][plans][delete][error]', error);
                    toast.add({
                        severity: 'error',
                        summary: 'No se pudo eliminar',
                        detail: error.response?.data?.message ?? 'Intenta nuevamente más tarde.',
                        life: 5000,
                    });
                }
            },
        });
    };

    const closeModal = () => {
        isModalOpen.value = false;
    };

    const handleModalHide = () => {
        formFieldErrors.value = {};
        formGeneralError.value = '';
        modalMode.value = 'create';
        selectedPlan.value = null;
    };

    const formatPrice = (price, currency) => {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: (currency || defaultCurrency.value).toUpperCase(),
            minimumFractionDigits: 2,
        }).format(price ?? 0);
    };

    const formatInterval = (interval, count) => {
        const labels = {
            day: 'día',
            week: 'semana',
            month: 'mes',
            year: 'año',
        };

        const label = labels[interval] ?? interval;
        if (!count || count === 1) {
            return `Cada ${label}`;
        }

        return `Cada ${count} ${label}${count > 1 ? 's' : ''}`;
    };

    const formatUpdatedAt = (timestamp) => {
        if (!timestamp) {
            return '—';
        }

        const date = new Date(timestamp);
        return new Intl.DateTimeFormat('es-ES', {
            dateStyle: 'medium',
            timeStyle: 'short',
        }).format(date);
    };

    onMounted(async () => {
        await fetchPlans();
    });

    const handlePageChange = async (event) => {
        first.value = event.first;
        await fetchPlans({ page: event.page + 1 });
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

    .admin-page__action {
        display: inline-flex;
        align-items: center;
        gap: 10px;
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

    .admin-table__features {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 12px;
    }

    .admin-table__feature-chip {
        background: rgba(23, 23, 23, 0.08);
        color: #171717;
        border-radius: 999px;
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.06em;
        font-size: 11px;
        padding: 4px 10px;
    }

    .admin-table__feature-more {
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.06em;
        font-size: 11px;
        color: rgba(23, 23, 23, 0.6);
    }

    .admin-table__price {
        font-weight: 600;
        font-size: 15px;
        color: #111111;
    }

    .admin-table__timestamp {
        color: rgba(23, 23, 23, 0.6);
        font-size: 13px;
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

    .admin-icon-button:hover {
        background: rgba(23, 23, 23, 0.08);
    }

    .admin-icon-button--danger {
        border-color: rgba(221, 51, 51, 0.3);
        color: #dd3333;
    }

    .admin-icon-button--danger:hover {
        background: rgba(221, 51, 51, 0.12);
        color: #c42b2b;
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
