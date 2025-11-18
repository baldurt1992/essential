/**
 * Composable para generación automática de slugs
 */
export function useSlugGeneration() {
    const generateSlug = (text) => {
        if (!text) {
            return '';
        }

        return text
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .trim();
    };

    return {
        generateSlug,
    };
}

