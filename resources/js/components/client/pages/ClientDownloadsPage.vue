<template>
    <div class="client-downloads">
        <div class="client-downloads__header">
            <h2 class="client-downloads__title">Mis Descargas</h2>
            <p class="client-downloads__subtitle">Historial de todas tus descargas de plantillas</p>
        </div>

        <div v-if="downloadsStore.loading.value" class="client-downloads__loading">
            <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
            <p>Cargando descargas...</p>
        </div>

        <div v-else-if="downloadsStore.error.value" class="client-downloads__error">
            <p>{{ downloadsStore.error.value }}</p>
            <button @click="downloadsStore.fetchDownloads()" class="essential-button essential-button--primary">
                Reintentar
            </button>
        </div>

        <div v-else-if="!downloadsStore.hasDownloads.value" class="client-downloads__empty">
            <p>No has realizado descargas aún</p>
            <RouterLink :to="{ name: 'templates' }" class="essential-button essential-button--primary">
                Explorar plantillas
            </RouterLink>
        </div>

        <div v-else class="client-downloads__list">
            <div v-for="download in downloadsStore.downloads.value" :key="download.uuid" class="client-download-card">
                <div class="client-download-card__header">
                    <div class="client-download-card__template">
                        <img v-if="download.template?.preview_image_url" :src="download.template.preview_image_url"
                            :alt="download.template.title" class="client-download-card__image" />
                        <div class="client-download-card__info">
                            <h3>{{ download.template?.title }}</h3>
                            <p class="client-download-card__date">
                                Descargado el {{ formatDate(download.last_downloaded_at) }}
                            </p>
                        </div>
                    </div>
                    <div class="client-download-card__badge">
                        <i class="pi pi-check-circle"></i>
                        <span>{{ download.download_count }} {{ download.download_count === 1 ? 'descarga' : 'descargas'
                        }}</span>
                    </div>
                </div>
                <div class="client-download-card__content">
                    <div class="client-download-card__details">
                        <div class="client-download-card__detail">
                            <span class="client-download-card__detail-label">Última descarga:</span>
                            <span class="client-download-card__detail-value">{{ formatDate(download.last_downloaded_at)
                            }}</span>
                        </div>
                        <div v-if="download.download_limit" class="client-download-card__detail">
                            <span class="client-download-card__detail-label">Límite:</span>
                            <span class="client-download-card__detail-value">{{ download.download_limit }}
                                descargas</span>
                        </div>
                        <div v-else class="client-download-card__detail">
                            <span class="client-download-card__detail-label">Límite:</span>
                            <span class="client-download-card__detail-value">Ilimitado</span>
                        </div>
                        <div v-if="download.expires_at" class="client-download-card__detail">
                            <span class="client-download-card__detail-label">Válido hasta:</span>
                            <span class="client-download-card__detail-value">{{ formatDate(download.expires_at)
                            }}</span>
                        </div>
                    </div>
                    <div class="client-download-card__actions">
                        <button @click="handleDownload(download)" class="essential-button essential-button--primary"
                            :disabled="isDownloading && downloadingId === download.uuid">
                            <i v-if="isDownloading && downloadingId === download.uuid"
                                class="pi pi-spin pi-spinner"></i>
                            <i v-else class="pi pi-download"></i>
                            Descargar de nuevo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dialog de error de descarga -->
    <Dialog v-model:visible="showDownloadErrorDialog" modal :style="{ width: '90%', maxWidth: '500px' }"
        :closable="true" :draggable="false">
        <template #header>
            <h2 class="download-error-dialog__title">Error al descargar</h2>
        </template>

        <div class="download-error-dialog__content">
            <p class="download-error-dialog__message">{{ downloadErrorMessage }}</p>
        </div>

        <template #footer>
            <Button @click="showDownloadErrorDialog = false" class="essential-button essential-button--primary">
                Entendido
            </Button>
        </template>
    </Dialog>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import axios from 'axios';
    import Dialog from 'primevue/dialog';
    import Button from 'primevue/button';
    import { useClientDownloads } from '../../../composables/useClientDownloads.js';
    import { useClientFormatting } from '../../../composables/useClientFormatting.js';

    const downloadsStore = useClientDownloads();
    const { formatDate } = useClientFormatting();

    const isDownloading = ref(false);
    const downloadingId = ref(null);
    const showDownloadErrorDialog = ref(false);
    const downloadErrorMessage = ref('');

    const handleDownload = async (download) => {
        if (!download.template?.slug) {
            console.error('[ClientDownloads] No se puede descargar: falta slug de la plantilla', { download });
            return;
        }

        isDownloading.value = true;
        downloadingId.value = download.uuid;

        try {
            console.log('[ClientDownloads] Iniciando descarga', { template_slug: download.template.slug, download_uuid: download.uuid });

            const downloadUrl = `/api/downloads/${download.template.slug}`;
            const response = await axios.get(downloadUrl, {
                responseType: 'blob',
                timeout: 300000, // 5 minutos
            });

            // Si la respuesta es exitosa, crear un enlace temporal y descargar
            const blob = new Blob([response.data]);
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = download.template.slug + '.zip';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);

            console.log('[ClientDownloads] Descarga completada exitosamente', { template_slug: download.template.slug });

            // Refrescar la lista de descargas para actualizar el contador
            await downloadsStore.fetchDownloads();
        } catch (error) {
            console.error('[ClientDownloads] Error al descargar', { error, template_slug: download.template.slug });

            let errorMessage = 'Ocurrió un error al intentar descargar el archivo. Inténtalo más tarde.';

            if (error.response?.status === 503) {
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
            downloadingId.value = null;
        }
    };

    onMounted(async () => {
        await downloadsStore.fetchDownloads();
    });
