import { reactive } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

/**
 * Composable para manejar el formulario de contacto
 * @returns {Object} - { contactForm, handleContactSubmit }
 */
export function useContactForm() {
    const toast = useToast();

    const contactForm = reactive({
        email: '',
        firstName: '',
        lastName: '',
        message: '',
        submitting: false,
    });

    const handleContactSubmit = async () => {
        if (contactForm.submitting) return;

        contactForm.submitting = true;

        try {
            await axios.post('/api/contact-messages', {
                email: contactForm.email,
                name: `${contactForm.firstName} ${contactForm.lastName}`.trim(),
                message: contactForm.message,
            });

            toast.add({
                severity: 'success',
                summary: 'Mensaje enviado',
                detail: 'Tu mensaje ha sido enviado correctamente. Te responderemos pronto.',
                life: 5000,
            });

            // Reset form
            contactForm.email = '';
            contactForm.firstName = '';
            contactForm.lastName = '';
            contactForm.message = '';
        } catch (error) {
            const message = error.response?.data?.message || 'Error al enviar el mensaje. Por favor intenta de nuevo.';
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: message,
                life: 5000,
            });
        } finally {
            contactForm.submitting = false;
        }
    };

    return {
        contactForm,
        handleContactSubmit,
    };
}

