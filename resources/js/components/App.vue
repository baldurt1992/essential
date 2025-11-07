<template>
    <div id="app">
        <Header />
        <main>
            <HomePage />
        </main>
        <Footer />
    </div>
</template>

<script setup>
    import Header from './layout/Header.vue';
    import Footer from './layout/Footer.vue';
    import HomePage from './pages/HomePage.vue';
</script>

<style scoped>
    #app {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
</style>
