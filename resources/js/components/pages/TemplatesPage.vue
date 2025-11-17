<template>
    <div class="templates-page">
        <TemplatesHero :categories="categories" :filters="filters" @toggle-category="handleToggleCategory"
            @toggle-flag="handleToggleFlag" />

        <TemplatesGrid :templates="templates" :is-loading="isLoading" :has-error="hasError" :pagination="pagination"
            :is-downloading="isDownloading" :downloading-template-id="downloadingTemplateId"
            :is-authenticated="auth.isAuthenticated.value" @retry="handleRetry" @clear-filters="handleClearFilters"
            @primary-action="handlePrimaryAction" @preview="handlePreview" @open-auth="handleOpenAuth"
            @page-change="handlePageChange" />

        <TemplatePreviewDialog v-model:visible="previewVisible"
            :template="selectedTemplate" :dialog-width="previewDialogWidth" :breakpoints="previewBreakpoints"
            :is-downloading="isDownloading" :downloading-template-id="downloadingTemplateId"
            @primary-action="handlePrimaryAction" />

        <TemplateEmailModal v-model:visible="showEmailModal"
            :is-submitting="isCreatingCheckout" @submit="handleGuestCheckout" />

        <TemplateDownloadErrorDialog v-model:visible="showDownloadErrorDialog"
            :message="downloadErrorMessage" />
    </div>
</template>

<script setup>
    import { computed, onMounted } from 'vue';
    import { useAuth } from '@/composables/useAuth';
    import { useAuthModal } from '@/composables/useAuthModal';
    import { useSiteTemplates } from '@/composables/useSiteTemplates';
    import { useTemplatePurchase } from '@/composables/useTemplatePurchase';
    import { useTemplateFilters } from '@/composables/useTemplateFilters';
    import { useTemplatePreview } from '@/composables/useTemplatePreview';
    import { useTemplateRouteSync } from '@/composables/useTemplateRouteSync';
    import TemplatesHero from '../templates/ui/TemplatesHero.vue';
    import TemplatesGrid from '../templates/ui/TemplatesGrid.vue';
    import TemplatePreviewDialog from '../templates/modals/TemplatePreviewDialog.vue';
    import TemplateEmailModal from '../templates/modals/TemplateEmailModal.vue';
    import TemplateDownloadErrorDialog from '../templates/modals/TemplateDownloadErrorDialog.vue';

    const auth = useAuth();
    const { openAuthModal } = useAuthModal();
    const siteTemplates = useSiteTemplates();

    const {
        isDownloading,
        downloadingTemplateId,
        showEmailModal,
        guestEmail,
        isCreatingCheckout,
        showDownloadErrorDialog,
        downloadErrorMessage,
        handlePrimaryAction: handlePurchasePrimaryAction,
        handleGuestCheckout: handlePurchaseGuestCheckout,
    } = useTemplatePurchase();

    const { filters, categories, normalizeCategory } = useTemplateFilters();

    const {
        previewVisible,
        selectedTemplate,
        previewDialogWidth,
        previewBreakpoints,
        openPreview,
    } = useTemplatePreview();

    const { updateRoute, syncFromRoute } = useTemplateRouteSync();

    const templates = computed(() => siteTemplates.templates.value);
    const pagination = computed(() => siteTemplates.pagination.value);
    const isLoading = computed(() => siteTemplates.isLoading.value);
    const hasError = computed(() => !!siteTemplates.error.value);

    const handleToggleCategory = (value) => {
        const normalized = normalizeCategory(value);
        if (normalized === null) {
            updateRoute({ category: null, page: 1 });
            return;
        }

        const nextCategory = filters.value.category === normalized ? null : normalized;
        updateRoute({ category: nextCategory, page: 1 });
    };

    const handleToggleFlag = (flag) => {
        const next = !filters.value[flag];
        const overrides = { page: 1 };
        overrides[flag] = next;
        updateRoute(overrides);
    };

    const handleClearFilters = () => {
        updateRoute({ category: null, popular: false, fresh: false, page: 1 });
    };

    const handlePageChange = ({ page }) => {
        const nextPage = page + 1;
        updateRoute({ page: nextPage });
    };

    const handleRetry = async () => {
        await syncFromRoute();
    };

    const handlePrimaryAction = async (template) => {
        await handlePurchasePrimaryAction(template);
    };

    const handlePreview = (template) => {
        openPreview(template);
    };

    const handleOpenAuth = () => {
        openAuthModal('login');
    };

    const handleGuestCheckout = async (email) => {
        guestEmail.value = email;
        await handlePurchaseGuestCheckout();
    };

    onMounted(async () => {
        await syncFromRoute();
    });
</script>

<style scoped>
    :root {
        --templates-max-width: 1200px;
        --templates-grid-gap: 24px;
    }

    .templates-page {
        display: flex;
        flex-direction: column;
        gap: 40px;
        padding: 24px 20px 100px;
        background: var(--essential-background-color);
        color: var(--essential-text-color);
        min-height: 100vh;
    }

    @media (min-width: 768px) {
        .templates-page {
            padding: 36px 40px 140px;
            gap: 60px;
        }
    }
</style>
