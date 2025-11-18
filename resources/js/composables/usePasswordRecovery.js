import { ref, computed, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';
import { useAuth } from './useAuth';

/**
 * Composable para manejar la lógica de recuperación de contraseña
 * Incluye: email, OTP, nueva contraseña, countdown, validación, API calls
 */
export function usePasswordRecovery() {
    const auth = useAuth();

    // Step 1: Email
    const email = ref('');
    const emailError = ref(null);
    const sendingEmail = ref(false);

    // Step 2: OTP
    const otpInput = ref('');
    const otpError = ref(null);
    const verifying = ref(false);
    const resending = ref(false);
    const countdown = ref(60);
    const countdownInterval = ref(null);

    // Step 3: New Password
    const newPassword = ref('');
    const confirmPassword = ref('');
    const passwordError = ref(null);
    const confirmPasswordError = ref(null);
    const updating = ref(false);

    // General
    const currentStep = ref('email'); // 'email' | 'otp' | 'new-password' | 'success'
    const generalError = computed(() => auth.error.value);

    /**
     * Resetea todo el formulario
     */
    const resetForm = () => {
        email.value = '';
        emailError.value = null;
        otpInput.value = '';
        otpError.value = null;
        newPassword.value = '';
        confirmPassword.value = '';
        passwordError.value = null;
        confirmPasswordError.value = null;
        currentStep.value = 'email';
        stopCountdown();
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
     * Envía el código OTP al email
     */
    const sendRecoveryCode = async () => {
        if (!email.value || !email.value.includes('@')) {
            emailError.value = 'Ingresa un correo electrónico válido';
            return { success: false };
        }

        sendingEmail.value = true;
        emailError.value = null;
        auth.clearErrors();

        try {
            await axios.get('/sanctum/csrf-cookie');
            await axios.post('/password/recovery', {
                email: email.value,
            });

            currentStep.value = 'otp';
            startCountdown();
            return { success: true };
        } catch (error) {
            if (error.response?.status === 422) {
                const errors = error.response.data.errors ?? {};
                emailError.value = errors.email?.[0] ?? 'El correo electrónico no está registrado.';
            } else {
                emailError.value = error.response?.data?.message ?? 'Ocurrió un error al enviar el código.';
            }
            return { success: false };
        } finally {
            sendingEmail.value = false;
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
            const response = await axios.post('/password/verify-otp', {
                email: email.value,
                otp: otpInput.value,
            });

            if (response.data.verified) {
                currentStep.value = 'new-password';
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
            await axios.post('/password/recovery', {
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
     * Actualiza la contraseña
     */
    const updatePassword = async () => {
        // Validaciones
        passwordError.value = null;
        confirmPasswordError.value = null;

        if (!newPassword.value || newPassword.value.length < 8) {
            passwordError.value = 'La contraseña debe tener al menos 8 caracteres';
            return { success: false };
        }

        if (newPassword.value !== confirmPassword.value) {
            confirmPasswordError.value = 'Las contraseñas no coinciden';
            return { success: false };
        }

        updating.value = true;
        auth.clearErrors();

        try {
            await axios.get('/sanctum/csrf-cookie');
            await axios.post('/password/reset', {
                email: email.value,
                otp: otpInput.value,
                password: newPassword.value,
                password_confirmation: confirmPassword.value,
            });

            currentStep.value = 'success';
            return { success: true };
        } catch (error) {
            if (error.response?.status === 422) {
                const errors = error.response.data.errors ?? {};
                passwordError.value = errors.password?.[0] ?? null;
                confirmPasswordError.value = errors.password_confirmation?.[0] ?? null;
            } else {
                passwordError.value = error.response?.data?.message ?? 'Ocurrió un error al actualizar la contraseña.';
            }
            return { success: false };
        } finally {
            updating.value = false;
        }
    };

    /**
     * Inicializa el countdown cuando el modal se abre
     */
    const initializeCountdown = (visible) => {
        if (visible && currentStep.value === 'otp') {
            startCountdown();
        } else {
            stopCountdown();
        }
    };

    // Limpiar interval al desmontar
    onBeforeUnmount(() => {
        stopCountdown();
    });

    return {
        // State - Email
        email,
        emailError,
        sendingEmail,

        // State - OTP
        otpInput,
        otpError,
        verifying,
        resending,
        countdown,

        // State - New Password
        newPassword,
        confirmPassword,
        passwordError,
        confirmPasswordError,
        updating,

        // State - General
        currentStep,
        generalError,

        // Actions
        resetForm,
        startCountdown,
        stopCountdown,
        sendRecoveryCode,
        verifyOtp,
        resendOtp,
        updatePassword,
        initializeCountdown,
    };
}

