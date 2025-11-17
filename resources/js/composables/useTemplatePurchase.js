import { ref } from 'vue';
import { useAuth } from './useAuth';
import { useAuthModal } from './useAuthModal';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

/**
 * Composable para manejar la lógica de compra y descarga de plantillas
 */
export function useTemplatePurchase() {
    const auth = useAuth();
    const { openAuthModal } = useAuthModal();
    const toast = useToast();

    const isDownloading = ref(false);
    const downloadingTemplateId = ref(null);
    const showEmailModal = ref(false);
    const guestEmail = ref('');
    const isCreatingCheckout = ref(false);
    const pendingTemplate = ref(null);
    const showDownloadErrorDialog = ref(false);
    const downloadErrorMessage = ref('');

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

    return {
        isDownloading,
        downloadingTemplateId,
        showEmailModal,
        guestEmail,
        isCreatingCheckout,
        pendingTemplate,
        showDownloadErrorDialog,
        downloadErrorMessage,
        handleDownload,
        handleAuthenticatedCheckout,
        handleGuestCheckout,
        handlePrimaryAction,
    };
}

