<template>
    <section class="admin-page">
        <ConfirmPopup />
        <AdminPageHeader title="Plantillas"
            subtitle="Administra tu catálogo de diseños, establece precios y controla qué se muestra como destacado."
            action-label="Nueva plantilla" action-icon="pi-plus" @action="openCreateModal" />

        <div class="admin-page__content">
            <AdminLoader v-if="isLoading" message="Cargando plantillas..." />

            <template v-else>
                <AdminTableToolbar :count="pagination.total" entity-name="plantillas registradas"
                    :is-refreshing="isRefreshing" />

                <AdminPlaceholder v-if="!templates.length" icon="pi-images"
                    text="Aquí aparecerá el listado de plantillas con filtros, estado y carga de archivos (preview y paquete descargable)." />

                <div v-else class="admin-table__wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Plantilla</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Actualización</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="template in templates" :key="template.id">
                                <td>
                                    <div class="admin-table__title">
                                        <div class="admin-table__title-main">
                                            <strong>{{ template.title }}</strong>
                                            <span class="admin-table__slug">/{{ template.slug }}</span>
                                        </div>
                                        <div class="admin-table__flags">
                                            <span v-if="template.is_popular"
                                                class="admin-badge admin-badge--accent">Popular</span>
                                            <span v-if="template.is_new"
                                                class="admin-badge admin-badge--muted">Nuevo</span>
                                        </div>
                                    </div>
                                    <p v-if="template.description" class="admin-table__description">
                                        {{ template.description }}
                                    </p>
                                </td>
                                <td class="admin-table__price">
                                    {{ formatPrice(template.price, template.currency) }}
                                </td>
                                <td>
                                    <span
                                        :class="['admin-status', template.is_active ? 'admin-status--active' : 'admin-status--inactive']">
                                        {{ template.is_active ? 'Activa' : 'Oculta' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="admin-table__timestamp">{{ formatUpdatedAt(template.updated_at)
                                    }}</span>
                                </td>
                                <td class="admin-table__actions">
                                    <button type="button" class="admin-icon-button"
                                        v-tooltip.bottom="'Editar plantilla'" @click="openEditModal(template)">
                                        <i class="pi pi-pencil"></i>
                                    </button>
                                    <button type="button" class="admin-icon-button admin-icon-button--danger"
                                        v-tooltip.bottom="'Eliminar plantilla'"
                                        @click="confirmDelete($event, template)">
                                        <i class="pi pi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="templates.length" class="admin-table__paginator">
                    <Paginator :first="first" :rows="pagination.per_page" :totalRecords="pagination.total"
                        template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords}"
                        @page="handlePageChange" />
                </div>
            </template>
        </div>

        <Dialog v-model:visible="isModalOpen" modal :draggable="false"
            :breakpoints="{ '960px': '75vw', '640px': '92vw' }" style="width: 820px" :header="modalTitle"
            @hide="handleModalHide">
            <AdminTemplateForm v-if="isModalOpen" ref="formRef" :key="modalKey" :mode="modalMode"
                :template="selectedTemplate" :saving="isSaving" :backend-errors="formFieldErrors"
                :general-error="formGeneralError" :currency="defaultCurrency" @submit="handleFormSubmit"
                @cancel="closeModal" />
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
    import AdminTemplateForm from '../templates/AdminTemplateForm.vue';
    import { useAdminTemplates } from '@/composables/admin/useTemplates';
    import { useAdminFormatting } from '@/composables/admin/useAdminFormatting';
    import { useAdminPagination } from '@/composables/admin/useAdminPagination';
    import { useConfirm } from 'primevue/useconfirm';
    import { useToast } from 'primevue/usetoast';

    const {
        templates,
        pagination,
        isLoading,
        isRefreshing,
        isSaving,
        fetchTemplates,
        createTemplate,
        updateTemplate,
        deleteTemplate,
    } = useAdminTemplates();

    const { formatPrice, formatUpdatedAt } = useAdminFormatting();
    const { first, handlePageChange: handlePaginationChange, resetPagination } = useAdminPagination(fetchTemplates);

    const confirm = useConfirm();
    const toast = useToast();
    const formRef = ref(null);
    const isModalOpen = ref(false);
    const modalMode = ref('create');
    const selectedTemplate = ref(null);
    const formFieldErrors = ref({});
    const formGeneralError = ref('');
    const modalKey = ref(0);

    const defaultCurrency = computed(() => templates.value?.[0]?.currency?.toUpperCase() ?? 'EUR');
    const modalTitle = computed(() => (modalMode.value === 'edit' ? 'Editar plantilla' : 'Nueva plantilla'));

    watch(
        pagination,
        (meta) => {
            const currentPage = meta?.current_page ?? 1;
            const perPage = meta?.per_page ?? 15;
            first.value = (currentPage - 1) * perPage;
        },
        { immediate: true, deep: true }
    );

    const openCreateModal = () => {
        modalMode.value = 'create';
        selectedTemplate.value = null;
        formFieldErrors.value = {};
        formGeneralError.value = '';
        modalKey.value++;
        isModalOpen.value = true;
    };

    const openEditModal = (template) => {
        modalMode.value = 'edit';
        selectedTemplate.value = template;
        formFieldErrors.value = {};
        formGeneralError.value = '';
        modalKey.value++;
        isModalOpen.value = true;
    };

    const handleFormSubmit = async ({ payload, formData, setPreviewFile, setPackageFile, updateBackendErrors }) => {
        formFieldErrors.value = {};
        formGeneralError.value = '';

        try {
            if (modalMode.value === 'edit' && selectedTemplate.value) {
                const dataToSend = formData || payload;
                if (import.meta.env.DEV) {
                    console.debug('[admin][templates][update][dataToSend]', {
                        isFormData: dataToSend instanceof FormData,
                        hasPackageFile: dataToSend instanceof FormData
                            ? dataToSend.has('package_file')
                            : 'package_file' in dataToSend,
                    });
                }
                const response = await updateTemplate(selectedTemplate.value.id, dataToSend);
                const currentPage = pagination.value?.current_page ?? 1;
                await fetchTemplates({ page: currentPage });
                toast.add({
                    severity: 'success',
                    summary: 'Plantilla actualizada',
                    detail: 'La plantilla se ha actualizado correctamente.',
                    life: 3000,
                });
                isModalOpen.value = false;
                return response;
            }

            await createTemplate(formData ?? payload);
            await fetchTemplates({ page: 1 });
            resetPagination();
            toast.add({
                severity: 'success',
                summary: 'Plantilla creada',
                detail: 'La plantilla se ha creado correctamente.',
                life: 3000,
            });
            isModalOpen.value = false;
        } catch (error) {
            const backendErrors = error.response?.data?.errors ?? {};
            formFieldErrors.value = backendErrors;
            formGeneralError.value = error.response?.data?.message ?? 'Ocurrió un error inesperado.';

            toast.add({ severity: 'error', summary: 'Error', detail: formGeneralError.value, life: 5000 });

            if (updateBackendErrors) {
                updateBackendErrors(backendErrors);
            }

            if (backendErrors.preview_image && setPreviewFile) {
                setPreviewFile(null);
            }

            if (backendErrors.package_file && setPackageFile) {
                setPackageFile(null);
            }
        }
    };

    const confirmDelete = (event, template) => {
        confirm.require({
            target: event.currentTarget,
            message: `¿Eliminar definitivamente la plantilla "${template.title}"?`,
            icon: 'pi pi-trash',
            rejectClass: 'essential-button essential-button--ghost',
            acceptClass: 'essential-button essential-button--danger',
            acceptLabel: 'Eliminar',
            rejectLabel: 'Cancelar',
            accept: async () => {
                try {
                    await deleteTemplate(template.id);
                    const currentPage = pagination.value?.current_page ?? 1;
                    await fetchTemplates({ page: currentPage });
                    toast.add({ severity: 'success', summary: 'Plantilla eliminada', life: 3000 });
                } catch (error) {
                    console.error('[admin][templates][delete][error]', error);
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
        selectedTemplate.value = null;
    };

    const handlePageChange = async (event) => {
        await handlePaginationChange(event);
    };

    onMounted(async () => {
        await fetchTemplates();
    });
</script>
