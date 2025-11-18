<template>
    <section class="admin-page">
        <ConfirmPopup />
        <AdminPageHeader title="Servicios"
            subtitle="Administra los servicios ofrecidos, controla su visibilidad en la landing y marca cuáles aparecen como populares."
            action-label="Nuevo servicio" action-icon="pi-plus" @action="openCreateModal" />

        <div class="admin-page__content">
            <AdminLoader v-if="isLoading" message="Cargando servicios..." />

            <template v-else>
                <AdminTableToolbar :count="pagination.total" entity-name="servicios registrados"
                    :is-refreshing="isRefreshing" />

                <AdminPlaceholder v-if="!services.length" icon="pi-list"
                    text="Aún no hay servicios cargados. Crea el primero para mostrarlos en la página principal." />

                <div v-else class="admin-table__wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Servicio</th>
                                <th>Estado</th>
                                <th>Popular</th>
                                <th>Orden</th>
                                <th>Actualizado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="service in services" :key="service.id">
                                <td>
                                    <div class="admin-table__title">
                                        <strong>{{ service.title }}</strong>
                                        <span class="admin-table__slug">/{{ service.slug }}</span>
                                    </div>
                                    <p v-if="service.description" class="admin-table__description">
                                        {{ service.description }}
                                    </p>
                                    <span v-if="service.link_url" class="admin-table__link">{{ service.link_url }}</span>
                                </td>
                                <td>
                                    <span
                                        :class="['admin-status', service.is_active ? 'admin-status--active' : 'admin-status--inactive']">
                                        {{ service.is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        :class="['admin-badge', service.is_popular ? 'admin-badge--accent' : 'admin-badge--muted']">
                                        {{ service.is_popular ? 'Popular' : 'Standard' }}
                                    </span>
                                </td>
                                <td>{{ service.sort_order }}</td>
                                <td class="admin-table__timestamp">{{ formatUpdatedAt(service.updated_at) }}</td>
                                <td class="admin-table__actions">
                                    <button type="button" class="admin-icon-button" v-tooltip.bottom="'Editar servicio'"
                                        @click="openEditModal(service)">
                                        <i class="pi pi-pencil"></i>
                                    </button>
                                    <button type="button" class="admin-icon-button admin-icon-button--danger"
                                        v-tooltip.bottom="'Eliminar servicio'" @click="confirmDelete($event, service)">
                                        <i class="pi pi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="services.length" class="admin-table__paginator">
                    <Paginator :first="first" :rows="pagination.per_page" :totalRecords="pagination.total"
                        template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords}" @page="handlePageChange" />
                </div>
            </template>
        </div>

        <Dialog v-model:visible="isModalOpen" modal :draggable="false"
            :breakpoints="{ '960px': '75vw', '640px': '92vw' }" style="width: 720px" :header="modalTitle"
            @hide="handleModalHide">
            <AdminServiceForm v-if="isModalOpen" :key="modalKey" :mode="modalMode" :service="selectedService"
                :saving="isSaving" :backend-errors="formFieldErrors" :general-error="formGeneralError"
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
import AdminServiceForm from '../services/AdminServiceForm.vue';
import { useAdminServices } from '@/composables/admin/useServices';
import { useAdminFormatting } from '@/composables/admin/useAdminFormatting';
import { useAdminPagination } from '@/composables/admin/useAdminPagination';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const {
    services,
    pagination,
    isLoading,
    isRefreshing,
    isSaving,
    fetchServices,
    createService,
    updateService,
    deleteService,
} = useAdminServices();

const { formatUpdatedAt } = useAdminFormatting();
const { first, handlePageChange: handlePaginationChange, resetPagination } = useAdminPagination(fetchServices);

const confirm = useConfirm();
const toast = useToast();
const isModalOpen = ref(false);
const modalMode = ref('create');
const selectedService = ref(null);
const formFieldErrors = ref({});
const formGeneralError = ref('');
const modalKey = ref(0);

const modalTitle = computed(() => (modalMode.value === 'edit' ? 'Editar servicio' : 'Nuevo servicio'));

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
    selectedService.value = null;
    resetErrors();
    modalKey.value++;
    isModalOpen.value = true;
};

const openEditModal = (service) => {
    modalMode.value = 'edit';
    selectedService.value = service;
    resetErrors();
    modalKey.value++;
    isModalOpen.value = true;
};

const handleFormSubmit = async ({ payload }) => {
    resetErrors();

    try {
        if (modalMode.value === 'edit' && selectedService.value) {
            await updateService(selectedService.value.id, payload);
            toast.add({ severity: 'success', summary: 'Servicio actualizado', life: 3000 });
        } else {
            await createService(payload);
            resetPagination();
            toast.add({ severity: 'success', summary: 'Servicio creado', life: 3000 });
        }
        const currentPage = pagination.value?.current_page ?? 1;
        await fetchServices({ page: currentPage });
        isModalOpen.value = false;
    } catch (error) {
        const backendErrors = error.response?.data?.errors ?? {};
        formFieldErrors.value = backendErrors;
        formGeneralError.value = error.response?.data?.message ?? 'No pudimos guardar el servicio, intenta de nuevo.';
        toast.add({ severity: 'error', summary: 'Error', detail: formGeneralError.value, life: 5000 });
    }
};

const confirmDelete = (event, service) => {
    confirm.require({
        target: event.currentTarget,
        message: `¿Eliminar definitivamente el servicio "${service.title}"?`,
        icon: 'pi pi-trash',
        rejectClass: 'essential-button essential-button--ghost',
        acceptClass: 'essential-button essential-button--danger',
        acceptLabel: 'Eliminar',
        rejectLabel: 'Cancelar',
        accept: async () => {
            try {
                await deleteService(service.id);
                const currentPage = pagination.value?.current_page ?? 1;
                await fetchServices({ page: currentPage });
                toast.add({ severity: 'success', summary: 'Servicio eliminado', life: 3000 });
            } catch (error) {
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
    resetErrors();
    modalMode.value = 'create';
    selectedService.value = null;
};

const handlePageChange = async (event) => {
    await handlePaginationChange(event);
};

const resetErrors = () => {
    formFieldErrors.value = {};
    formGeneralError.value = '';
};

onMounted(async () => {
    await fetchServices();
});
</script>
