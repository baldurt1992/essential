<template>
    <section class="services-hero-video">
        <div class="services-hero-video-container">
            <video class="services-hero-video-element" autoplay muted playsinline loop :src="videoSrc"
                ref="videoElement"></video>
        </div>
    </section>
</template>

<script setup>
    import { onMounted, ref } from 'vue';

    const props = defineProps({
        videoSrc: {
            type: String,
            required: true,
        },
    });

    const videoElement = ref(null);

    onMounted(() => {
        if (videoElement.value) {
            videoElement.value.play().catch(err => {
                console.warn('Error al reproducir video:', err);
            });
        }
    });
</script>

<style scoped>
    .services-hero-video {
        position: relative;
        width: 100%;
        height: 100vh;
        min-height: 645px;
        margin: 0;
        padding: 0;
        overflow: hidden;
        z-index: 0;
    }

    .services-hero-video-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
    }

    .services-hero-video-element {
        position: absolute;
        top: -120px;
        left: 0;
        width: 100%;
        height: 125%;
        object-fit: cover;
        margin: 0;
        padding: 0;
    }
</style>

