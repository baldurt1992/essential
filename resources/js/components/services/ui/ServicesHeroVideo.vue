<template>
    <section class="services-hero-video">
        <div class="services-hero-video__frame">
            <video :key="videoSrc" class="services-hero-video__element" autoplay muted playsinline loop preload="auto"
                :src="videoSrc" ref="videoElement" @error="onVideoError" />
        </div>
    </section>
</template>

<script setup>
    import { watch, onMounted, ref, nextTick } from 'vue';

    const props = defineProps({
        videoSrc: {
            type: String,
            required: true,
        },
    });

    const videoElement = ref(null);

    const reloadAndPlay = async () => {
        await nextTick();
        const el = videoElement.value;
        if (!el) {
            return;
        }
        el.load();
        el.play().catch(() => {
            /* autoplay puede bloquearse hasta interacción; muted suele permitirlo */
        });
    };

    const onVideoError = () => {
        if (!import.meta.env.DEV) {
            return;
        }
        const el = videoElement.value;
        // eslint-disable-next-line no-console
        console.warn('[ServicesHeroVideo] error al cargar video de cabecera', {
            src: el?.currentSrc || props.videoSrc,
            code: el?.error?.code,
            message: el?.error?.message,
        });
    };

    onMounted(() => {
        reloadAndPlay();
    });

    watch(() => props.videoSrc, reloadAndPlay);
</script>

<style scoped>
    .services-hero-video {
        position: relative;
        width: 100%;
        height: 100vh;
        min-height: 645px;
        max-height: 100vh;
        margin: 0;
        padding: 0;
        overflow: hidden;
        z-index: 0;
    }

    .services-hero-video__frame {
        position: absolute;
        inset: 0;
        margin: 0;
        padding: 0;
    }

    /* cover + contenedor fijo: sin deformar, sin desbordar la cabecera */
    .services-hero-video__element {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center center;
        margin: 0;
        padding: 0;
    }
</style>

