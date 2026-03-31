import { ref } from 'vue';
import axios from 'axios';

const FALLBACK_SRC = '/videos/main-home-video-2.mp4';

/**
 * Video de cabecera en /servicios (configuración pública).
 */
export function useServicesHero() {
    const videoSrc = ref(FALLBACK_SRC);
    const loading = ref(false);

    const loadHeroVideo = async () => {
        loading.value = true;
        try {
            const response = await axios.get('/api/services-hero');
            const payload = response.data?.data ?? response.data;
            videoSrc.value = payload?.video_url || FALLBACK_SRC;
        } catch {
            videoSrc.value = FALLBACK_SRC;
        } finally {
            loading.value = false;
        }
    };

    return {
        videoSrc,
        loading,
        loadHeroVideo,
    };
}