</script>

<style scoped>

    /* Client Downloads Specific Styles */
    .client-download-card {
        background: #ffffff;
        border: 1px solid rgba(18, 18, 18, 0.1);
        border-radius: 16px;
        padding: 24px;
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    body.dark-mode .client-download-card {
        background: #1a1a1a;
        border-color: rgba(255, 255, 255, 0.1);
    }

    .client-download-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    body.dark-mode .client-download-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .client-download-card__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .client-download-card__template {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
    }

    .client-download-card__image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid rgba(18, 18, 18, 0.1);
    }

    body.dark-mode .client-download-card__image {
        border-color: rgba(255, 255, 255, 0.1);
    }

    .client-download-card__info h3 {
        font-family: 'Inter', sans-serif;
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 6px;
        color: inherit;
    }

    .client-download-card__badge {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.2);
        border-radius: 8px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 12px;
        color: #22c55e;
        white-space: nowrap;
    }

    body.dark-mode .client-download-card__badge {
        background: rgba(34, 197, 94, 0.15);
        border-color: rgba(34, 197, 94, 0.3);
    }

    .client-download-card__badge .pi {
        font-size: 14px;
    }

    .client-download-card__content {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .client-download-card__details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .client-download-card__detail {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .client-download-card__detail-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: rgba(23, 23, 23, 0.6);
    }

    body.dark-mode .client-download-card__detail-label {
        color: rgba(243, 243, 243, 0.6);
    }

    .client-download-card__detail-value {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: inherit;
    }

    .client-download-card__actions {
        display: flex;
        gap: 12px;
        padding-top: 16px;
        border-top: 1px solid rgba(18, 18, 18, 0.1);
    }

    body.dark-mode .client-download-card__actions {
        border-top-color: rgba(255, 255, 255, 0.1);
    }

    @media (max-width: 880px) {
        .client-download-card__header {
            flex-direction: column;
            align-items: stretch;
        }

        .client-download-card__badge {
            align-self: flex-start;
        }

        .client-download-card__details {
            grid-template-columns: 1fr;
        }
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
</style>
