/**
 * Composable para formateo de datos en el panel admin
 */
export function useAdminFormatting() {
    const formatPrice = (price, currency = 'EUR') => {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: currency?.toUpperCase() ?? 'EUR',
            minimumFractionDigits: 2,
        }).format(price ?? 0);
    };

    const formatUpdatedAt = (timestamp) => {
        if (!timestamp) {
            return '—';
        }

        const date = new Date(timestamp);
        return new Intl.DateTimeFormat('es-ES', {
            dateStyle: 'medium',
            timeStyle: 'short',
        }).format(date);
    };

    const formatInterval = (interval, count = 1) => {
        const intervalMap = {
            day: count === 1 ? 'Diario' : `Cada ${count} días`,
            week: count === 1 ? 'Semanal' : `Cada ${count} semanas`,
            month: count === 1 ? 'Mensual' : `Cada ${count} meses`,
            year: count === 1 ? 'Anual' : `Cada ${count} años`,
        };

        return intervalMap[interval] ?? `${interval} (${count})`;
    };

    return {
        formatPrice,
        formatUpdatedAt,
        formatInterval,
    };
}

