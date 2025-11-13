<template>
    <div class="purchase-confirmation">
        <div v-if="isLoading" class="purchase-confirmation__loading">
            <i class="pi pi-spin pi-spinner" style="font-size: 2rem; color: var(--qode-heading-color);"></i>
            <p class="purchase-confirmation__loading-message">Verificando tu compra...</p>
        </div>

        <div v-else-if="hasError" class="purchase-confirmation__error">
            <div class="purchase-confirmation__icon">
                <i class="pi pi-times-circle"></i>
            </div>
            <h1 class="purchase-confirmation__title">Pago no completado</h1>
            <p class="purchase-confirmation__message">
                {{ displayErrorMessage }}
            </p>
            <div class="purchase-confirmation__actions">
                <RouterLink :to="{ name: 'templates' }" class="qodef-button qodef-button--ghost">
                    Volver a plantillas
                </RouterLink>
            </div>
        </div>

        <div v-else-if="purchase" class="purchase-confirmation__success">
            <div class="purchase-confirmation__icon">
                <i class="pi pi-check-circle"></i>
            </div>
            <h1 class="purchase-confirmation__title">¡Compra completada!</h1>
            <p class="purchase-confirmation__message" v-html="successMessage"></p>

            <div v-if="isGuest" class="purchase-confirmation__resend">
                <p class="purchase-confirmation__resend-message">
                    ¿No recibiste el correo? Espera un minuto y luego puedes reenviarlo.
                </p>
                <Button @click="handleResendLink" class="qodef-button qodef-button--primary"
                    :disabled="isResending || !canResend">
                    <i class="pi pi-envelope"></i>
                    <span v-if="!isResending">Reenviar enlace de descarga</span>
                    <span v-else>Enviando...</span>
                </Button>
                <p v-if="!canResend && countdown > 0" class="purchase-confirmation__countdown">
                    Podrás reenviar en <strong>{{ countdown }}</strong> segundo{{ countdown !== 1 ? 's' : '' }}
                </p>
            </div>

            <div class="purchase-confirmation__actions">
                <RouterLink :to="{ name: 'templates' }" class="qodef-button qodef-button--ghost">
                    Explorar más plantillas
                </RouterLink>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
    import { useRoute, useRouter, RouterLink } from 'vue-router';
    import { useToast } from 'primevue/usetoast';
    import axios from 'axios';

    const route = useRoute();
    const router = useRouter();
    const toast = useToast();

    const isLoading = ref(true);
    const hasError = ref(false);
    const errorMessage = ref('');
    const purchase = ref(null);
    const contactEmail = ref(null);
    const isResending = ref(false);
    const canResend = ref(false);
    const resendTimer = ref(null);
    const countdown = ref(60);
    const countdownInterval = ref(null);

    const isGuest = computed(() => purchase.value && !purchase.value.user_id);

    const displayErrorMessage = computed(() => {
        return errorMessage.value || 'No pudimos verificar tu compra. Si realizaste el pago, revisa tu correo electrónico.';
    });

    const successMessage = computed(() => {
        if (!purchase.value) return '';

        const templateTitle = purchase.value.template?.title || 'la plantilla';

        if (isGuest.value) {
            let message = `Hemos enviado el enlace de descarga de <strong>${templateTitle}</strong> a <strong>${purchase.value.guest_email}</strong>. Revisa tu correo electrónico para descargar la plantilla.`;

            if (contactEmail.value) {
                message += `<br><br>Si no recibiste el correo, no dudes en escribirnos a <a href="mailto:${contactEmail.value}" style="color: #dd3333; text-decoration: underline;">${contactEmail.value}</a>`;
            }

            return message;
        }

        return `Tu compra de <strong>${templateTitle}</strong> ha sido procesada correctamente. Revisa tu correo electrónico para obtener el código de compra.`;
    });

    const verifyPurchase = async (retryCount = 0) => {
        const sessionId = route.query.session_id;

        if (!sessionId) {
            hasError.value = true;
            errorMessage.value = 'No se proporcionó un ID de sesión válido.';
            isLoading.value = false;
            return;
        }

        try {
            const response = await axios.get(`/api/purchases/verify/${sessionId}`);

            if (response.data.purchase) {
                purchase.value = response.data.purchase;
                contactEmail.value = response.data.contact_email || null;
                // Iniciar contador de 60 segundos
                startCountdown();
            } else {
                // Si no se encontró la compra pero aún no hemos intentado muchas veces, reintentar
                // (el webhook puede estar procesando aún)
                if (retryCount < 5) {
                    setTimeout(() => {
                        verifyPurchase(retryCount + 1);
                    }, 2000); // Esperar 2 segundos antes de reintentar
                    return;
                }
                hasError.value = true;
                errorMessage.value = response.data.message || 'No se pudo verificar la compra. Si realizaste el pago, revisa tu correo electrónico.';
            }
        } catch (error) {
            // Si es un 404 y aún no hemos intentado muchas veces, reintentar
            if (error.response?.status === 404 && retryCount < 5) {
                setTimeout(() => {
                    verifyPurchase(retryCount + 1);
                }, 2000);
                return;
            }
            hasError.value = true;
            errorMessage.value = error.response?.data?.message || 'Error al verificar la compra. Si realizaste el pago, revisa tu correo electrónico.';
        } finally {
            if (purchase.value || hasError.value) {
                isLoading.value = false;
            }
        }
    };

    const handleResendLink = async () => {
        if (!purchase.value || !isGuest.value) {
            return;
        }

        isResending.value = true;

        try {
            await axios.post(`/api/purchases/${purchase.value.uuid}/resend-link`, {
                email: purchase.value.guest_email,
            });

            toast.add({
                severity: 'success',
                summary: 'Enlace reenviado',
                detail: 'El enlace de descarga ha sido enviado nuevamente a tu correo electrónico.',
                life: 5000,
            });

            canResend.value = false;
            // Reiniciar contador
            startCountdown();
        } catch (error) {
            const message = error.response?.data?.message || 'Error al reenviar el enlace.';
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: message,
                life: 5000,
            });
        } finally {
            isResending.value = false;
        }
    };

    const startCountdown = () => {
        // Limpiar intervalos anteriores
        if (countdownInterval.value) {
            clearInterval(countdownInterval.value);
        }
        if (resendTimer.value) {
            clearTimeout(resendTimer.value);
        }

        countdown.value = 60;
        canResend.value = false;

        // Actualizar contador cada segundo
        countdownInterval.value = setInterval(() => {
            countdown.value--;
            if (countdown.value <= 0) {
                clearInterval(countdownInterval.value);
                countdownInterval.value = null;
                canResend.value = true;
            }
        }, 1000);
    };

    onMounted(() => {
        verifyPurchase();
    });

    // Limpiar intervalos al desmontar
    onBeforeUnmount(() => {
        if (countdownInterval.value) {
            clearInterval(countdownInterval.value);
        }
        if (resendTimer.value) {
            clearTimeout(resendTimer.value);
        }
    });
