import { computed, ref } from 'vue';
import { useSiteServices } from './useSiteServices';

/**
 * Composable para manejar la lógica de la página de servicios
 * @returns {Object} - { services, isLoading, hasError, fetchServices, selectFilter, retryFetch, serviceTarget, formatIndex }
 */
export function useServicesPage() {
    const servicesStore = useSiteServices();

    const activeFilter = ref('all');

    const services = computed(() => servicesStore.services.value);
    const isLoading = computed(() => servicesStore.isLoading.value);
    const hasError = computed(() => !!servicesStore.error.value);

    const fetchServices = async (filter) => {
        const popular = filter === 'popular';
        await servicesStore.fetchServices({ popular });
    };

    const selectFilter = async (filter) => {
        if (filter === activeFilter.value) {
            return;
        }

        activeFilter.value = filter;
        await fetchServices(filter);
    };

    const retryFetch = async () => {
        await fetchServices(activeFilter.value);
    };

    const serviceTarget = (service) => {
        const resolved = service.linkUrl ?? `/servicios/${service.slug}`;

        try {
            const resolvedUrl = new URL(resolved, window.location.origin);
            if (resolvedUrl.origin === window.location.origin) {
                return resolvedUrl.pathname + resolvedUrl.search + resolvedUrl.hash;
            }
        } catch (error) {
            console.warn('[ServicesPage] Invalid service link. Falling back to slug.', error);
        }

        return resolved.startsWith('http') ? resolved : `/servicios/${service.slug}`;
    };

    const formatIndex = (index) => String(index + 1).padStart(2, '0');

    return {
        services,
        isLoading,
        hasError,
        activeFilter,
        fetchServices,
        selectFilter,
        retryFetch,
        serviceTarget,
        formatIndex,
    };
}

