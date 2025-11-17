import { ref, computed, onBeforeUnmount, onMounted } from 'vue';

/**
 * Composable para efecto parallax en imagen basado en movimiento del mouse
 * @param {Object} options - Opciones de configuración
 * @param {number} options.maxOffset - Máximo desplazamiento en píxeles (default: 40)
 * @param {number} options.easing - Factor de suavizado (0-1, default: 0.12)
 * @param {number} options.breakpoint - Ancho mínimo para activar parallax (default: 1024)
 * @returns {Object} - { containerRef, imageStyle, handleMouseMove, resetOffsets }
 */
export function useParallax(options = {}) {
    const {
        maxOffset = 40,
        easing = 0.12,
        breakpoint = 1024,
    } = options;

    const containerRef = ref(null);
    const offsetX = ref(0);
    const offsetY = ref(0);
    const targetOffsetX = ref(0);
    const targetOffsetY = ref(0);
    const isMobile = ref(false);
    let animationFrameId = null;

    const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

    const checkMobile = () => {
        isMobile.value = window.innerWidth <= breakpoint;
    };

    onMounted(() => {
        checkMobile();
        window.addEventListener('resize', checkMobile);
    });

    const imageStyle = computed(() => {
        // No aplicar parallax en responsive
        if (isMobile.value) {
            return {};
        }
        return {
            transform: `translate(-50%, 0) translate(${offsetX.value}px, ${offsetY.value}px)`
        };
    });

    const animate = () => {
        const deltaX = targetOffsetX.value - offsetX.value;
        const deltaY = targetOffsetY.value - offsetY.value;

        offsetX.value += deltaX * easing;
        offsetY.value += deltaY * easing;

        if (Math.abs(deltaX) < 0.1 && Math.abs(deltaY) < 0.1) {
            offsetX.value = targetOffsetX.value;
            offsetY.value = targetOffsetY.value;
            animationFrameId = null;
            return;
        }

        animationFrameId = requestAnimationFrame(animate);
    };

    const ensureAnimation = () => {
        if (animationFrameId === null) {
            animationFrameId = requestAnimationFrame(animate);
        }
    };

    const handleMouseMove = (event) => {
        // No aplicar parallax en responsive
        if (isMobile.value || !containerRef.value) {
            return;
        }

        const rect = containerRef.value.getBoundingClientRect();
        const relativeX = (event.clientX - (rect.left + rect.width / 2)) / (rect.width / 2);
        const relativeY = (event.clientY - (rect.top + rect.height / 2)) / (rect.height / 2);

        targetOffsetX.value = clamp(-relativeX * maxOffset, -maxOffset, maxOffset);
        targetOffsetY.value = clamp(-relativeY * maxOffset, -maxOffset, maxOffset);
        ensureAnimation();
    };

    const resetOffsets = () => {
        targetOffsetX.value = 0;
        targetOffsetY.value = 0;
        ensureAnimation();
    };

    onBeforeUnmount(() => {
        if (animationFrameId !== null) {
            cancelAnimationFrame(animationFrameId);
        }
        window.removeEventListener('resize', checkMobile);
    });

    return {
        containerRef,
        imageStyle,
        handleMouseMove,
        resetOffsets,
    };
}

