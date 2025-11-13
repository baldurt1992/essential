<template>
    <div class="templates-page">
        <section class="templates-hero">
            <div class="templates-hero__intro">
                <div class="templates-hero__eyebrow">
                    <span class="templates-hero__dot"></span>
                    <span class="templates-hero__label">Catálogo creativo</span>
                </div>
                <h1 class="templates-hero__title">
                    Plantillas listas<br />
                    para convertir
                </h1>
                <p class="templates-hero__description">
                    Descarga recursos profesionales, optimizados para campañas, eventos y redes. Pensados para
                    diseñadores
                    que necesitan velocidad sin ceder calidad.
                </p>
            </div>

            <div class="templates-filters">
                <div class="templates-filters__group templates-filters__group--primary">
                    <button v-for="category in categories" :key="category.value ?? 'all'" type="button"
                        class="templates-filter-chip"
                        :class="{ 'templates-filter-chip--active': filters.category === category.value }"
                        @click="toggleCategory(category.value)">
                        {{ category.label }}
                    </button>
                </div>

                <div class="templates-filters__group templates-filters__group--secondary">
                    <button type="button" class="templates-filter-toggle"
                        :class="{ 'templates-filter-toggle--active': filters.popular }" @click="toggleFlag('popular')">
                        <span class="templates-filter-toggle__indicator"></span>
                        <span>Populares</span>
                    </button>
                    <button type="button" class="templates-filter-toggle"
                        :class="{ 'templates-filter-toggle--active': filters.fresh }" @click="toggleFlag('fresh')">
                        <span class="templates-filter-toggle__indicator"></span>
                        <span>Nuevas</span>
                    </button>
                </div>
            </div>
        </section>

        <section class="templates-collection">
            <div v-if="isLoading" class="templates-empty-state">
                <span class="templates-spinner"></span>
                <p>Cargando plantillas…</p>
            </div>

            <div v-else-if="hasError" class="templates-empty-state templates-empty-state--error">
                <p>No pudimos cargar las plantillas. ¿Intentamos de nuevo?</p>
                <button type="button" class="templates-retry-button" @click="retryFetch">
                    Reintentar
                </button>
            </div>

            <div v-else-if="!templates.length" class="templates-empty-state">
                <p>No encontramos plantillas con los filtros seleccionados.</p>
                <button type="button" class="templates-retry-button" @click="clearFilters">
                    Limpiar filtros
                </button>
            </div>

            <div v-else class="templates-grid">
                <article v-for="template in templates" :key="template.uuid" class="template-card">
                    <div class="template-card__media">
                        <Image :src="template.previewUrl" :alt="template.title" class="template-card__image" preview />

                        <div class="template-card__badges">
                            <span v-if="isTemplatePopular(template)" class="template-badge template-badge--popular">
                                Popular
                            </span>
                            <span v-if="isTemplateFresh(template)" class="template-badge template-badge--fresh">
                                Nuevo
                            </span>
                        </div>

                        <button type="button" class="template-card__preview" @click="openPreview(template)">
                            <i class="pi pi-eye"></i>
                            <span>Ver detalle</span>
                        </button>
                    </div>

                    <div class="template-card__body">
                        <div class="template-card__header">
                            <h2 class="template-card__title" :title="template.title">
                                {{ template.title }}
                            </h2>
                            <p class="template-card__price">
                                <span>{{ formatPrice(template.price, template.currency) }}</span>
                            </p>
                        </div>

                        <div class="template-card__tags" v-if="template.tags.length">
                            <span v-for="tag in template.tags" :key="tag" class="template-tag">
                                {{ tag }}
                            </span>
                        </div>

                        <div class="template-card__actions">
                            <button type="button" class="template-card__cta"
                                :disabled="isDownloading && downloadingTemplateId === template.id"
                                @click="handlePrimaryAction(template)">
                                <i v-if="isDownloading && downloadingTemplateId === template.id"
                                    class="pi pi-spin pi-spinner template-card__cta-spinner"></i>
                                <span v-else>{{ template.isAccessible ? 'Descargar' : 'Comprar' }}</span>
                            </button>
                            <button v-if="!auth.isAuthenticated.value" type="button" class="template-card__link"
                                @click="openAuthModal('login')">
                                Iniciar sesión
                                <i class="pi pi-arrow-right template-card__link-icon"></i>
                            </button>
                        </div>
                    </div>
                </article>
            </div>

            <div v-if="showPaginator" class="templates-pagination">
                <Paginator :rows="pagination.perPage" :total-records="pagination.total"
                    :first="(pagination.currentPage - 1) * pagination.perPage" @page="handlePageChange" />
            </div>
        </section>

        <Dialog v-model:visible="previewVisible" modal :header="selectedTemplate?.title ?? 'Plantilla'"
            class="template-preview-dialog" :style="{ width: previewDialogWidth }" :breakpoints="previewBreakpoints">
            <div v-if="selectedTemplate" class="template-preview">
                <div class="template-preview__media">
                    <Image :src="selectedTemplate.previewUrl" :alt="selectedTemplate.title" preview />
                </div>

                <div class="template-preview__content">
                    <div class="template-preview__meta">
                        <span class="template-preview__price">
                            {{ formatPrice(selectedTemplate.price, selectedTemplate.currency) }}
                        </span>
                        <div class="template-preview__flags">
                            <span v-if="isTemplatePopular(selectedTemplate)"
                                class="template-badge template-badge--popular">Popular</span>
                            <span v-if="isTemplateFresh(selectedTemplate)"
                                class="template-badge template-badge--fresh">Nuevo</span>
                        </div>
                    </div>

                    <p class="template-preview__description">
                        {{ selectedTemplate.description }}
                    </p>

                    <div v-if="selectedTemplate.tags.length" class="template-preview__tags">
                        <span v-for="tag in selectedTemplate.tags" :key="tag" class="template-tag">
                            {{ tag }}
                        </span>
                    </div>

                    <div class="template-preview__actions">
                        <button type="button" class="template-preview__buy"
                            :disabled="isDownloading && downloadingTemplateId === selectedTemplate.id"
                            @click="handlePrimaryAction(selectedTemplate)">
                            <i v-if="isDownloading && downloadingTemplateId === selectedTemplate.id"
                                class="pi pi-spin pi-spinner template-preview__buy-spinner"></i>
                            <span v-else>{{ selectedTemplate.isAccessible ? 'Descargar' : 'Comprar ahora' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </Dialog>

        <!-- Modal para solicitar email (invitados) -->
        <Dialog v-model:visible="showEmailModal" modal class="template-email-dialog"
            :style="{ width: '90%', maxWidth: '500px' }" :closable="true" :draggable="false">
            <template #header>
                <h2 class="template-email-modal__title">Completa tu compra</h2>
            </template>

            <div class="template-email-modal__content">
                <p class="template-email-modal__message">
                    Para continuar con la compra, necesitamos tu correo electrónico. Te enviaremos el enlace de descarga
                    una vez
                    completado el pago.
                </p>
                <form @submit.prevent="handleGuestCheckout" class="template-email-modal__form">
                    <div class="template-email-modal__field">
                        <InputText v-model="guestEmail" type="email" name="email" placeholder="tu@email.com" required
                            :disabled="isCreatingCheckout" class="w-full" />
                    </div>
                    <Button type="submit" class="qodef-button qodef-button--primary"
                        :disabled="isCreatingCheckout || !guestEmail">
                        <span v-if="!isCreatingCheckout">Continuar al pago</span>
                        <span v-else>Procesando...</span>
                    </Button>
                </form>
            </div>
        </Dialog>

        <!-- Dialog de error de descarga -->
        <Dialog v-model:visible="showDownloadErrorDialog" modal class="download-error-dialog"
            :style="{ width: '90%', maxWidth: '500px' }" :closable="true" :draggable="false">
            <template #header>
                <h2 class="download-error-dialog__title">Error al descargar</h2>
            </template>

            <div class="download-error-dialog__content">
                <p class="download-error-dialog__message">{{ downloadErrorMessage }}</p>
            </div>
            <template #footer>
                <Button @click="showDownloadErrorDialog = false" class="qodef-button qodef-button--primary">
                    Entendido
                </Button>
            </template>
        </Dialog>
    </div>
</template>

<script setup>
    import { computed, onMounted, onBeforeUnmount, ref, watch } from 'vue';
    import { RouterLink, useRoute, useRouter } from 'vue-router';
    import { useSiteTemplates } from '../../composables/useSiteTemplates';
    import { useAuth } from '../../composables/useAuth';
    import { useAuthModal } from '../../composables/useAuthModal';
    import { useToast } from 'primevue/usetoast';
    import Dialog from 'primevue/dialog';
    import Image from 'primevue/image';
    import Button from 'primevue/button';
    import axios from 'axios';

    const siteTemplates = useSiteTemplates();
    const router = useRouter();
    const route = useRoute();
    const auth = useAuth();
    const { openAuthModal } = useAuthModal();
    const toast = useToast();

    const filters = computed(() => siteTemplates.filters.value);
    const templates = computed(() => siteTemplates.templates.value);
    const pagination = computed(() => siteTemplates.pagination.value);
    const isLoading = computed(() => siteTemplates.isLoading.value);
    const hasError = computed(() => !!siteTemplates.error.value);
    const showPaginator = computed(() => pagination.value.total > pagination.value.perPage);

    const normalizeCategory = (category) => {
        if (typeof category === 'string') {
            const trimmed = category.trim();
            return trimmed.length ? trimmed : null;
        }
        return category ?? null;
    };

    const categories = computed(() => {
        const set = new Set();
        templates.value.forEach((template) => {
            const templateCategories = template.metadata?.categories ?? template.tags ?? [];
            templateCategories.forEach((category) => {
                const normalized = normalizeCategory(category);
                if (normalized) {
                    set.add(normalized);
                }
            });
        });
        const activeCategory = normalizeCategory(filters.value.category);
        if (activeCategory && !set.has(activeCategory)) {
            set.add(activeCategory);
        }
        const sortedCategories = Array.from(set).sort((a, b) => a.localeCompare(b, 'es'));
        return [
            { label: 'Todos', value: null },
            ...sortedCategories.map((value) => ({ label: value, value })),
        ];
    });

    const previewVisible = ref(false);
    const selectedTemplate = ref(null);
    const lastSyncedQueryKey = ref('');
    const hasSyncedOnce = ref(false);
    const showEmailModal = ref(false);
    const guestEmail = ref('');
    const isCreatingCheckout = ref(false);
    const pendingTemplate = ref(null);
    const showDownloadErrorDialog = ref(false);
    const downloadErrorMessage = ref('');
    const isDownloading = ref(false);
    const downloadingTemplateId = ref(null);

    const previewDialogWidth = ref('92vw');

    const previewBreakpoints = {
        '1280px': '720px',
        '960px': '92vw',
    };

    const formatPrice = (price, currency = 'USD') => {
        if (price == null) {
            return '—';
        }
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency,
            minimumFractionDigits: 0,
        }).format(price);
    };

    const isTemplatePopular = (template) => {
        if (typeof template.isPopular === 'boolean') {
            return template.isPopular;
        }

        const flags = template.metadata?.flags ?? {};
        return !!(flags.popular ?? flags.is_popular ?? template.metadata?.is_popular);
    };

    const isTemplateFresh = (template) => {
        if (typeof template.isNew === 'boolean') {
            return template.isNew;
        }

        const flags = template.metadata?.flags ?? {};
        return !!(flags.is_new ?? template.metadata?.is_new);
    };

    const openPreview = (template) => {
        selectedTemplate.value = template;
        previewVisible.value = true;
    };


    const handleDownload = async (template) => {
        isDownloading.value = true;
        downloadingTemplateId.value = template.id;

        try {
            const downloadUrl = `/api/downloads/${template.slug}`;
            const response = await axios.get(downloadUrl, {
                responseType: 'blob',
                timeout: 300000, // 5 minutos
            });

            // Si la respuesta es exitosa, crear un enlace temporal y descargar
            const blob = new Blob([response.data]);
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = template.slug + '.zip';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        } catch (error) {
            // Cuando responseType es 'blob', los errores también vienen como blob
            let errorMessage = 'Ocurrió un error al intentar descargar el archivo. Inténtalo más tarde.';

            if (error.response?.status === 503) {
                // Intentar parsear el blob como JSON
                if (error.response.data instanceof Blob) {
                    try {
                        const text = await error.response.data.text();
                        const errorData = JSON.parse(text);
                        errorMessage = errorData?.message || 'El archivo no está disponible temporalmente. Inténtalo más tarde.';
                    } catch (e) {
                        errorMessage = 'El archivo no está disponible temporalmente. Inténtalo más tarde.';
                    }
                } else {
                    errorMessage = error.response.data?.message || 'El archivo no está disponible temporalmente. Inténtalo más tarde.';
                }
            } else if (error.response?.status === 403) {
                // Intentar parsear el blob como JSON
                if (error.response.data instanceof Blob) {
                    try {
                        const text = await error.response.data.text();
                        const errorData = JSON.parse(text);
                        errorMessage = errorData?.message || 'No tienes permiso para descargar este archivo.';
                    } catch (e) {
                        errorMessage = 'No tienes permiso para descargar este archivo.';
                    }
                } else {
                    errorMessage = error.response.data?.message || 'No tienes permiso para descargar este archivo.';
                }
            }

            downloadErrorMessage.value = errorMessage;
            showDownloadErrorDialog.value = true;
        } finally {
            isDownloading.value = false;
            downloadingTemplateId.value = null;
        }
    };

    const handlePrimaryAction = async (template) => {
        if (template.isAccessible) {
            await handleDownload(template);
            return;
        }

        // Si el usuario está autenticado, proceder con el checkout normal
        if (auth.isAuthenticated.value) {
            await handleAuthenticatedCheckout(template);
            return;
        }

        // Si es invitado, mostrar modal para solicitar email
        pendingTemplate.value = template;
        showEmailModal.value = true;
    };

    const handleAuthenticatedCheckout = async (template) => {
        isCreatingCheckout.value = true;

        try {
            const response = await axios.post('/api/checkout/purchase', {
                template_slug: template.slug,
                is_guest: false,
            });

            if (response.data.checkout_url) {
                window.location.href = response.data.checkout_url;
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Error al crear la sesión de pago. Intenta nuevamente.';
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: message,
                life: 5000,
            });
        } finally {
            isCreatingCheckout.value = false;
        }
    };

    const handleGuestCheckout = async () => {
        if (!pendingTemplate.value || !guestEmail.value) {
            return;
        }

        isCreatingCheckout.value = true;

        try {
            const response = await axios.post('/api/checkout/purchase', {
                template_slug: pendingTemplate.value.slug,
                is_guest: true,
                email: guestEmail.value,
            });

            if (response.data.checkout_url) {
                showEmailModal.value = false;
                window.location.href = response.data.checkout_url;
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Error al crear la sesión de pago. Intenta nuevamente.';
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: message,
                life: 5000,
            });
        } finally {
            isCreatingCheckout.value = false;
        }
    };

    const updatePreviewWidth = () => {
        if (window.innerWidth >= 1280) {
            previewDialogWidth.value = '900px';
            return;
        }
        if (window.innerWidth >= 960) {
            previewDialogWidth.value = '720px';
            return;
        }
        previewDialogWidth.value = '92vw';
    };

    const buildRouteQuery = (overrides = {}) => {
        const currentPage = overrides.page ?? pagination.value.currentPage ?? 1;
        const category = Object.prototype.hasOwnProperty.call(overrides, 'category')
            ? overrides.category
            : filters.value.category;
        const popular = Object.prototype.hasOwnProperty.call(overrides, 'popular')
            ? overrides.popular
            : filters.value.popular;
        const fresh = Object.prototype.hasOwnProperty.call(overrides, 'fresh')
            ? overrides.fresh
            : filters.value.fresh;

        const query = {};
        if (category) {
            query.category = category;
        }
        if (popular) {
            query.popular = '1';
        }
        if (fresh) {
            query.fresh = '1';
        }
        if (currentPage && currentPage !== 1) {
            query.page = String(currentPage);
        }

        return query;
    };

    const updateRoute = (overrides = {}) => {
        router.replace({ name: 'templates', query: buildRouteQuery(overrides) }).catch(() => { });
    };

    const buildCurrentQueryKey = () => {
        const routeQuery = route.query;
        const page = parseInt(routeQuery.page ?? '1', 10);
        const category = routeQuery.category ?? null;
        const popular = routeQuery.popular === '1';
        const fresh = routeQuery.fresh === '1';

        return JSON.stringify({
            page: Number.isNaN(page) ? 1 : page,
            category,
            popular,
            fresh,
        });
    };

    const syncFromRoute = async () => {
        const queryKey = buildCurrentQueryKey();

        if (hasSyncedOnce.value && lastSyncedQueryKey.value === queryKey) {
            return;
        }

        const payload = JSON.parse(queryKey);

        await siteTemplates.fetchTemplates({
            ...payload,
            force: true,
        });

        hasSyncedOnce.value = true;
        lastSyncedQueryKey.value = queryKey;
    };

    const toggleCategory = (value) => {
        const normalized = normalizeCategory(value);
        if (normalized === null) {
            updateRoute({ category: null, page: 1 });
            return;
        }

        const nextCategory = filters.value.category === normalized ? null : normalized;
        updateRoute({ category: nextCategory, page: 1 });
    };

    const toggleFlag = (flag) => {
        const next = !filters.value[flag];
        const overrides = { page: 1 };
        overrides[flag] = next;
        updateRoute(overrides);
    };

    const clearFilters = () => {
        updateRoute({ category: null, popular: false, fresh: false, page: 1 });
    };

    const handlePageChange = ({ page }) => {
        const nextPage = page + 1;
        updateRoute({ page: nextPage });
    };

    const retryFetch = async () => {
        await syncFromRoute();
    };

    watch(
        () => route.query,
        async () => {
            const queryKey = buildCurrentQueryKey();
            if (hasSyncedOnce.value && lastSyncedQueryKey.value === queryKey) {
                return;
            }
            await syncFromRoute();
        },
        { immediate: true }
    );

    watch(previewVisible, (visible) => {
        if (visible) {
            updatePreviewWidth();
        }
    });

    const handleResize = () => {
        updatePreviewWidth();
    };

    onMounted(async () => {
        updatePreviewWidth();
        window.addEventListener('resize', handleResize, { passive: true });
        await syncFromRoute();
    });

    onBeforeUnmount(() => {
        window.removeEventListener('resize', handleResize);
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
        background: var(--qode-background-color);
        color: var(--qode-text-color);
        min-height: 100vh;
    }

    .templates-hero {
        display: flex;
        flex-direction: column;
        gap: 28px;
        max-width: var(--templates-max-width);
        width: 100%;
        margin: 0 auto;
    }

    .templates-hero__intro {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .templates-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .templates-hero__dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .templates-hero__title {
        font-family: 'Lexend', sans-serif;
        font-size: clamp(32px, 8vw, 52px);
        font-weight: 400;
        line-height: 1.05;
        letter-spacing: -0.01em;
        text-transform: uppercase;
        margin: 0;
    }

    .templates-hero__description {
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        line-height: 1.6;
        margin: 0;
        color: rgba(23, 23, 23, 0.78);
    }

    body.dark-mode .templates-hero__description {
        color: rgba(243, 243, 243, 0.78);
    }

    .templates-filters {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .templates-filters__group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .templates-filter-chip {
        border: 1px solid rgba(23, 23, 23, 0.12);
        background: transparent;
        color: inherit;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 10px 18px;
        border-radius: 999px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .templates-filter-chip--active {
        background: #dd3333;
        border-color: #dd3333;
        color: #ffffff;
    }

    .templates-filter-chip:not(.templates-filter-chip--active):hover {
        transform: translateY(-1px);
    }

    .templates-filter-toggle {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 1px solid rgba(23, 23, 23, 0.12);
        background: rgba(23, 23, 23, 0.02);
        color: inherit;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 10px 16px;
        border-radius: 999px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .templates-filter-toggle__indicator {
        width: 12px;
        height: 12px;
        border-radius: 4px;
        background: rgba(23, 23, 23, 0.18);
        transition: background 0.2s ease;
    }

    .templates-filter-toggle--active {
        border-color: #dd3333;
        background: rgba(221, 51, 51, 0.12);
        color: #dd3333;
    }

    .templates-filter-toggle--active .templates-filter-toggle__indicator {
        background: #dd3333;
    }

    .templates-collection {
        max-width: 1320px;
        width: 100%;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 36px;
    }

    .templates-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
        padding: 60px 20px;
        font-family: 'Inter', sans-serif;
        border: 1px dashed rgba(23, 23, 23, 0.12);
        border-radius: 16px;
        text-align: center;
        color: rgba(23, 23, 23, 0.72);
    }

    body.dark-mode .templates-empty-state {
        border-color: rgba(255, 255, 255, 0.15);
        color: rgba(243, 243, 243, 0.72);
    }

    .templates-empty-state--error {
        border-style: solid;
    }

    .templates-spinner {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 3px solid rgba(23, 23, 23, 0.15);
        border-top-color: #dd3333;
        animation: templates-spin 0.9s linear infinite;
    }

    body.dark-mode .templates-spinner {
        border-color: rgba(255, 255, 255, 0.15);
        border-top-color: #ff6666;
    }

    @keyframes templates-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .templates-retry-button {
        border: 1px solid currentColor;
        background: transparent;
        color: inherit;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 10px 18px;
        border-radius: 999px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .templates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(248px, 248px));
        gap: clamp(20px, 4.5vw, 32px);
        padding: 0 clamp(10px, 2vw, 24px);
        justify-content: center;
        max-width: calc(5 * 248px + 4 * clamp(20px, 4.5vw, 32px));
        margin: 0 auto;
    }

    @media (max-width: 1400px) {
        .templates-grid {
            max-width: calc(4 * 248px + 3 * clamp(20px, 4.5vw, 32px));
        }
    }

    @media (max-width: 1120px) {
        .templates-grid {
            max-width: calc(3 * 248px + 2 * clamp(20px, 4.5vw, 32px));
        }
    }

    @media (max-width: 840px) {
        .templates-grid {
            max-width: calc(2 * 248px + 1 * clamp(20px, 4.5vw, 32px));
        }
    }

    @media (max-width: 560px) {
        .templates-grid {
            max-width: 248px;
        }
    }

    .template-card {
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid rgba(23, 23, 23, 0.08);
        border-radius: 18px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        width: 248px;
        height: 441px;
        box-shadow: 0 18px 32px rgba(0, 0, 0, 0.06);
    }

    body.dark-mode .template-card {
        background: rgba(17, 17, 17, 0.9);
        border-color: rgba(255, 255, 255, 0.06);
        box-shadow: 0 20px 35px rgba(0, 0, 0, 0.4);
    }

    .template-card__media {
        position: relative;
        overflow: hidden;
        width: 100%;
        height: 248px;
        flex-shrink: 0;
    }

    .template-card__image {
        width: 100%;
        height: 100%;
    }

    .template-card__image :deep(img) {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .template-card__media:hover .template-card__image :deep(img) {
        transform: scale(1.04);
    }

    .template-card__badges {
        position: absolute;
        top: 16px;
        left: 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .template-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(23, 23, 23, 0.78);
        color: #ffffff;
    }

    .template-badge--popular {
        background: rgba(221, 51, 51, 0.9);
    }

    .template-badge--fresh {
        background: rgba(0, 0, 0, 0.75);
    }

    .template-card__preview {
        position: absolute;
        right: 12px;
        bottom: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
        background: rgba(23, 23, 23, 0.85);
        color: #ffffff;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 9px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 6px 12px;
        border-radius: 999px;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .template-card__preview:hover {
        background: #dd3333;
    }

    .template-card__body {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 14px 16px 16px;
        overflow: hidden;
        min-height: 0;
    }

    .template-card__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .template-card__title {
        font-family: 'Space Mono', monospace;
        font-size: 14px;
        text-transform: uppercase;
        margin: 0;
        line-height: 1.3;
        line-clamp: 2;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-break: break-word;
    }

    .template-card__price {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin: 0;
        color: rgba(221, 51, 51, 0.95);
        white-space: nowrap;
    }

    .template-card__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        flex-shrink: 0;
    }

    .template-tag {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 9px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 4px 8px;
        border-radius: 999px;
        background: rgba(23, 23, 23, 0.08);
        color: rgba(23, 23, 23, 0.7);
        line-height: 1.2;
    }

    body.dark-mode .template-tag {
        background: rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.7);
    }

    .template-card__actions {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex-shrink: 0;
        padding-top: 8px;
    }

    .template-card__cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        background: #dd3333;
        color: #ffffff;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 8px 16px;
        border-radius: 999px;
        cursor: pointer;
        transition: background 0.2s ease, transform 0.2s ease, opacity 0.2s ease;
        white-space: nowrap;
        position: relative;
    }

    .template-card__cta:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }

    .template-card__cta:hover:not(:disabled) {
        background: #c42b2b;
        transform: translateY(-1px);
    }

    .template-card__cta-spinner {
        margin-right: 8px;
    }

    .template-card__link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 9px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: inherit;
        text-decoration: none;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0;
        position: relative;
        white-space: nowrap;
    }

    .template-card__link-icon {
        font-size: 8px;
    }

    .template-card__link::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -2px;
        width: 100%;
        height: 1px;
        background: currentColor;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .template-card__link:hover::after {
        transform: scaleX(1);
    }

    .templates-pagination {
        display: flex;
        justify-content: center;
    }

    .template-preview-dialog :deep(.p-dialog-content) {
        padding: 0;
        background: transparent;
    }

    .template-preview {
        display: flex;
        flex-direction: column;
        gap: 18px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        overflow: hidden;
    }

    body.dark-mode .template-preview {
        background: rgba(17, 17, 17, 0.95);
    }

    .template-preview__media {
        width: 100%;
        background: rgba(23, 23, 23, 0.06);
    }

    .template-preview__media :deep(img) {
        width: 100%;
        height: auto;
        display: block;
    }

    .template-preview__content {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 20px 24px 24px;
    }

    .template-preview__meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .template-preview__price {
        font-family: 'Space Mono', monospace;
        font-size: 22px;
        text-transform: uppercase;
        color: #dd3333;
    }

    .template-preview__flags {
        display: flex;
        gap: 8px;
    }

    .template-preview__description {
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        line-height: 1.7;
        margin: 0;
        color: rgba(23, 23, 23, 0.8);
    }

    body.dark-mode .template-preview__description {
        color: rgba(243, 243, 243, 0.8);
    }

    .template-preview__tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .template-preview__actions {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: 10px;
    }

    .template-preview__buy {
        position: relative;
        border: none;
        background: #dd3333;
        color: #ffffff;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 12px 22px;
        border-radius: 999px;
        cursor: pointer;
        transition: background 0.2s ease, opacity 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .template-preview__buy:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }

    .template-preview__buy:hover:not(:disabled) {
        background: #c42b2b;
    }

    .template-preview__buy-spinner {
        margin-right: 8px;
    }

    .template-preview__link {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: inherit;
        text-decoration: none;
        position: relative;
        align-self: flex-start;
    }

    .template-preview__link::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -2px;
        width: 100%;
        height: 1px;
        background: currentColor;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .template-preview__link:hover::after {
        transform: scaleX(1);
    }

    @media (min-width: 768px) {
        .templates-page {
            padding: 36px 40px 140px;
            gap: 60px;
        }

        .template-card__actions {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .template-card__cta {
            width: auto;
        }

        .template-card__link {
            margin-left: auto;
        }

        .template-preview {
            flex-direction: row;
            gap: 0;
        }

        .template-preview__media {
            flex: 1 1 55%;
        }

        .template-preview__content {
            flex: 1 1 45%;
        }

        .template-preview__actions {
            flex-direction: row;
            align-items: center;
            justify-content: flex-start;
        }
    }

    /* Modal de email para invitados */
    .template-email-modal__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(24px, 5vw, 32px);
        font-weight: 400;
        text-transform: uppercase;
        color: var(--qode-heading-color);
    }

    .template-email-modal__content {
        padding: 20px 0;
    }

    .template-email-modal__message {
        margin: 0 0 24px;
        font-family: 'Inter', sans-serif;
        font-size: 16px;
        line-height: 1.6;
        color: var(--qode-text-color);
    }

    .template-email-modal__form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .template-email-modal__field {
        width: 100%;
    }

    .template-email-modal__field :deep(.p-inputtext) {
        width: 100%;
        border: none;
        border-bottom: 1px solid rgba(23, 23, 23, 0.6);
        background: transparent;
        font-family: 'Inter', sans-serif;
        font-size: 16px;
        color: var(--qode-text-color);
        padding: 12px 0;
        border-radius: 0;
        box-shadow: none;
    }

    body.dark-mode .template-email-modal__field :deep(.p-inputtext) {
        border-bottom-color: rgba(255, 255, 255, 0.6);
    }

    .template-email-modal__field :deep(.p-inputtext:focus) {
        border-bottom-color: var(--qode-text-color);
        box-shadow: none;
    }

    .template-email-modal__field :deep(.p-inputtext::placeholder) {
        color: rgba(23, 23, 23, 0.8);
        font-family: 'Inter', sans-serif;
    }

    body.dark-mode .template-email-modal__field :deep(.p-inputtext::placeholder) {
        color: rgba(255, 255, 255, 0.8);
    }

    .download-error-dialog__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(24px, 5vw, 32px);
        font-weight: 400;
        text-transform: uppercase;
        color: var(--qode-heading-color);
    }

    .download-error-dialog__content {
        padding: 20px 0;
    }

    .download-error-dialog__message {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 16px;
        line-height: 1.6;
        color: var(--qode-text-color);
    }

    .download-error-dialog :deep(.p-dialog-footer) {
        padding-top: 20px;
        padding-bottom: 0;
    }

    .download-error-dialog :deep(.p-dialog-footer .qodef-button) {
        margin: 0;
    }

    /* Los estilos de modales están ahora en app.css como reglas globales */
</style>
