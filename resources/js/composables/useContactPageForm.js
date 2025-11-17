import { reactive } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

/**
 * Composable para manejar el formulario completo de contacto en ContactPage
 */
export function useContactPageForm() {
    const toast = useToast();

    const initialFormData = {
        name: '',
        email: '',
        phone: '',
        company: '',
        subject: '',
        message: '',
    };

    const contactForm = reactive({
        isOpen: false,
        submitting: false,
        data: { ...initialFormData },
        errors: {},
    });

    function openContactDialog() {
        contactForm.isOpen = true;
    }

    function handleDialogHide() {
        contactForm.errors = {};
    }

    function resetForm() {
        Object.assign(contactForm.data, initialFormData);
        contactForm.errors = {};
    }

    async function submitContact() {
        contactForm.submitting = true;
        contactForm.errors = {};

        try {
            await axios.post('/api/contact-messages', {
                ...contactForm.data,
                origin_url: window.location.href,
            });

            toast.add({
                severity: 'success',
                summary: 'Mensaje enviado',
                detail: 'Gracias por escribirnos. Te contactaremos muy pronto.',
                life: 6000,
            });

            contactForm.isOpen = false;
            resetForm();
        } catch (error) {
            if (error.response?.status === 422) {
                contactForm.errors = Object.fromEntries(
                    Object.entries(error.response.data.errors || {}).map(([field, messages]) => [field, messages[0]]),
                );
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'No pudimos enviar tu mensaje',
                    detail: 'Intenta nuevamente en unos segundos o escríbenos por WhatsApp.',
                    life: 6000,
                });
            }
        } finally {
            contactForm.submitting = false;
        }
    }

    return {
        contactForm,
        openContactDialog,
        handleDialogHide,
        submitContact,
    };
}

