<template>
    <section class="admin-page">
        <ConfirmPopup />
        <AdminPageHeader title="Planes de suscripción"
            subtitle="Gestiona los planes disponibles, sus precios, intervalos de cobro y beneficios incluidos para los clientes."
            action-label="Nuevo plan" action-icon="pi-plus" @action="openCreateModal" />

        <div class="admin-page__content">
            <AdminLoader v-if="isLoading" message="Cargando planes..." />

            <template v-else>
                <AdminTableToolbar :count="pagination.total" entity-name="planes registrados"
                    :is-refreshing="isRefreshing" />

                <AdminPlaceholder v-if="!plans.length" icon="pi-briefcase"
                    text="Todavía no has configurado planes. Crea el primero para habilitar suscripciones recurrentes." />

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
    import ConfirmPopup from 'primevue/confirmpopup';
    import Dialog from 'primevue/dialog';
    import Paginator from 'primevue/paginator';
    import AdminPageHeader from '../ui/AdminPageHeader.vue';
    import AdminLoader from '../ui/AdminLoader.vue';
    import AdminPlaceholder from '../ui/AdminPlaceholder.vue';
    import AdminTableToolbar from '../ui/AdminTableToolbar.vue';
    import AdminPlanForm from '../plans/AdminPlanForm.vue';
    import { useAdminPlans } from '@/composables/admin/usePlans';
    import { useAdminFormatting } from '@/composables/admin/useAdminFormatting';
    import { useAdminPagination } from '@/composables/admin/useAdminPagination';
    import { useConfirm } from 'primevue/useconfirm';
    import { useToast } from 'primevue/usetoast';

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

    const { formatPrice, formatUpdatedAt, formatInterval } = useAdminFormatting();
    const { first, handlePageChange: handlePaginationChange, resetPagination } = useAdminPagination(fetchPlans);

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
                resetPagination();
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

    const handlePageChange = async (event) => {
        await handlePaginationChange(event);
    };

    onMounted(async () => {
        await fetchPlans();
    });
</script>
