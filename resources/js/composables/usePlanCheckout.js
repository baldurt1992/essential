import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from './useAuth';
import { useAuthModal } from './useAuthModal';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

/**
 * Composable para manejar la lógica de checkout de planes
 */
export function usePlanCheckout() {
    const router = useRouter();
    const auth = useAuth();
    const { openAuthModal } = useAuthModal();
    const toast = useToast();

    const isCreatingCheckout = ref(null);
    const showAuthModal = ref(false);

    const handleCheckout = async (plan, event) => {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        if (isCreatingCheckout.value) {
            return;
        }

        // Verificar si el usuario está autenticado
        if (!auth.isAuthenticated.value) {
            showAuthModal.value = true;
            return;
        }

        isCreatingCheckout.value = plan.id;

        try {
            const payload = {
                success_url: `${window.location.origin}/planes?success=true`,
                cancel_url: `${window.location.origin}/planes?canceled=true`,
            };

            // Si el usuario está autenticado, usar su email automáticamente
            if (auth.isAuthenticated.value && auth.user.value?.email) {
                payload.email = auth.user.value.email;
            }

            const response = await axios.post(`/api/plans/${plan.uuid}/checkout`, payload);

            if (response.data?.checkout_url) {
                window.location.href = response.data.checkout_url;
            } else {
                throw new Error('No se recibió la URL de checkout');
            }
        } catch (error) {
            console.error('[PlansPage] Error creating checkout session', error);
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: error.response?.data?.message ?? 'No pudimos crear la sesión de checkout. Por favor, intenta de nuevo.',
                life: 5000,
            });
        } finally {
            isCreatingCheckout.value = null;
        }
    };

    const openLoginModal = () => {
        showAuthModal.value = false;
        openAuthModal('login');
    };

    const openRegisterModal = () => {
        showAuthModal.value = false;
        openAuthModal('register');
    };

    return {
        isCreatingCheckout,
        showAuthModal,
        handleCheckout,
        openLoginModal,
        openRegisterModal,
    };
}

