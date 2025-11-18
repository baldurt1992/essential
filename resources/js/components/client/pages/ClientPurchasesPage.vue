<template>
    <div class="client-purchases">
        <div class="client-purchases__header">
            <h2 class="client-purchases__title">Mis Compras</h2>
            <p class="client-purchases__subtitle">Gestiona tus plantillas compradas y códigos de compra</p>
        </div>

        <div v-if="purchasesStore.isLoading.value" class="client-purchases__loading">
            <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
            <p>Cargando compras...</p>
        </div>

        <div v-else-if="purchasesStore.hasError.value" class="client-purchases__error">
            <p>{{ purchasesStore.state.error }}</p>
            <button @click="purchasesStore.fetchPurchases()" class="essential-button essential-button--primary">
                Reintentar
            </button>
        </div>

        <div v-else-if="purchasesStore.purchases.value.length === 0" class="client-purchases__empty">
            <p>No has realizado compras aún</p>
            <RouterLink :to="{ name: 'templates' }" class="essential-button essential-button--primary">
                Explorar plantillas
            </RouterLink>
        </div>

        <div v-else class="client-purchases__list">
            <div v-for="purchase in purchasesStore.purchases.value" :key="purchase.uuid" class="client-purchase-card">
                <div class="client-purchase-card__header">
                    <div class="client-purchase-card__template">
                        <img v-if="purchase.template?.preview_url" :src="purchase.template.preview_url"
                            :alt="purchase.template.title" class="client-purchase-card__image" />
                        <div class="client-purchase-card__info">
                            <h3>{{ purchase.template?.title }}</h3>
                            <p class="client-purchase-card__date">
                                Comprado el {{ formatDate(purchase.purchased_at) }}
                            </p>
                        </div>
                    </div>
                    <div class="client-purchase-card__price">
                        {{ formatPrice(purchase.amount, purchase.currency) }}
                    </div>
                </div>
                <div class="client-purchase-card__content">
                    <div class="client-purchase-card__code-section">
                        <div class="client-purchase-card__code-header">
                            <span class="client-purchase-card__code-label">Código de compra</span>
                            <button v-if="getPurchaseState(purchase.uuid).code_validated"
                                @click="copyCode(purchase.purchase_code)" class="client-purchase-card__copy-button"
                                v-tooltip.bottom="'Copiar código'">
                                <i class="pi pi-copy"></i>
                            </button>
                        </div>
                        <div v-if="getPurchaseState(purchase.uuid).code_validated"
                            class="client-purchase-card__code-value">
                            {{ purchase.purchase_code }}
                        </div>
                        <div v-else class="client-purchase-card__code-input-section">
                            <input v-model="getPurchaseState(purchase.uuid).code_input" type="text"
                                placeholder="XXXX-XXXX-XXXX" class="client-purchase-card__code-input"
                                :class="{ 'client-purchase-card__code-input--error': getPurchaseState(purchase.uuid).code_error }"
                                maxlength="14" @input="formatCodeInput(purchase)" />
                            <p v-if="getPurchaseState(purchase.uuid).code_error"
                                class="client-purchase-card__code-error">
                                {{ getPurchaseState(purchase.uuid).code_error }}
                            </p>
                            <button @click="handleValidateCode(purchase)"
                                class="essential-button essential-button--primary"
                                :disabled="!getPurchaseState(purchase.uuid).code_input || getPurchaseState(purchase.uuid).code_input.length !== 14 || isValidating">
                                <span v-if="!isValidating">Validar código</span>
                                <span v-else>Validando...</span>
                            </button>
                        </div>
                        <button @click="handleResendCode(purchase)" class="essential-button essential-button--ghost"
                            :disabled="isResending">
                            <i class="pi pi-envelope"></i>
                            Reenviar código por email
                        </button>
                    </div>
                    <div v-if="getPurchaseState(purchase.uuid).code_validated && getPurchaseState(purchase.uuid).download_url"
                        class="client-purchase-card__actions">
                        <button @click="handleDownload(purchase)" class="essential-button essential-button--primary"
                            :disabled="isDownloading">
                            <i class="pi pi-download"></i>
                            <span v-if="!isDownloading">Descargar plantilla</span>
                            <span v-else>Descargando...</span>
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
    import { ref, reactive, onMounted, computed } from 'vue';
    import { RouterLink } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import Dialog from 'primevue/dialog';
    import Button from 'primevue/button';
    import { useClientPurchases } from '../../../composables/useClientPurchases.js';
    import { useClientFormatting } from '../../../composables/useClientFormatting.js';
    import axios from 'axios';

    const toast = useToast();
    const purchasesStore = useClientPurchases();
    const isResending = ref(false);
    const isValidating = ref(false);
    const isDownloading = ref(false);
    const showDownloadErrorDialog = ref(false);
    const downloadErrorMessage = ref('');

    // Estado local reactivo para cada compra (para evitar modificar objetos readonly del store)
    const purchaseStates = reactive({});

    const { formatDate, formatPrice } = useClientFormatting();

    const copyCode = async (code) => {
        try {
            await navigator.clipboard.writeText(code);
            toast.add({
                severity: 'success',
                summary: 'Código copiado',
                detail: 'El código de compra ha sido copiado al portapapeles.',
                life: 3000,
            });
        } catch (error) {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se pudo copiar el código',
                life: 3000,
            });
        }
    };

    const handleResendCode = async (purchase) => {
        isResending.value = true;
        const result = await purchasesStore.resendPurchaseCode(purchase.uuid);
        isResending.value = false;

        if (result.success) {
            toast.add({
                severity: 'success',
                summary: 'Código reenviado',
                detail: 'El código de compra ha sido enviado a tu correo electrónico.',
                life: 5000,
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: result.error || 'No se pudo reenviar el código',
                life: 5000,
            });
        }
    };

    const getPurchaseState = (uuid) => {
        if (!purchaseStates[uuid]) {
            // Intentar cargar desde localStorage si ya fue validado previamente
            const savedState = localStorage.getItem(`purchase_${uuid}`);
            let initialState;

            if (savedState) {
                const parsed = JSON.parse(savedState);
                initialState = {
                    code_input: '', // Siempre vacío, no mostramos el código en el input
                    code_validated: parsed.code_validated || false,
                    code_error: null,
                    download_url: parsed.download_url || null,
                };
            } else {
                initialState = {
                    code_input: '',
                    code_validated: false,
                    code_error: null,
                    download_url: null,
                };
            }

            purchaseStates[uuid] = initialState;
        }
        return purchaseStates[uuid];
    };

    const savePurchaseState = (uuid, state) => {
        // Guardar en localStorage solo si el código fue validado
        if (state.code_validated && state.download_url) {
            localStorage.setItem(`purchase_${uuid}`, JSON.stringify({
                code_validated: true,
                download_url: state.download_url,
            }));
        }
    };

    const formatCodeInput = (purchase) => {
        const state = getPurchaseState(purchase.uuid);
        // Formatear el código mientras se escribe: XXXX-XXXX-XXXX
        let value = state.code_input.replace(/[^A-Z0-9]/gi, '').toUpperCase();
        if (value.length > 12) value = value.substring(0, 12);

        // Agregar guiones
        if (value.length > 8) {
            value = value.substring(0, 8) + '-' + value.substring(8);
        }
        if (value.length > 4) {
            value = value.substring(0, 4) + '-' + value.substring(4);
        }

        state.code_input = value;
        state.code_error = null;
    };

    const handleValidateCode = async (purchase) => {
        const state = getPurchaseState(purchase.uuid);

        if (!state.code_input || state.code_input.length !== 14) {
            return;
        }

        isValidating.value = true;
        state.code_error = null;

        const result = await purchasesStore.validatePurchaseCode(purchase.uuid, state.code_input);

        if (result.success) {
            state.code_validated = true;
            state.download_url = result.download_url;
            // Guardar en localStorage para persistir la validación
            savePurchaseState(purchase.uuid, state);
            toast.add({
                severity: 'success',
                summary: 'Código válido',
                detail: 'El código ha sido validado correctamente. Ya puedes descargar la plantilla.',
                life: 5000,
            });
        } else {
            state.code_error = result.error || 'El código no es válido';
            toast.add({
                severity: 'error',
                summary: 'Código inválido',
                detail: result.error || 'El código de compra no es válido.',
                life: 5000,
            });
        }

        isValidating.value = false;
    };

    const handleDownload = async (purchase) => {
        const state = getPurchaseState(purchase.uuid);

        if (!state.download_url) {
            return;
        }

        isDownloading.value = true;

        try {
            // Intentar descargar el archivo
            const response = await axios.get(state.download_url, {
                responseType: 'blob',
                timeout: 300000, // 5 minutos
            });

            // Si la respuesta es exitosa, crear un enlace temporal y descargar
            const blob = new Blob([response.data]);
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = purchase.template?.slug + '.zip' || 'template.zip';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        } catch (error) {
            // Interceptar errores 503 (archivo no disponible) y otros errores
            if (error.response?.status === 503) {
                const errorData = error.response.data;
                downloadErrorMessage.value = errorData?.message || 'El archivo no está disponible temporalmente. Inténtalo más tarde.';
                showDownloadErrorDialog.value = true;
            } else if (error.response?.status === 403) {
                const errorData = error.response.data;
                downloadErrorMessage.value = errorData?.message || 'No tienes permiso para descargar este archivo.';
                showDownloadErrorDialog.value = true;
            } else {
                downloadErrorMessage.value = 'Ocurrió un error al intentar descargar el archivo. Inténtalo más tarde.';
                showDownloadErrorDialog.value = true;
            }
        } finally {
            isDownloading.value = false;
        }
    };

    onMounted(async () => {
        await purchasesStore.fetchPurchases();
        // Inicializar estado local para cada compra
        purchasesStore.purchases.value.forEach(purchase => {
            getPurchaseState(purchase.uuid);
        });
    });
