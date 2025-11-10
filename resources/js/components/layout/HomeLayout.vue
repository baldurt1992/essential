<template>
    <div class="app-shell">
        <Header />

        <main class="app-content">
            <RouterView />
        </main>

        <Footer />
    </div>
</template>

<script setup>
    import { RouterView } from 'vue-router';
    import Header from './Header.vue';
    import Footer from './Footer.vue';
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
