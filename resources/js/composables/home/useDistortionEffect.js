import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue';

/**
 * Composable para efecto de distorsión SVG en imágenes al hacer hover
 * @param {Array} items - Array de items que tendrán el efecto (debe tener length)
 * @param {Object} options - Opciones de configuración
 * @param {number} options.strength - Fuerza de la distorsión (default: 25)
 * @param {number} options.duration - Duración de la animación en ms (default: 600)
 * @param {string} options.filterPrefix - Prefijo para los IDs de filtros (default: 'filter-distort')
 * @param {string} options.distortPrefix - Prefijo para los data attributes (default: 'distort')
 * @returns {Object} - { startDistortion, stopDistortion }
 */
export function useDistortionEffect(items, options = {}) {
    const {
        strength = 25,
        duration = 600,
        filterPrefix = 'filter-distort',
        distortPrefix = 'distort',
    } = options;

    const animationFrames = ref({});
    const isAnimating = ref({});

    const startDistortion = (index) => {
        if (isAnimating.value[index]) return;

        const filter = document.querySelector(`#${filterPrefix}-${index}`);
        if (!filter) return;

        const displacementMap = filter.querySelector('feDisplacementMap');
        const colorMatrix = filter.querySelector('feColorMatrix');
        if (!displacementMap || !colorMatrix) return;

        isAnimating.value[index] = true;
        const startTime = Date.now();

        const img = document.querySelector(`[data-filter-id="${filterPrefix}-${index}"]`);
        if (img) {
            img.style.filter = `url(#${filterPrefix}-${index})`;
        }

        const animate = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);

            let scale;
            let hueRotate;

            if (progress < 0.25) {
                const phaseProgress = progress / 0.25;
                scale = strength * phaseProgress;
                hueRotate = 0;
            } else if (progress < 0.75) {
                scale = strength;
                const phaseProgress = (progress - 0.25) / 0.5;
                hueRotate = 360 * phaseProgress;
            } else {
                const phaseProgress = (progress - 0.75) / 0.25;
                scale = strength * (1 - phaseProgress);
                hueRotate = 360;
            }

            displacementMap.setAttribute('scale', scale);
            colorMatrix.setAttribute('values', hueRotate);

            if (progress < 1) {
                animationFrames.value[index] = requestAnimationFrame(animate);
            } else {
                if (img) {
                    img.style.filter = 'none';
                }
                displacementMap.setAttribute('scale', 0);
                colorMatrix.setAttribute('values', 0);
                isAnimating.value[index] = false;
            }
        };

        animationFrames.value[index] = requestAnimationFrame(animate);
    };

    const stopDistortion = (index) => {
        if (animationFrames.value[index]) {
            cancelAnimationFrame(animationFrames.value[index]);
            animationFrames.value[index] = null;
        }

        const filter = document.querySelector(`#${filterPrefix}-${index}`);
        if (filter) {
            const displacementMap = filter.querySelector('feDisplacementMap');
            const colorMatrix = filter.querySelector('feColorMatrix');
            if (displacementMap) {
                displacementMap.setAttribute('scale', 0);
            }
            if (colorMatrix) {
                colorMatrix.setAttribute('values', 0);
            }
        }

        const img = document.querySelector(`[data-filter-id="${filterPrefix}-${index}"]`);
        if (img) {
            img.style.filter = 'none';
        }

        isAnimating.value[index] = false;
    };

    const setupEventListeners = () => {
        // Obtener el array de items (puede ser un ref o un array directo)
        const itemsArray = items.value || items;

        // Calcular la posición de la primera imagen para que todas usen la misma posición
        // Usar el data-attribute que es más confiable que las clases
        const firstImage = document.querySelector(`[data-distort-id="${distortPrefix}-0"]`);
        let baseTop = 35; // Valor por defecto (igual que el CSS)

        if (firstImage) {
            // Buscar primero essential, luego qodef como fallback
            const firstArticle = firstImage.closest('.essential-grid-item') || firstImage.closest('.qodef-grid-item');

            if (firstArticle) {
                const rect = firstArticle.getBoundingClientRect();
                const gridInner = firstArticle.closest('.essential-grid-inner') || firstArticle.closest('.qodef-grid-inner');

                if (gridInner) {
                    const gridRect = gridInner.getBoundingClientRect();
                    const calculatedTop = rect.top - gridRect.top + (rect.height / 2);
                    // Solo usar el valor calculado si es razonable (entre 0 y 500px)
                    if (calculatedTop >= 0 && calculatedTop <= 500) {
                        baseTop = calculatedTop;
                    }
                }
            }

        }

        // Aplicar la misma posición a todas las imágenes
        itemsArray.forEach((service, index) => {
            const item = document.querySelector(`[data-distort-id="${distortPrefix}-${index}"]`);
            if (item) {
                item.style.top = `${baseTop}px`;
                item.style.transform = 'translateY(-50%)';

                // Buscar primero essential, luego qodef como fallback
                const article = item.closest('.essential-e') || item.closest('.qodef-e');

                if (article) {
                    article.addEventListener('mouseenter', () => startDistortion(index));
                    article.addEventListener('mouseleave', () => stopDistortion(index));
                }
            }
        });
    };

    onMounted(() => {
        // Esperar a que el DOM esté completamente renderizado
        nextTick(() => {
            // Pequeño delay adicional para asegurar que los elementos estén en su posición final
            setTimeout(() => {
                setupEventListeners();
            }, 100);
        });
    });

    onBeforeUnmount(() => {
        Object.keys(animationFrames.value).forEach(index => {
            if (animationFrames.value[index]) {
                cancelAnimationFrame(animationFrames.value[index]);
            }
        });
    });

    return {
        startDistortion,
        stopDistortion,
    };
}