</script>

<style scoped>
    /* Client Purchases Specific Styles */
    .client-purchase-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .client-purchase-card__template {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
    }

    .client-purchase-card__image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }

    .client-purchase-card__info {
        flex: 1;
    }

    .client-purchase-card__info h3 {
        margin: 0 0 4px;
        font-family: 'Space Mono', monospace;
        font-size: 16px;
    }

    .client-purchase-card__date {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        opacity: 0.7;
    }

    .client-purchase-card__price {
        font-family: 'Space Mono', monospace;
        font-size: 18px;
        font-weight: 600;
    }

    .client-purchase-card__content {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .client-purchase-card__code-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .client-purchase-card__code-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .client-purchase-card__code-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 11px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        opacity: 0.7;
    }

    .client-purchase-card__copy-button {
        background: none;
        border: none;
        color: var(--qode-text-color);
        cursor: pointer;
        padding: 6px;
        border-radius: 6px;
        transition: background 0.2s ease;
    }

    .client-purchase-card__copy-button:hover {
        background: rgba(23, 23, 23, 0.05);
    }

    body.dark-mode .client-purchase-card__copy-button:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .client-purchase-card__code-value {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 16px;
        padding: 12px;
        background: rgba(23, 23, 23, 0.03);
        border-radius: 8px;
        border: 1px solid rgba(23, 23, 23, 0.1);
        letter-spacing: 0.1em;
    }

    body.dark-mode .client-purchase-card__code-value {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .client-purchase-card__code-input-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .client-purchase-card__code-input {
        width: 100%;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 16px;
        padding: 12px;
        background: rgba(23, 23, 23, 0.03);
        border-radius: 8px;
        border: 1px solid rgba(23, 23, 23, 0.1);
        letter-spacing: 0.1em;
        text-transform: uppercase;
        outline: none;
        transition: border-color 0.2s ease;
    }

    body.dark-mode .client-purchase-card__code-input {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.08);
        color: var(--qode-text-color);
    }

    .client-purchase-card__code-input:focus {
        border-color: #dd3333;
    }

    .client-purchase-card__code-input--error {
        border-color: #dd3333;
    }

    .client-purchase-card__code-error {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 12px;
        color: #dd3333;
    }

    .client-purchase-card__actions {
        display: flex;
        justify-content: flex-end;
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
