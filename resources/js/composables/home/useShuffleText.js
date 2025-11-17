import { ref, onBeforeUnmount } from 'vue';

/**
 * Composable para animación de texto shuffle (efecto de revelado con caracteres aleatorios)
 * @param {string} initialText - Texto inicial a mostrar
 * @param {Object} options - Opciones de configuración
 * @param {number} options.duration - Duración de la animación en ms (default: 500)
 * @param {number} options.delay - Delay inicial en ms (default: 100)
 * @returns {Object} - { displayText, buttonTextRef, shuffleText, resetText }
 */
export function useShuffleText(initialText, options = {}) {
    const { duration = 500, delay = 100 } = options;

    const buttonTextRef = ref(null);
    const displayText = ref(initialText);
    const originalText = initialText;
    const isShuffling = ref(false);
    let animationFrameId = null;

    const getRandomChar = () => {
        const possible = "!\"#$%&'()*+,-./:;<=>?@[\\]^_`{|}~" +
            "0123456789" +
            "ABCDEFGHIJKLMNOPQRSTUVWXYZ" +
            "abcdefghijklmnopqrstuvwxyz";
        return possible.charAt(Math.floor(Math.random() * possible.length));
    };

    const mask = (chars, progress) => {
        const masked = [];
        for (let i = 0; i < chars.length; i++) {
            const position = (i + 1) / chars.length;
            if (position > progress) {
                masked.push(getRandomChar());
            } else {
                masked.push(chars[i]);
            }
        }
        return masked.join('');
    };

    const shuffleText = () => {
        if (isShuffling.value || !buttonTextRef.value) return;

        // Cancelar cualquier animación anterior
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }

        isShuffling.value = true;
        const chars = originalText.trim().split('');
        const startTime = Date.now() + delay;

        const animate = () => {
            const elapsed = Math.max(0, Date.now() - startTime);
            const progress = Math.min(elapsed / duration, 1);

            // Easing: easeInQuad (progress^2)
            const easedProgress = progress * progress;

            displayText.value = mask(chars, easedProgress);

            if (progress < 1) {
                animationFrameId = requestAnimationFrame(animate);
            } else {
                displayText.value = originalText;
                isShuffling.value = false;
                animationFrameId = null;
            }
        };

        // Delay inicial
        setTimeout(() => {
            if (isShuffling.value) {
                animationFrameId = requestAnimationFrame(animate);
            }
        }, delay);
    };

    const resetText = () => {
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }
        if (isShuffling.value) {
            displayText.value = originalText;
            isShuffling.value = false;
        }
    };

    onBeforeUnmount(() => {
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }
    });

    return {
        displayText,
        buttonTextRef,
        shuffleText,
        resetText,
    };
}

