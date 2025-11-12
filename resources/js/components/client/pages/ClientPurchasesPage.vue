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
            <button @click="purchasesStore.fetchPurchases()" class="qodef-button qodef-button--primary">
                Reintentar
            </button>
        </div>

        <div v-else-if="purchasesStore.purchases.value.length === 0" class="client-purchases__empty">
            <p>No has realizado compras aún</p>
            <RouterLink :to="{ name: 'templates' }" class="qodef-button qodef-button--primary">
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
                            <button @click="copyCode(purchase.purchase_code)" class="client-purchase-card__copy-button"
                                v-tooltip.bottom="'Copiar código'">
                                <i class="pi pi-copy"></i>
                            </button>
                        </div>
                        <div class="client-purchase-card__code-value">
                            {{ purchase.purchase_code }}
                        </div>
                        <button @click="handleResendCode(purchase)" class="qodef-button qodef-button--ghost"
                            :disabled="isResending">
                            <i class="pi pi-envelope"></i>
                            Reenviar código por email
                        </button>
                    </div>
                    <div class="client-purchase-card__actions">
                        <a :href="getDownloadUrl(purchase.template?.slug, purchase.purchase_code)" target="_blank"
                            class="qodef-button qodef-button--primary">
                            <i class="pi pi-download"></i>
                            Descargar plantilla
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted } from 'vue';
    import { RouterLink } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import { useClientPurchases } from '../../../composables/useClientPurchases.js';

    const toast = useToast();
    const purchasesStore = useClientPurchases();
    const isResending = ref(false);

    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    const formatPrice = (price, currency = 'USD') => {
        if (!price) return 'N/A';
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: currency,
        }).format(price);
    };

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

    const getDownloadUrl = (slug, code) => {
        if (!slug || !code) return '#';
        return `/api/downloads/${slug}?code=${code}`;
    };

    onMounted(async () => {
        await purchasesStore.fetchPurchases();
    });
</script>

<style scoped>
    .client-purchases {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .client-purchases__header {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .client-purchases__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: 32px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .client-purchases__subtitle {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        opacity: 0.7;
    }

    .client-purchases__loading,
    .client-purchases__error,
    .client-purchases__empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        padding: 60px 20px;
        text-align: center;
    }

    .client-purchases__error p,
    .client-purchases__empty p {
        margin: 0;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        opacity: 0.7;
    }

    .client-purchases__list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 24px;
    }

    @media (max-width: 880px) {
        .client-purchases__list {
            grid-template-columns: 1fr;
        }
    }

    .client-purchase-card {
        background: var(--qode-background-color);
        border: 1px solid rgba(23, 23, 23, 0.1);
        border-radius: 16px;
        overflow: hidden;
    }

    body.dark-mode .client-purchase-card {
        border-color: rgba(255, 255, 255, 0.08);
    }

    .client-purchase-card__header {
        padding: 20px 24px;
        border-bottom: 1px solid rgba(23, 23, 23, 0.1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    body.dark-mode .client-purchase-card__header {
        border-bottom-color: rgba(255, 255, 255, 0.08);
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
        padding: 24px;
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

    .client-purchase-card__actions {
        display: flex;
        justify-content: flex-end;
    }
</style>

