import { computed, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useSiteTemplates } from './useSiteTemplates';

/**
 * Composable para sincronizar los filtros de plantillas con la URL
 */
export function useTemplateRouteSync() {
    const route = useRoute();
    const router = useRouter();
    const siteTemplates = useSiteTemplates();

    const filters = computed(() => siteTemplates.filters.value);
    const pagination = computed(() => siteTemplates.pagination.value);

    const lastSyncedQueryKey = ref('');
    const hasSyncedOnce = ref(false);

    const buildRouteQuery = (overrides = {}) => {
        const currentPage = overrides.page ?? pagination.value.currentPage ?? 1;
        const category = Object.prototype.hasOwnProperty.call(overrides, 'category')
            ? overrides.category
            : filters.value.category;
        const popular = Object.prototype.hasOwnProperty.call(overrides, 'popular')
            ? overrides.popular
            : filters.value.popular;
        const fresh = Object.prototype.hasOwnProperty.call(overrides, 'fresh')
            ? overrides.fresh
            : filters.value.fresh;

        const query = {};
        if (category) {
            query.category = category;
        }
        if (popular) {
            query.popular = '1';
        }
        if (fresh) {
            query.fresh = '1';
        }
        if (currentPage && currentPage !== 1) {
            query.page = String(currentPage);
        }

        return query;
    };

    const updateRoute = (overrides = {}) => {
        router.replace({ name: 'templates', query: buildRouteQuery(overrides) }).catch(() => { });
    };

    const buildCurrentQueryKey = () => {
        const routeQuery = route.query;
        const page = parseInt(routeQuery.page ?? '1', 10);
        const category = routeQuery.category ?? null;
        const popular = routeQuery.popular === '1';
        const fresh = routeQuery.fresh === '1';

        return JSON.stringify({
            page: Number.isNaN(page) ? 1 : page,
            category,
            popular,
            fresh,
        });
    };

    const syncFromRoute = async () => {
        const queryKey = buildCurrentQueryKey();

        if (hasSyncedOnce.value && lastSyncedQueryKey.value === queryKey) {
            return;
        }

        const payload = JSON.parse(queryKey);

        await siteTemplates.fetchTemplates({
            ...payload,
            force: true,
        });

        hasSyncedOnce.value = true;
        lastSyncedQueryKey.value = queryKey;
    };

    watch(
        () => route.query,
        async () => {
            const queryKey = buildCurrentQueryKey();
            if (hasSyncedOnce.value && lastSyncedQueryKey.value === queryKey) {
                return;
            }
            await syncFromRoute();
        },
        { immediate: true }
    );

    return {
        updateRoute,
        syncFromRoute,
    };
}

