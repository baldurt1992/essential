import { reactive, computed } from 'vue';
import axios from 'axios';

const state = reactive({
    loading: false,
    loaded: false,
    contact: null,
    error: null,
});

const ensureNormalizedLinks = (links = []) =>
    Array.isArray(links)
        ? links
            .filter((link) => link?.network && link?.url)
            .map((link) => ({
                network: link.network,
                url: link.url,
            }))
        : [];

export function useSiteContactInformation() {
    const fetchContactInformation = async () => {
        if (state.loaded || state.loading) {
            return;
        }

        state.loading = true;
        state.error = null;

        try {
            const response = await axios.get('/api/contact-information');
            const payload = response.data?.data ?? {};

            state.contact = {
                ...payload,
                social_links: ensureNormalizedLinks(payload.social_links),
            };
            state.loaded = true;
        } catch (error) {
            state.error =
                error.response?.data ?? {
                    message: 'No logramos cargar los datos de contacto.',
                };
        } finally {
            state.loading = false;
        }
    };

    const contact = computed(() => state.contact ?? {});
    const socialLinks = computed(() => ensureNormalizedLinks(contact.value?.social_links));

    return {
        state,
        contact,
        socialLinks,
        isLoading: computed(() => state.loading && !state.loaded),
        fetchContactInformation,
    };
}

