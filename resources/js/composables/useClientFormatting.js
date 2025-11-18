/**
 * Composable para formateo de datos en el client panel
 */
export function useClientFormatting() {
    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    const formatPrice = (price, currency = 'EUR') => {
        if (!price) return 'N/A';
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: currency.toUpperCase(),
        }).format(price);
    };

    return {
        formatDate,
        formatPrice,
    };
}

