<template>
    <RouterView />
    <Toast />
</template>

<script setup>
    import { onMounted } from 'vue';
    import { RouterView } from 'vue-router';
    import Toast from 'primevue/toast';
    import { useAuth } from '../composables/useAuth.js';

    const auth = useAuth();

    onMounted(async () => {
        await auth.init();
    });
</script>
<style scoped>
    .app-shell {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .app-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
</style>
