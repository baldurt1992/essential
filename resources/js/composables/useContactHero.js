import { reactive, ref, computed, onMounted, onBeforeUnmount } from 'vue';

/**
 * Composable para manejar la animación interactiva del hero de contacto
 */
export function useContactHero() {
    const heroContainer = ref(null);

    const heroState = reactive({
        currentX: 0,
        currentY: 0,
        targetX: 0,
        targetY: 0,
    });

    const heroMaxOffset = 44;
    const heroEasing = 0.12;
    const isHeroInteractive = ref(false);
    let heroAnimationFrame = null;

    const heroImageStyle = computed(() => {
        if (!isHeroInteractive.value) {
            return {};
        }

        return {
            transform: `translate3d(${heroState.currentX}px, ${heroState.currentY}px, 0)`,
        };
    });

    function updateHeroInteractivity() {
        const shouldAnimate = window.matchMedia('(min-width: 1024px)').matches
            && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!shouldAnimate) {
            heroState.currentX = 0;
            heroState.currentY = 0;
            heroState.targetX = 0;
            heroState.targetY = 0;
        }

        isHeroInteractive.value = shouldAnimate;
    }

    function ensureHeroAnimation() {
        if (heroAnimationFrame) {
            return;
        }
        heroAnimationFrame = requestAnimationFrame(animateHeroFrame);
    }

    function animateHeroFrame() {
        const deltaX = heroState.targetX - heroState.currentX;
        const deltaY = heroState.targetY - heroState.currentY;

        heroState.currentX += deltaX * heroEasing;
        heroState.currentY += deltaY * heroEasing;

        if (Math.abs(deltaX) < 0.3 && Math.abs(deltaY) < 0.3) {
            heroState.currentX = heroState.targetX;
            heroState.currentY = heroState.targetY;
            heroAnimationFrame = null;
            return;
        }

        heroAnimationFrame = requestAnimationFrame(animateHeroFrame);
    }

    function handleHeroMouseMove(event) {
        if (!isHeroInteractive.value || !heroContainer.value) {
            return;
        }

        const rect = heroContainer.value.getBoundingClientRect();
        const centerX = rect.left + rect.width * 0.75;
        const centerY = rect.top + rect.height * 0.5;

        const relativeX = (event.clientX - centerX) / rect.width;
        const relativeY = (event.clientY - centerY) / rect.height;

        heroState.targetX = Math.max(Math.min(-relativeX * heroMaxOffset, heroMaxOffset), -heroMaxOffset);
        heroState.targetY = Math.max(Math.min(-relativeY * heroMaxOffset, heroMaxOffset), -heroMaxOffset);

        ensureHeroAnimation();
    }

    function resetHeroOffsets() {
        heroState.targetX = 0;
        heroState.targetY = 0;
        ensureHeroAnimation();
    }

    onMounted(() => {
        updateHeroInteractivity();
        window.addEventListener('resize', updateHeroInteractivity, { passive: true });
    });

    onBeforeUnmount(() => {
        window.removeEventListener('resize', updateHeroInteractivity);
        if (heroAnimationFrame) {
            cancelAnimationFrame(heroAnimationFrame);
            heroAnimationFrame = null;
        }
    });

    return {
        heroContainer,
        heroImageStyle,
        handleHeroMouseMove,
        resetHeroOffsets,
    };
}

