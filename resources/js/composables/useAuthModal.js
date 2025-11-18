import { ref } from 'vue';

// Estado global del modal de autenticación
const authModalVisible = ref(false);
const authModalForm = ref('login');
const emailVerificationModalVisible = ref(false);
const emailVerificationEmail = ref('');

export const useAuthModal = () => {
    const openAuthModal = (form = 'login') => {
        authModalForm.value = form;
        authModalVisible.value = true;
    };

    const closeAuthModal = () => {
        authModalVisible.value = false;
    };

    const openEmailVerificationModal = (email) => {
        emailVerificationEmail.value = email;
        emailVerificationModalVisible.value = true;
        // También abrir el modal de auth si no está abierto para mostrar el modal de verificación dentro
        if (!authModalVisible.value) {
            authModalVisible.value = true;
        }
    };

    const closeEmailVerificationModal = () => {
        emailVerificationModalVisible.value = false;
    };

    return {
        authModalVisible,
        authModalForm,
        emailVerificationModalVisible,
        emailVerificationEmail,
        openAuthModal,
        closeAuthModal,
        openEmailVerificationModal,
        closeEmailVerificationModal,
    };
};