</script>

<style scoped>
    .purchase-confirmation {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        padding: 60px 20px;
        text-align: center;
    }

    .purchase-confirmation__loading,
    .purchase-confirmation__error,
    .purchase-confirmation__success {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 24px;
        max-width: 600px;
        width: 100%;
    }

    .purchase-confirmation__icon {
        font-size: 64px;
        color: #dd3333;
        margin-bottom: 8px;
    }

    .purchase-confirmation__error .purchase-confirmation__icon {
        color: #dd3333;
    }

    .purchase-confirmation__success .purchase-confirmation__icon {
        color: #22c55e;
    }

    .purchase-confirmation__title {
        margin: 0;
        font-family: 'Lexend', sans-serif;
        font-size: clamp(32px, 6vw, 48px);
        font-weight: 400;
        text-transform: uppercase;
        color: var(--qode-heading-color);
        letter-spacing: 0.02em;
    }

    .purchase-confirmation__message {
        margin: 0;
        font-family: var(--qode-font-body);
        font-size: 17px;
        line-height: 1.6;
        color: var(--qode-text-color);
    }

    .purchase-confirmation__message strong {
        color: var(--qode-heading-color);
    }

    .purchase-confirmation__loading-message {
        margin: 0;
        font-family: var(--qode-font-body);
        font-size: 17px;
        line-height: 1.6;
        color: var(--qode-text-color);
    }

    .purchase-confirmation__resend {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        padding: 24px;
        background: rgba(23, 23, 23, 0.03);
        border-radius: 12px;
        margin-top: 8px;
    }

    body.dark-mode .purchase-confirmation__resend {
        background: rgba(255, 255, 255, 0.03);
    }

    .purchase-confirmation__resend-message {
        margin: 0;
        font-family: var(--qode-font-body);
        font-size: 14px;
        line-height: 1.5;
        color: var(--qode-text-color);
        opacity: 0.7;
    }

    .purchase-confirmation__countdown {
        margin: 0;
        font-family: var(--qode-font-body);
        font-size: 14px;
        line-height: 1.5;
        color: var(--qode-text-color);
        opacity: 0.8;
    }

    .purchase-confirmation__countdown strong {
        font-family: var(--qode-font-menu);
        font-weight: 500;
        color: var(--qode-heading-color);
        opacity: 1;
    }

    .purchase-confirmation__actions {
        display: flex;
        gap: 12px;
        margin-top: 8px;
    }

    @media (max-width: 480px) {
        .purchase-confirmation__actions {
            flex-direction: column;
            width: 100%;
        }

        .purchase-confirmation__actions .qodef-button {
            width: 100%;
        }
    }
</style>
