import { ref } from 'vue';

// Estado global del modal de autenticación
const authModalVisible = ref(false);
const authModalForm = ref('login');

export const useAuthModal = () => {
    const openAuthModal = (form = 'login') => {
        authModalForm.value = form;
        authModalVisible.value = true;
    };

    const closeAuthModal = () => {
        authModalVisible.value = false;
    };

    return {
        authModalVisible,
        authModalForm,
        openAuthModal,
        closeAuthModal,
    };
};

