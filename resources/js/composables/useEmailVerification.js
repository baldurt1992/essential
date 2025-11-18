import { ref, computed, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { useAuth } from './useAuth';

/**
 * Composable para manejar la lógica de verificación de email con OTP
 * Incluye: countdown, validación OTP, API calls (verify, resend)
 */
export function useEmailVerification(email, fromEmail = false) {
    const auth = useAuth();

    const otpInput = ref('');
    const otpError = ref(null);
    const verifying = ref(false);
    const resending = ref(false);
    const countdown = ref(60);
    const countdownInterval = ref(null);

    const generalError = computed(() => auth.error.value);

    /**
     * Resetea el formulario OTP
     */
    const resetForm = () => {
        otpInput.value = '';
        otpError.value = null;
    };

    /**
     * Inicia el countdown de 60 segundos
     */
    const startCountdown = () => {
        countdown.value = 60;
        stopCountdown();
        countdownInterval.value = setInterval(() => {
            if (countdown.value > 0) {
                countdown.value--;
            } else {
                stopCountdown();
            }
        }, 1000);
    };

    /**
     * Detiene el countdown
     */
    const stopCountdown = () => {
        if (countdownInterval.value) {
            clearInterval(countdownInterval.value);
            countdownInterval.value = null;
        }
    };

    /**
     * Verifica el código OTP
     */
    const verifyOtp = async () => {
        if (otpInput.value.length !== 6) {
            otpError.value = 'El código debe tener 6 dígitos';
            return { success: false };
        }

        verifying.value = true;
        otpError.value = null;
        auth.clearErrors();

        try {
            await axios.get('/sanctum/csrf-cookie');
            const response = await axios.post('/verify-otp', {
                email: email.value,
                otp: otpInput.value,
            });

            if (response.data.verified) {
                // Actualizar usuario en el store
                await auth.refreshUser();
                return { success: true };
            }

            return { success: false };
        } catch (error) {
            if (error.response?.status === 422) {
                const errors = error.response.data.errors ?? {};
                otpError.value = errors.otp?.[0] ?? 'El código de verificación es inválido o ha expirado.';
            } else {
                otpError.value = error.response?.data?.message ?? 'Ocurrió un error al verificar el código.';
            }
            return { success: false };
        } finally {
            verifying.value = false;
        }
    };

    /**
     * Reenvía el código OTP
     */
    const resendOtp = async () => {
        if (countdown.value > 0) {
            return;
        }

        resending.value = true;
        auth.clearErrors();

        try {
            await axios.get('/sanctum/csrf-cookie');
            await axios.post('/resend-otp', {
                email: email.value,
            });

            startCountdown();
            return { success: true };
        } catch (error) {
            // El error se mostrará en generalError
            return { success: false };
        } finally {
            resending.value = false;
        }
    };

    /**
     * Inicializa el countdown cuando el modal se abre
     */
    const initializeCountdown = (visible) => {
        if (visible) {
            if (!fromEmail.value) {
                startCountdown();
            } else {
                // Si viene desde el email, el countdown debe estar en 0 para mostrar el botón de reenviar
                countdown.value = 0;
            }
            resetForm();
            auth.clearErrors();
        } else {
            stopCountdown();
        }
    };

    // Limpiar interval al desmontar
    onBeforeUnmount(() => {
        stopCountdown();
    });

    return {
        // State
        otpInput,
        otpError,
        verifying,
        resending,
        countdown,
        generalError,

        // Actions
        resetForm,
        startCountdown,
        stopCountdown,
        verifyOtp,
        resendOtp,
        initializeCountdown,
    };
}

