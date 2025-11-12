<template>
    <section class="admin-page">
        <ConfirmPopup />
        <header class="admin-page__header">
            <div>
                <h2 class="admin-page__title">Plantillas</h2>
                <p class="admin-page__subtitle">
                    Administra tu catálogo de diseños, establece precios y controla qué se muestra como destacado.
                </p>
            </div>
            <button type="button" class="admin-page__action qodef-button qodef-button--primary"
                @click="openCreateModal">
                <i class="pi pi-plus"></i>
                <span>Nueva plantilla</span>
            </button>
        </header>

        <div class="admin-page__content">
            <div v-if="isLoading" class="admin-loader">
                <span class="admin-loader__spinner"></span>
                <p class="admin-loader__text">Cargando plantillas...</p>
            </div>

            <template v-else>
                <div class="admin-table__toolbar">
                    <div class="admin-table__meta">
                        <span class="admin-table__count">{{ pagination.total }} plantillas registradas</span>
                        <span v-if="isRefreshing" class="admin-table__refresh">Actualizando…</span>
                    </div>
                </div>

                <div v-if="!templates.length" class="admin-placeholder">
                    <span class="admin-placeholder__icon">
                        <i class="pi pi-images"></i>
                    </span>
                    <p class="admin-placeholder__text">
                        Aquí aparecerá el listado de plantillas con filtros, estado y carga de archivos (preview y
                        paquete
                        descargable).
                    </p>
                </div>

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
    import { useConfirm } from 'primevue/useconfirm';
    import { useToast } from 'primevue/usetoast';
    import ConfirmPopup from 'primevue/confirmpopup';
    import AdminTemplateForm from '../templates/AdminTemplateForm.vue';
    import { useAdminTemplates } from '../../../composables/admin/useTemplates';
    import Paginator from 'primevue/paginator';

    const first = ref(0);

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

    const formRef = ref(null);
    const confirm = useConfirm();
    const toast = useToast();
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
                const response = await updateTemplate(selectedTemplate.value.id, formData ?? payload);
                const currentPage = pagination.value?.current_page ?? 1;
                await fetchTemplates({ page: currentPage });
                isModalOpen.value = false;
                return response;
            }

            await createTemplate(formData ?? payload);
            await fetchTemplates({ page: 1 });
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
            rejectClass: 'qodef-button qodef-button--ghost',
            acceptClass: 'qodef-button qodef-button--danger',
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

    const formatPrice = (price, currency) => {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: currency?.toUpperCase() ?? 'EUR',
            minimumFractionDigits: 2,
        }).format(price ?? 0);
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
        await fetchTemplates();
    });

    const handlePageChange = async (event) => {
        first.value = event.first;
        await fetchTemplates({ page: event.page + 1 });
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
        gap: 8px;
    }

    .admin-table__title-main {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .admin-table__flags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
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

    .admin-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-family: 'IBM Plex Mono', monospace;
        letter-spacing: 0.06em;
        font-size: 11px;
        text-transform: uppercase;
    }

    .admin-badge--accent {
        background: rgba(221, 51, 51, 0.12);
        color: #dd3333;
    }

    .admin-badge--muted {
        background: rgba(23, 23, 23, 0.08);
        color: rgba(23, 23, 23, 0.6);
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
