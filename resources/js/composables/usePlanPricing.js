import { computed } from 'vue';

/**
 * Composable para manejar el formateo de precios e intervalos de planes
 */
export function usePlanPricing() {
    const intervalDictionary = {
        day: ['día', 'días'],
        week: ['semana', 'semanas'],
        month: ['mes', 'meses'],
        year: ['año', 'años'],
    };

    const formatInterval = (plan) => {
        const interval = plan.billingInterval ?? 'month';
        const count = plan.billingIntervalCount ?? 1;
        const [singular, plural] = intervalDictionary[interval] ?? intervalDictionary.month;

        if (count === 1) {
            if (interval === 'month') return 'al mes';
            if (interval === 'year') return 'al año';
            return `por ${singular}`;
        }

        return `cada ${count} ${count === 1 ? singular : plural}`;
    };

    const formatPrice = (plan) => {
        const currency = (plan.currency ?? 'EUR').toUpperCase();
        const amount = Number(plan.price ?? 0);
        const formatter = new Intl.NumberFormat('es', {
            style: 'currency',
            currency,
            minimumFractionDigits: amount % 1 === 0 ? 0 : 2,
            maximumFractionDigits: 2,
        });
        return formatter.format(amount);
    };

    const formatDate = (dateString) => {
        if (!dateString) {
            return '';
        }
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    const getAnnualPrice = (plan) => {
        const price = plan.price || 0;
        const interval = plan.billing_interval || 'month';
        const count = plan.billing_interval_count || 1;

        if (interval === 'year') {
            return price / count; // Price per year
        } else if (interval === 'month') {
            return (price / count) * 12; // Convert to annual
        } else if (interval === 'week') {
            return (price / count) * 52; // Convert to annual
        } else if (interval === 'day') {
            return (price / count) * 365; // Convert to annual
        }
        return price;
    };

    return {
        formatInterval,
        formatPrice,
        formatDate,
        getAnnualPrice,
    };
}

